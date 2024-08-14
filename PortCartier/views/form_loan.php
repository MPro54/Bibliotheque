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

// Récupérer la liste des membres depuis la base de données
$query_members = "SELECT * FROM members";
$result_members = $conn->query($query_members);

// Récupérer la liste des documents disponibles depuis la base de données
$query_documents = "SELECT * FROM documents";
$result_documents = $conn->query($query_documents);

// Récupérer les genres depuis la base de données
$query_genres = "SELECT * FROM genres";
$result_genres = $conn->query($query_genres);

// Récupérer les classifications d'audience depuis la base de données
$query_ratings = "SELECT * FROM audience_ratings";
$result_ratings = $conn->query($query_ratings);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque Port-Cartier - Formulaire de prêt</title>
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/style.css">
</head>
<body>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading w-100 text-center"><h4>Formulaire de prêt</h4></div>
        <div class="panel-body">

         <!-- Afficher les messages -->
         <?php if (isset($_SESSION['loan_success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?= $_SESSION['loan_success']; unset($_SESSION['loan_success']); ?>
                </div>
            <?php elseif (isset($_SESSION['loan_error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['loan_error']; unset($_SESSION['loan_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire-->
            <form action="../actions/process_loan.php" method="POST">
                <div class="form-group row">
                    <label for="member_id" class="col-sm-2 col-form-label">Membre :</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="member_id" name="member_id" required>
                            <option value="" selected disabled>Sélectionnez un membre</option>
                            <?php while($row = $result_members->fetch_assoc()): ?>
                                <option value="<?= $row['member_id'] ?>"><?= $row['first_name'] ?> <?= $row['last_name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="document_id" class="col-sm-2 col-form-label">Document :</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="document_id" name="document_id" required>
                            <option value="" selected disabled>Sélectionnez un document</option>
                            <?php while($row = $result_documents->fetch_assoc()): ?>
                                <option value="<?= $row['document_id'] ?>"><?= $row['title'] ?></option>
                            <?php endwhile; ?>
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
                    <label for="loan_date" class="col-sm-2 col-form-label">Date d'emprunt :</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="loan_date" name="loan_date" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="return_date" class="col-sm-2 col-form-label">Date de retour :</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="return_date" name="return_date" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary">Enregistrer le prêt</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Script JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        let today = new Date().toISOString().split('T')[0];
        document.getElementById('loan_date').value = today;
    });
</script>
<script src="/PortCartier/js/jquery-3.3.1.js"></script>
<script src="/PortCartier/js/bootstrap.min.js"></script>
</body>
</html>
