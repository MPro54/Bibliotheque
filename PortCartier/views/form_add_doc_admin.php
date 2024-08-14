<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque Port-Cartier</title>
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/style.css">
</head>
<body>
<?php

session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION['employee_id'])) {
    header("Location: /PortCartier/login.php");
    exit();
}

// Inclure les autres fichiers et logique de la page
include("../includes/nav.php");
include("../database/db_connection.php");

// Récupérer les catégories, genres, ratings, etc.
$categories = $conn->query("SELECT category_id, name FROM categories");
$genres = $conn->query("SELECT genre_id, name FROM genres");
$ratings = $conn->query("SELECT rating_id, name FROM audience_ratings");
?>



    <div class="container py-3">
        <div class="panel panel-default margintop">
            <div class="panel-heading hidden"></div>
            <div class="panel-body hidden">
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading w-100 text-center"><h4>Ajouter un document à la bibliothèque</h4></div>
            <div class="panel-body">
                <form action="../actions/add_document.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="title" class="col-sm-2 col-form-label">Titre :</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="author_producer" class="col-sm-2 col-form-label">Créateur :</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="author_producer" name="author_producer" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="publication_year" class="col-sm-2 col-form-label">Année :</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="publication_year" name="publication_year" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="category_id" class="col-sm-2 col-form-label">Catégorie :</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="" selected disabled>Sélectionnez une catégorie</option>
                                <?php
                                if ($categories->num_rows > 0) {
                                    while($row = $categories->fetch_assoc()) {
                                        echo "<option value='" . $row['category_id'] . "'>" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="audience_rating" class="col-sm-2 col-form-label">Classification :</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="audience_rating" name="audience_rating" required>
                                <option value="" selected disabled>Sélectionnez une classification</option>
                                <?php
                                if ($ratings->num_rows > 0) {
                                    while($row = $ratings->fetch_assoc()) {
                                        echo "<option value='" . $row['rating_id'] . "'>" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="genre_id" class="col-sm-2 col-form-label">Genre :</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="genre_id" name="genre_id" required>
                                <option value="" selected disabled>Sélectionnez un genre</option>
                                <?php
                                if ($genres->num_rows > 0) {
                                    while($row = $genres->fetch_assoc()) {
                                        echo "<option value='" . $row['genre_id'] . "'>" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label">Description :</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="isbn" class="col-sm-2 col-form-label">ISBN :</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="isbn" name="isbn" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="image_path" class="col-sm-2 col-form-label">Image :</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control-file" id="image_path" name="image_path" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="quantity" class="col-sm-2 col-form-label">Quantité :</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary" onclick="return validateForm()">Ajouter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Vérifie si le paramètre de requête 'success' est présent dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const successParam = urlParams.get('success');

    // Si 'success' est présent et égal à 'true', affiche le message et rafraîchit la page
    if (successParam === 'true') {
        alert('Document ajouté avec succès!');
        window.location.href = 'form_add_doc_admin.php'; // Rafraîchit la page actuelle
    }

    document.addEventListener('DOMContentLoaded', function() {
        var categorySelect = document.getElementById('category_id');
        var isbnInput = document.getElementById('isbn');

        categorySelect.addEventListener('change', function() {
            if (categorySelect.value == '1') { // Mettez l'ID de la catégorie "livre"
                isbnInput.disabled = false; // Activer le champ ISBN
            } else {
                isbnInput.disabled = true; // Désactiver le champ ISBN
            }
        });
    });

    function validateForm() {
    var title = document.getElementById('title').value.trim();
    var author_producer = document.getElementById('author_producer').value.trim();
    var publicationYear = document.getElementById('publication_year').value.trim();
    var category = document.getElementById('category_id').value.trim();
    var audienceRating = document.getElementById('audience_rating').value.trim();
    var genre = document.getElementById('genre_id').value.trim();
    var description = document.getElementById('description').value.trim();
    var isbn = document.getElementById('isbn').value.trim();
    var quantity = document.getElementById('quantity').value.trim();

    // Vérifier si tous les champs obligatoires sont remplis
    if (title === '' || author_producer === '' || publicationYear === '' || category === '' || audienceRating === '' || genre === '' || description === '' || quantity === '') {
        alert('Veuillez remplir tous les champs obligatoires.');
        return false; // Empêcher la soumission du formulaire
    }

    // Vérifier spécifiquement pour le champ ISBN si la catégorie est "livre"
    if (category === '1' && isbn === '') { // '1' correspond à l'ID de la catégorie "livre"
        alert('Le champ ISBN est obligatoire pour la catégorie "livre".');
        return false; // Empêcher la soumission du formulaire
    }

    // Si la validation réussit, permettre la soumission du formulaire
    return true;
}



</script>
<script src="/PortCartier/js/jquery-3.3.1.js"></script>
<script src="/PortCartier/js/bootstrap.min.js"></script> 
</body>
</html>
