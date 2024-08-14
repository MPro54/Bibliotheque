<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['user_id']) && !isset($_SESSION['employee_id'])) {
    header("Location: /PortCartier/login.php");
    exit();
}

// Inclure le fichier de navigation
include $_SERVER['DOCUMENT_ROOT'] . "/PortCartier/includes/nav.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque Port-Cartier</title>
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/style.css">
    <style>
        .card-img-top {
            max-height: 300px;
            object-fit: cover;
        }
        .card-text.description {
            max-height: 100px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="panel panel-default margintop">
            <div class="panel-body">
                <?php
                include $_SERVER['DOCUMENT_ROOT'] . "/PortCartier/database/db_connection.php";
                include $_SERVER['DOCUMENT_ROOT'] . "/PortCartier/includes/user_utils.php";

                // Vérifier si l'utilisateur est connecté en tant que membre
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $user_info = get_member_info($conn, $user_id);

                    if ($user_info) {
                        $user_name = htmlspecialchars($user_info['first_name']) . ' ' . htmlspecialchars($user_info['last_name']);
                        echo "</br></br><h4>Bienvenue $user_name</br></br></h4>";
                    }
                } elseif (isset($_SESSION['employee_id'])) {
                    $employee_id = $_SESSION['employee_id'];
                    $employee_info = get_employee_info($conn, $employee_id);

                    if ($employee_info) {
                        $employee_name = htmlspecialchars($employee_info['first_name']) . ' ' . htmlspecialchars($employee_info['last_name']);

                        if ($employee_info['is_admin'] == 1) {
                            echo "<h4></br></br>Bienvenue Admin $employee_name</br></br></h4>";
                        } else {
                            echo "<h4></br></br>Bienvenue Employé(e) $employee_name</br></br></h4>";
                        }
                    }
                }

                // Inclure votre formulaire de recherche existant
                include $_SERVER['DOCUMENT_ROOT'] . "/PortCartier/includes/search_form.php";
                ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo $content; ?>
            </div>
        </div>
    </div>

    <script src="/PortCartier/js/jquery-3.3.1.js"></script>
    <script src="/PortCartier/js/bootstrap.min.js"></script>
</body>
</html>
