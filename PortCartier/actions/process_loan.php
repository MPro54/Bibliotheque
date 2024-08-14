<?php
session_start();

// Inclure les fichiers nécessaires
include("../database/db_connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = $_POST['member_id'];
    $document_id = $_POST['document_id'];
    $quantity = $_POST['quantity'];
    $loan_date = $_POST['loan_date'];
    $due_date = $_POST['return_date'];

    // Début de la transaction
    $conn->begin_transaction();

    try {
        // Vérifier si le membre a des réservations actives pour ce document
        $query_reservation = "SELECT * FROM reservations WHERE member_id = ? AND document_id = ? AND in_progress = TRUE";
        $stmt_reservation = $conn->prepare($query_reservation);
        if (!$stmt_reservation) {
            throw new Exception("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt_reservation->bind_param('ii', $member_id, $document_id);
        $stmt_reservation->execute();
        $result_reservation = $stmt_reservation->get_result();

        $reserved_quantity = 0;
        if ($result_reservation->num_rows > 0) {
            $reservation = $result_reservation->fetch_assoc();
            $reserved_quantity = $reservation['reserved_quantity'];

            // Annuler la réservation en mettant à jour in_progress à FALSE
            $update_reservation = "UPDATE reservations SET in_progress = FALSE WHERE reservation_id = ?";
            $stmt_update_reservation = $conn->prepare($update_reservation);
            if (!$stmt_update_reservation) {
                throw new Exception("Erreur de préparation de la requête : " . $conn->error);
            }
            $stmt_update_reservation->bind_param('i', $reservation['reservation_id']);
            $stmt_update_reservation->execute();
        }

        // Quantité totale à prendre en compte
        $total_needed_quantity = $quantity;
        // Quantité restant à prendre dans le stock disponible après prise en compte de la réservation
        $needed_from_available = $quantity - $reserved_quantity;

        // Vérifier la quantité disponible dans l'inventaire
        $query_inventory = "SELECT * FROM inventory WHERE document_id = ?";
        $stmt_inventory = $conn->prepare($query_inventory);
        if (!$stmt_inventory) {
            throw new Exception("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt_inventory->bind_param('i', $document_id);
        $stmt_inventory->execute();
        $result_inventory = $stmt_inventory->get_result();

        if ($result_inventory->num_rows > 0) {
            $inventory = $result_inventory->fetch_assoc();
            $available_quantity = $inventory['available_quantity'];

            // Débogage : affichage des valeurs actuelles
            error_log("Debug: reserved_quantity = $reserved_quantity");
            error_log("Debug: total_needed_quantity = $total_needed_quantity");
            error_log("Debug: needed_from_available = $needed_from_available");
            error_log("Debug: available_quantity = $available_quantity");

            if ($needed_from_available <= $available_quantity) {
                // Mettre à jour la quantité disponible et réservée
                $new_reserved_quantity = $inventory['reserved_quantity'] - $reserved_quantity;
                $new_available_quantity = $available_quantity - $needed_from_available;

                $update_inventory = "UPDATE inventory SET reserved_quantity = ?, available_quantity = ? WHERE document_id = ?";
                $stmt_update_inventory = $conn->prepare($update_inventory);
                if (!$stmt_update_inventory) {
                    throw new Exception("Erreur de préparation de la requête : " . $conn->error);
                }
                $stmt_update_inventory->bind_param('iii', $new_reserved_quantity, $new_available_quantity, $document_id);
                $stmt_update_inventory->execute();

                // Insérer le prêt dans la base de données
                $insert_loan = "INSERT INTO loans (member_id, document_id, quantity, loan_date, due_date) VALUES (?, ?, ?, ?, ?)";
                $stmt_loan = $conn->prepare($insert_loan);
                if (!$stmt_loan) {
                    throw new Exception("Erreur de préparation de la requête : " . $conn->error);
                }
                $stmt_loan->bind_param('iiiss', $member_id, $document_id, $quantity, $loan_date, $due_date);
                if ($stmt_loan->execute()) {
                    $_SESSION['loan_success'] = "Prêt enregistré avec succès.";
                } else {
                    throw new Exception("Erreur lors de l'enregistrement du prêt.");
                }
            } else {
                throw new Exception("Quantité insuffisante dans l'inventaire.");
            }
        } else {
            throw new Exception("Document non trouvé dans l'inventaire.");
        }

        // Commit transaction
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['loan_error'] = $e->getMessage();
    }

    header("Location: ../views/form_loan.php"); // Redirection vers le formulaire de prêt
    exit();
}
?>
