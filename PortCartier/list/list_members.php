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

// Requête pour récupérer la liste des membres
$query_members = "SELECT * FROM members";
$result_members = $conn->query($query_members);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Membres</title>
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/style.css">
</head>
<body>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading w-100 text-center"><h4>Liste des membres</h4></div>
        <div class="panel-body">
            <?php
            if ($result_members && $result_members->num_rows > 0) {
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>ID</th><th>Prénom</th><th>Nom</th><th>Email</th></tr></thead><tbody>';
                while ($row = $result_members->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['member_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="alert alert-info" role="alert">Aucun membre trouvé.</div>';
            }
            ?>
        </div>
    </div>
</div>

<script src="/PortCartier/js/jquery-3.3.1.js"></script>
<script src="/PortCartier/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
