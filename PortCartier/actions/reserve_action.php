<?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion de la connexion à la base de données
include($_SERVER['DOCUMENT_ROOT'] . "/PortCartier/database/db_connection.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: /PortCartier/login.php");
    exit();
}

// Vérifier si le formulaire de réservation est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $document_id = $_POST['document_id'];
    $quantity = $_POST['quantity'];

    // Vérifier si la quantité est valide
    if ($quantity < 1) {
        $_SESSION['reserve_error'] = "La quantité de réservation doit être d'au moins 1.";
        header("Location: /PortCartier/views/documents.php");
        exit();
    }

    // Vérifier l'inventaire disponible
    $sql_check_inventory = "SELECT IFNULL(inv.quantity, 0) - IFNULL(inv.reserved_quantity, 0) AS available_quantity
                            FROM inventory inv
                            WHERE inv.document_id = ?";
    
    $stmt_check_inventory = $conn->prepare($sql_check_inventory);
    if (!$stmt_check_inventory) {
        $_SESSION['reserve_error'] = "Erreur lors de la préparation de la requête de vérification de l'inventaire : " . $conn->error;
        header("Location: /PortCartier/views/documents.php");
        exit();
    }

    $stmt_check_inventory->bind_param("i", $document_id);
    $stmt_check_inventory->execute();
    $result_check_inventory = $stmt_check_inventory->get_result();
    $row_check_inventory = $result_check_inventory->fetch_assoc();

    if ($row_check_inventory['available_quantity'] < $quantity) {
        $_SESSION['reserve_error'] = "L'inventaire est insuffisant pour la quantité demandée.";
        header("Location: /PortCartier/views/documents.php");
        exit();
    }

    // Préparation de la requête SQL pour insérer la réservation
    $sql_insert_reservation = "INSERT INTO reservations (member_id, document_id, reservation_date, in_progress, reserved_quantity)
                               VALUES (?, ?, NOW(), 1, ?)";
    
    $stmt_insert_reservation = $conn->prepare($sql_insert_reservation);
    if (!$stmt_insert_reservation) {
        $_SESSION['reserve_error'] = "Erreur lors de la préparation de la requête d'insertion de réservation : " . $conn->error;
        header("Location: /PortCartier/views/documents.php");
        exit();
    }

    $member_id = $_SESSION['user_id']; // Assurez-vous que user_id est correctement initialisé

    $stmt_insert_reservation->bind_param("iii", $member_id, $document_id, $quantity);

    // Exécuter l'insertion de la réservation et la mise à jour de l'inventaire
    $conn->begin_transaction();
    try {
        $stmt_insert_reservation->execute();

        // Mettre à jour l'inventaire
        $sql_update_inventory = "UPDATE inventory 
                                 SET reserved_quantity = reserved_quantity + ? 
                                 WHERE document_id = ?";
        
        $stmt_update_inventory = $conn->prepare($sql_update_inventory);
        if (!$stmt_update_inventory) {
            throw new Exception("Erreur lors de la préparation de la requête de mise à jour de l'inventaire : " . $conn->error);
        }

        $stmt_update_inventory->bind_param("ii", $quantity, $document_id);
        $stmt_update_inventory->execute();

        $conn->commit();
        $_SESSION['reserve_success'] = "La réservation a été effectuée avec succès.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['reserve_error'] = "Une erreur s'est produite lors de la réservation. Veuillez réessayer.";
    }

    // Fermer les requêtes et la connexion
    $stmt_check_inventory->close();
    $stmt_insert_reservation->close();
    if (isset($stmt_update_inventory)) {
        $stmt_update_inventory->close();
    }
    $conn->close();

    // Redirection vers la page des documents
    header("Location: /PortCartier/views/documents.php");
    exit();
} else {
    // Redirection si le formulaire n'est pas soumis correctement
    header("Location: /PortCartier/views/documents.php");
    exit();
}
