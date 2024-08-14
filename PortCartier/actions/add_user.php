<?php
include("../database/db_connection.php"); // Inclure le fichier de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $access = $_POST['access'];

    if ($access == 'member') {
        // Insérer les données dans la table members
        $stmt = $conn->prepare("INSERT INTO members (last_name, first_name, address, city, province, phone, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $last_name, $first_name, $address, $city, $province, $phone, $email, $password);
    } elseif ($access == 'employee') {
        // Insérer les données dans la table employees avec is_admin = 0
        $stmt = $conn->prepare("INSERT INTO employees (last_name, first_name, address, city, province, phone, email, password, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssssss", $last_name, $first_name, $address, $city, $province, $phone, $email, $password);
    } elseif ($access == 'admin') {
        // Insérer les données dans la table employees avec is_admin = 1
        $stmt = $conn->prepare("INSERT INTO employees (last_name, first_name, address, city, province, phone, email, password, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("ssssssss", $last_name, $first_name, $address, $city, $province, $phone, $email, $password);
    } else {
        // Gérer le cas où l'accès n'est pas valide
        die("Accès non valide.");
    }

    if ($stmt->execute()) {
        // Rediriger avec un message de succès
        header("Location: ../views/form_add_user_admin.php?success=true");
    } else {
        // Gérer les erreurs d'insertion
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
