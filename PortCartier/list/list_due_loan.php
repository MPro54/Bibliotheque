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
$error_message = "";
$overdue_loans = [];
$date_check = date("Y-m-d");
$result_all = null; // Initialiser la variable pour éviter les erreur

// Traitement du formulaire de recherche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date_check'])) {
    $date_check = $_POST['date_check'];
}

// Requête SQL pour récupérer les prêts en retard
$query_overdue = "SELECT loans.loan_id, loans.loan_date, loans.due_date, loans.quantity, 
                         members.first_name, members.last_name, documents.title 
                  FROM loans
                  JOIN members ON loans.member_id = members.member_id
                  JOIN documents ON loans.document_id = documents.document_id
                  WHERE loans.return_date IS NULL AND loans.due_date < ?";
$stmt_overdue = $conn->prepare($query_overdue);
$stmt_overdue->bind_param('s', $date_check);
$stmt_overdue->execute();
$result_overdue = $stmt_overdue->get_result();
$overdue_loans = $result_overdue->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prêts en Retard</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading w-100 text-center">
            <h4>Liste des prêts en retard</h4>
        </div>
        <div class="panel-body">

            <!-- Formulaire de recherche de prêts en retard -->
            <form action="list_due_loan.php" method="POST" class="mb-3">
                <div class="input-group">
                    <input type="date" class="form-control" name="date_check" value="<?= htmlspecialchars($date_check) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                    </div>
                </div>
            </form>

            <!-- Affichage des prêts en retard -->
            <?php if (!empty($overdue_loans)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Prêt</th>
                            <th>Nom du Membre</th>
                            <th>Titre du Document</th>
                            <th>Date d'Emprunt</th>
                            <th>Date de Retour Prévue</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdue_loans as $loan): ?>
                            <tr>
                                <td><?= htmlspecialchars($loan['loan_id']); ?></td>
                                <td><?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']); ?></td>
                                <td><?= htmlspecialchars($loan['title']); ?></td>
                                <td><?= htmlspecialchars($loan['loan_date']); ?></td>
                                <td><?= htmlspecialchars($loan['due_date']); ?></td>
                                <td><?= htmlspecialchars($loan['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info" role="alert">Aucun prêt en retard trouvé pour la date spécifiée.</div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="/PortCartier/js/jquery-3.3.1.js"></script>
<script src="/PortCartier/js/bootstrap.min.js"></script>

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