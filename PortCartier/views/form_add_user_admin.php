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
?>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading hidden"></div>
        <div class="panel-body hidden"></div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading w-100 text-center"><h4>Ajouter un utilisateur pour la bibliothèque</h4></div>
        <div class="panel-body">
            <form action="../actions/add_user.php" method="POST" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="first_name" class="col-sm-2 col-form-label">Prénom :</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="last_name" class="col-sm-2 col-form-label">Nom :</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address" class="col-sm-2 col-form-label">Adresse :</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="city" class="col-sm-2 col-form-label">Ville :</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="province" class="col-sm-2 col-form-label">Province :</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="province" name="province" required>
                            <option value="" selected disabled>Sélectionnez la province</option>
                            <option value="QC">Québec</option>
                            <option value="ON">Ontario</option>
                            <option value="BC">Colombie-Britannique</option>
                            <option value="AB">Alberta</option>
                            <option value="PE">Île-du-Prince-Édouard</option>
                            <option value="MB">Manitoba</option>
                            <option value="NB">Nouveau-Brunswick</option>
                            <option value="NS">Nouvelle-Écosse </option>
                            <option value="SK">Saskatchewan</option>
                            <option value="NL">Terre-Neuve-et-Labrador</option>
                            <option value="NU">Nunavut</option>
                            <option value="NT">Territoires du Nord-Ouest</option>
                            <option value="YT">Yukon</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-sm-2 col-form-label">Téléphone :</label>
                    <div class="col-sm-10">
                        <input type="phone" class="form-control" id="phone" name="phone" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Courriel :</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Mot de passe :</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="access" class="col-sm-2 col-form-label">Accès :</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="access" name="access" required>
                            <option value="" selected disabled>Sélectionnez l'accès</option>
                            <option value="member">Membre</option>
                            <option value="employee">Employé(e)</option>
                            <option value="admin">Admin</option>
                        </select>
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
        alert('Utilisateur ajouté avec succès!');
        window.location.href = 'form_add_user_admin.php'; // Rafraîchit la page actuelle
    }

    function validateForm() {
        var first_name = document.getElementById('first_name').value.trim();
        var last_name = document.getElementById('last_name').value.trim();
        var address = document.getElementById('address').value.trim();
        var city = document.getElementById('city').value.trim();
        var province = document.getElementById('province').value.trim();
        var phone = document.getElementById('phone').value.trim();
        var email = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value.trim();
        var access = document.getElementById('access').value.trim();

        // Vérifier si tous les champs obligatoires sont remplis
        if (first_name === '' || last_name === '' || address === '' || city === '' || province === '' || phone === '' || email === '' || password === '' || access === '') {
            alert('Veuillez remplir tous les champs obligatoires.');
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
