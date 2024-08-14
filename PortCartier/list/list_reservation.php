<?php
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION['employee_id'])) {
    header("Location: /PortCartier/login.php");
    exit();
}

// Inclure les fichiers nécessaires
include("../includes/nav.php");
include("../database/db_connection.php");

// Initialisation des variables
$reservations = [];
$error_message = "";
$result_all = null; // Initialiser la variable pour éviter les erreurs

// Traitement de l'annulation de réservation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_reservation_id'])) {
    $cancel_reservation_id = $_POST['cancel_reservation_id'];

    // Début de la transaction pour assurer l'intégrité des données
    $conn->begin_transaction();

    // Récupérer les détails de la réservation
    $query_reservation_details = "
        SELECT r.reservation_id, r.document_id, r.reserved_quantity, i.available_quantity 
        FROM reservations r
        INNER JOIN inventory i ON r.document_id = i.document_id
        WHERE r.reservation_id = ? FOR UPDATE";

    $stmt_reservation_details = $conn->prepare($query_reservation_details);
    $stmt_reservation_details->bind_param('i', $cancel_reservation_id);
    $stmt_reservation_details->execute();
    $result_reservation_details = $stmt_reservation_details->get_result();

    if ($result_reservation_details->num_rows > 0) {
        $row = $result_reservation_details->fetch_assoc();
        $document_id = $row['document_id'];
        $reserved_quantity = $row['reserved_quantity'];
        $available_quantity = $row['available_quantity'];

        // Mettre à jour la réservation pour la marquer comme annulée
        $query_cancel = "UPDATE reservations SET in_progress = 0 WHERE reservation_id = ?";
        $stmt_cancel = $conn->prepare($query_cancel);
        $stmt_cancel->bind_param('i', $cancel_reservation_id);

        // Mettre à jour l'inventaire
        $query_update_inventory = "UPDATE inventory SET available_quantity = available_quantity + ?, reserved_quantity = reserved_quantity - ? WHERE document_id = ?";
        $stmt_update_inventory = $conn->prepare($query_update_inventory);
        $stmt_update_inventory->bind_param('iii', $reserved_quantity, $reserved_quantity, $document_id);

        // Exécuter les requêtes dans la transaction
        if ($stmt_cancel->execute() && $stmt_update_inventory->execute()) {
            $conn->commit();
            $error_message = "Réservation annulée avec succès. Stock d'inventaire mis à jour.";
        } else {
            $conn->rollback();
            $error_message = "Erreur lors de l'annulation de la réservation ou mise à jour de l'inventaire.";
        }
    } else {
        $error_message = "Réservation introuvable.";
    }

    // Fermer les requêtes préparées
    $stmt_reservation_details->close();
    $stmt_cancel->close();
    $stmt_update_inventory->close();
}

// Traitement du formulaire de recherche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_term'])) {
    $search_term = $_POST['search_term'];

    // Vérifier si le champ de recherche est vide
    if (empty($search_term)) {
        $error_message = "Aucun terme de recherche spécifié.";
    } else {
        $search_query = "%{$search_term}%";

        // Requête SQL pour rechercher les réservations actives selon le nom du membre ou le titre du document
        $query_search = "
            SELECT reservations.*, members.first_name, members.last_name, documents.title 
            FROM reservations
            JOIN members ON reservations.member_id = members.member_id
            JOIN documents ON reservations.document_id = documents.document_id
            WHERE (members.first_name LIKE ? OR members.last_name LIKE ? OR documents.title LIKE ?)
            AND reservations.in_progress = TRUE";

        $stmt_search = $conn->prepare($query_search);
        $stmt_search->bind_param('sss', $search_query, $search_query, $search_query);
        $stmt_search->execute();
        $result_search = $stmt_search->get_result();

        // Vérifier s'il y a des résultats
        if ($result_search->num_rows > 0) {
            $reservations = $result_search->fetch_all(MYSQLI_ASSOC);
        } else {
            $error_message = "Aucune réservation trouvée avec ce critère de recherche.";
        }
    }
} else {
    // Requête SQL pour récupérer toutes les réservations actives
    $query_all = "
        SELECT reservations.*, members.first_name, members.last_name, documents.title 
        FROM reservations
        JOIN members ON reservations.member_id = members.member_id
        JOIN documents ON reservations.document_id = documents.document_id
        WHERE reservations.in_progress = TRUE";

    $result_all = $conn->query($query_all);

    // Vérifier s'il y a des résultats
    if ($result_all->num_rows > 0) {
        $reservations = $result_all->fetch_all(MYSQLI_ASSOC);
    } else {
        $error_message = "Aucune réservation active trouvée.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Réservations Actives</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading w-100 text-center">
            <h4>Liste des réservations actives</h4>
        </div>
        <div class="panel-body">

            <!-- Formulaire de recherche de réservation -->
            <form action="list_reservation.php" method="POST" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Rechercher par nom de membre ou titre de document..." name="search_term">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                    </div>
                </div>
            </form>

            <!-- Affichage des résultats de recherche -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-info" role="alert">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Affichage des réservations actives -->
            <?php if (count($reservations) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Réservation</th>
                            <th>Nom du Membre</th>
                            <th>Titre du Document</th>
                            <th>Date de Réservation</th>
                            <th>Quantité Réservée</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['reservation_id']); ?></td>
                                <td><?= htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></td>
                                <td><?= htmlspecialchars($reservation['title']); ?></td>
                                <td><?= htmlspecialchars($reservation['reservation_date']); ?></td>
                                <td><?= htmlspecialchars($reservation['reserved_quantity']); ?></td>
                                <td>
                                    <form action="list_reservation.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
                                        <input type="hidden" name="cancel_reservation_id" value="<?= htmlspecialchars($reservation['reservation_id']); ?>">
                                        <button type="submit" class="btn btn-danger">Annuler</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info" role="alert">Aucune réservation active trouvée.</div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Fermer la connexion et libérer les ressources
if ($result_all) {
    $result_all->free();
}
if (isset($stmt_search)) {
    $stmt_search->close();
}
if (isset($stmt_cancel)) {
    $stmt_cancel->close();
}
$conn->close();
?>
