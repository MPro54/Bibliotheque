<?php
session_start();
include("../database/db_connection.php"); // Inclure le fichier de connexion à la base de données

if ($conn->connect_error) {
    die("Échec de la connexion à la base de données: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification de l'utilisateur dans la table members
    $stmt = $conn->prepare("SELECT member_id, password, first_name, last_name FROM members WHERE email = ?");
    if ($stmt === false) {
        die("Erreur de préparation de la requête (membres): " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die("Erreur lors de l'exécution de la requête (membres): " . $stmt->error);
    }
    $user = $result->fetch_assoc();

    // Vérification de l'employé dans la table employees
    if (!$user) {
        $stmt = $conn->prepare("SELECT employee_id, password, first_name, last_name, is_admin FROM employees WHERE email = ?");
        if ($stmt === false) {
            die("Erreur de préparation de la requête (employés): " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            die("Erreur lors de l'exécution de la requête (employés): " . $stmt->error);
        }
        $user = $result->fetch_assoc();
    }

    if ($user) {
        if ($password == $user['password']) {  // Fonctionne seulement si les mot de passe visible dans la base de donnée
            if (isset($user['member_id'])) {
                $_SESSION['user_id'] = $user['member_id'];
                $_SESSION['user_type'] = 'member';
            } elseif (isset($user['employee_id'])) {
                $_SESSION['employee_id'] = $user['employee_id'];
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['user_type'] = 'employee';
            }
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            header('Location: ../views/documents.php');
            exit();
        } else {
            echo "Mot de passe incorrect.<br>";
        }
    } else {
        echo "Utilisateur non trouvé.<br>";
    }

    $_SESSION['error'] = "Courriel ou mot de passe incorrect.";
    header('Location: ../login.php');
    exit();
}

