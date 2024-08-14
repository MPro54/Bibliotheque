<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque de Port-Cartier</title>
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PortCartier/css/style.css">
</head>
<body>
<?php
    session_start();
    if (isset($_SESSION['error'])) {
        echo "<p class='text-danger text-center'>" . htmlspecialchars($_SESSION['error']) . "</p>";
        unset($_SESSION['error']);
    }
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header w-100 text-center">
            <h3>Bibliothèque de Port-Cartier</h3>
        </div>
    </div>
</nav>
   
<div class="container py-3">
    <div class="panel panel-default my-3">
        <div class="panel-heading"></div>
        <div class="panel-body">
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <form action="/PortCartier/actions/login_action.php" method="POST" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="courriel" class="col-sm-2 col-form-label">Courriel:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="email" name="email" placeholder="courriel..." required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Mot de passe:</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name="password" placeholder="mot de passe..." required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-info btn-lg">Se connecter</button>
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
