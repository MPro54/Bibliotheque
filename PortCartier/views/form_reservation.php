<?php
session_start();

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
    <title>Bibliothèque Port-Cartier - Formulaire de Réservation</title>
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/style.css">
</head>
<body>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading w-100 text-center"><h4>Formulaire de réservation</h4></div>
        <div class="panel-body">

            <!-- Zone pour afficher les messages d'erreur ou de succès -->
            <div class="row mb-3">
                <div class="col">
                    <?php
                    if (isset($_SESSION['reserve_error'])) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['reserve_error']) . '</div>';
                        unset($_SESSION['reserve_error']);
                    }

                    if (isset($_SESSION['reserve_success'])) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['reserve_success']) . '</div>';
                        unset($_SESSION['reserve_success']);
                    }
                    ?>
                </div>
            </div>

            <!-- Formulaire de réservation -->
            <form action="../actions/process_reservation.php" method="POST">
                <div class="form-group row">
                    <label for="member_id" class="col-sm-2 col-form-label">Membre :</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="member_id" name="member_id" required>
                            <option value="" selected disabled>Sélectionnez un membre</option>
                            <?php
                            // Récupérer la liste des membres depuis la base de données
                            include $_SERVER['DOCUMENT_ROOT'] . "/PortCartier/database/db_connection.php";
                            $query_members = "SELECT * FROM members";
                            $result_members = $conn->query($query_members);
                            while ($row = $result_members->fetch_assoc()) {
                                echo '<option value="' . $row['member_id'] . '">' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="document_id" class="col-sm-2 col-form-label">Document :</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="document_id" name="document_id" required>
                            <option value="" selected disabled>Sélectionnez un document</option>
                            <?php
                            // Récupérer la liste des documents disponibles depuis la base de données
                            $query_documents = "SELECT * FROM documents";
                            $result_documents = $conn->query($query_documents);
                            while ($row = $result_documents->fetch_assoc()) {
                                echo '<option value="' . $row['document_id'] . '">' . htmlspecialchars($row['title']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="quantity" class="col-sm-2 col-form-label">Quantité :</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary">Faire la réservation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="/PortCartier/js/jquery-3.3.1.js"></script>
<script src="/PortCartier/js/bootstrap.min.js"></script>
</body>
</html>
