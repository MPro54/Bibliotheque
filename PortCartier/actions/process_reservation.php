<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'employé
if (!isset($_SESSION['employee_id'])) {
    header("Location: /PortCartier/login.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
include("../database/db_connection.php");

// Vérifier si les données ont été soumises via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $member_id = $_POST['member_id'];
    $document_id = $_POST['document_id'];
    $quantity = $_POST['quantity'];

    // Validation des données
    if (empty($member_id) || empty($document_id) || empty($quantity)) {
        $_SESSION['reserve_error'] = "Veuillez remplir tous les champs obligatoires.";
        header("Location: ../views/form_reservation.php");
        exit();
    }

    // Vérifier si la quantité demandée est disponible dans l'inventaire
    $query_check_quantity = "SELECT quantity FROM inventory WHERE document_id = ?";
    $stmt_check_quantity = $conn->prepare($query_check_quantity);
    $stmt_check_quantity->bind_param("i", $document_id);
    $stmt_check_quantity->execute();
    $result_check_quantity = $stmt_check_quantity->get_result();

    if ($result_check_quantity->num_rows > 0) {
        $row = $result_check_quantity->fetch_assoc();
        $available_quantity = $row['quantity'];

        if ($quantity > $available_quantity) {
            $_SESSION['reserve_error'] = "La quantité demandée n'est pas disponible.";
            header("Location: ../views/form_reservation.php");
            exit();
        }
    } else {
        $_SESSION['reserve_error'] = "Document non trouvé dans l'inventaire.";
        header("Location: ../views/form_reservation.php");
        exit();
    }
    
    // Déterminer la date de réservation (date actuelle)
    $reservation_date = date('Y-m-d');

    // Préparer la requête d'insertion pour enregistrer la réservation
    $query_insert_reservation = "INSERT INTO reservations (member_id, document_id, reservation_date, reserved_quantity) VALUES (?, ?, ?, ?)";
    $stmt_insert_reservation = $conn->prepare($query_insert_reservation);
    $stmt_insert_reservation->bind_param("iisi", $member_id, $document_id, $reservation_date, $quantity);

    // Exécuter la requête d'insertion pour enregistrer la réservation
if ($stmt_insert_reservation->execute()) {
    // Mettre à jour la quantité réservée dans l'inventaire
    $query_update_inventory = "UPDATE inventory 
                               SET reserved_quantity = IFNULL(reserved_quantity, 0) + ?, 
                                   available_quantity = IFNULL(available_quantity, 0) - ?
                               WHERE document_id = ?";
    $stmt_update_inventory = $conn->prepare($query_update_inventory);
    
    // Calculer la quantité négative pour déduire de available_quantity
    $negative_quantity = -1 * $quantity;
    
    $stmt_update_inventory->bind_param("iii", $quantity, $quantity, $document_id);
    $stmt_update_inventory->execute();

    // Rediriger avec un paramètre de succès si nécessaire
    $_SESSION['reserve_success'] = "La réservation a été effectuée avec succès!";
    header("Location: ../views/form_reservation.php");
    exit();
} else {
    // Gérer l'erreur : échec de l'insertion
    $_SESSION['reserve_error'] = "Erreur lors de l'enregistrement de la réservation : " . $stmt_insert_reservation->error;
    header("Location: ../views/form_reservation.php");
    exit();
}

}

// Fermer les déclarations préparées
$stmt_check_quantity->close();
$stmt_insert_reservation->close();
$stmt_update_inventory->close();

// Fermer la connexion à la base de données
$conn->close();
