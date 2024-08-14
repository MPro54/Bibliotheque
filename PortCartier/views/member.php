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
$selected_member = null;
$loans = [];
$reservations = [];

// Traitement de la recherche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_term'])) {
    $search_term = $_POST['search_term'];

    // Vérifier si le champ de recherche est vide
    if (empty($search_term)) {
        $_SESSION['error_message'] = "Aucun terme de recherche spécifié.";
        header("Location: member.php");
        exit();
    } else {
        $search_query = "%{$search_term}%";

        // Requête SQL pour rechercher le membre par nom, code ou téléphone
        $query_search = "SELECT * FROM members 
                         WHERE first_name LIKE ? 
                            OR last_name LIKE ? 
                            OR phone LIKE ?";

        $stmt_search = $conn->prepare($query_search);
        $stmt_search->bind_param('sss', $search_query, $search_query, $search_query);
        $stmt_search->execute();
        $result_search = $stmt_search->get_result();

        // Vérifier s'il y a des résultats
        if ($result_search->num_rows > 0) {
            // Afficher les résultats de la recherche
            $selected_member = $result_search->fetch_assoc();
            $member_id = $selected_member['member_id']; // Récupérer l'ID du membre sélectionné

            // Récupérer les emprunts actifs du membre
            $query_loans = "SELECT * FROM loans WHERE member_id = ? AND return_date IS NULL";
            $stmt_loans = $conn->prepare($query_loans);
            $stmt_loans->bind_param('i', $member_id);
            $stmt_loans->execute();
            $result_loans = $stmt_loans->get_result();
            $loans = $result_loans->fetch_all(MYSQLI_ASSOC);

            // Récupérer les réservations actives du membre
            $query_reservations = "SELECT * FROM reservations WHERE member_id = ? AND in_progress = TRUE";
            $stmt_reservations = $conn->prepare($query_reservations);
            $stmt_reservations->bind_param('i', $member_id);
            $stmt_reservations->execute();
            $result_reservations = $stmt_reservations->get_result();
            $reservations = $result_reservations->fetch_all(MYSQLI_ASSOC);
        } else {
            $_SESSION['error_message'] = "Aucun membre trouvé avec ce critère de recherche.";
            header("Location: member.php");
            exit();
        }
    }
}

// Récupérer la liste complète des membres pour le dropdown
$query_members = "SELECT member_id, first_name, last_name FROM members";
$result_members = $conn->query($query_members);

// Vérifier si la requête a réussi
if (!$result_members) {
    die("Erreur lors de la récupération des membres: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Membre</title>
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/style.css">
</head>
<body>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading w-100 text-center">
            <h4>Relevé du membre</h4>
        </div>
        <div class="panel-body">

            <!-- Affichage des messages d'erreur -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($_SESSION['error_message']); ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Formulaire de recherche de membre -->
            <form action="member.php" method="POST" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Rechercher par nom, code ou téléphone..." name="search_term">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                    </div>
                </div>
            </form>

            <!-- Affichage des résultats de recherche ou des détails du membre -->
            <?php if ($selected_member): ?>
                <hr>
                <!-- Affichage des informations du membre -->
                <h5>Informations personnelles :</h5>
                <div>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($selected_member['first_name'] . ' ' . $selected_member['last_name']) ?></p>
                    <p><strong>Adresse :</strong> <?= htmlspecialchars($selected_member['address'] . ', ' . $selected_member['city'] . ', ' . $selected_member['province']) ?></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($selected_member['phone']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($selected_member['email']) ?></p>
                </div>

                <hr>

                <!-- Affichage des emprunts actifs du membre -->
                <h5>Emprunts actifs :</h5>
                <?php if (count($loans) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Emprunt</th>
                                <th>Date d'emprunt</th>
                                <th>Date de retour prévue</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($loan['loan_id']); ?></td>
                                    <td><?= htmlspecialchars($loan['loan_date']); ?></td>
                                    <td><?= htmlspecialchars($loan['due_date']); ?></td>
                                    <td><?= htmlspecialchars($loan['quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">Aucun emprunt actif pour ce membre.</div>
                <?php endif; ?>

                <hr>

                <!-- Affichage des réservations actives du membre -->
                <h5>Réservations actives :</h5>
                <?php if (count($reservations) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Réservation</th>
                                <th>Date de Réservation</th>
                                <th>Quantité Réservée</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reservation['reservation_id']); ?></td>
                                    <td><?= htmlspecialchars($reservation['reservation_date']); ?></td>
                                    <td><?= htmlspecialchars($reservation['reserved_quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">Aucune réservation active pour ce membre.</div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>


</body>
<script src="/PortCartier/js/jquery-3.3.1.js"></script>
<script src="/PortCartier/js/bootstrap.min.js"></script>
</html>

<?php
// Fermer la connexion et libérer les ressources
$result_members->free();
if (isset($stmt_search)) {
    $stmt_search->close();
}
$conn->close();
?>
