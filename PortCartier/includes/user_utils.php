<?php

// Fonction pour récupérer les informations d'un membre
function get_member_info($conn, $member_id) {
    $stmt = $conn->prepare("
        SELECT member_id, NULL AS employee_id, first_name, last_name, 'member' AS user_type
        FROM members
        WHERE member_id = ?
    ");
    if ($stmt === false) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die("Erreur lors de l'exécution de la requête: " . $stmt->error);
    }
    return $result->fetch_assoc();
}

// Fonction pour récupérer les informations d'un employé
function get_employee_info($conn, $employee_id) {
    $stmt = $conn->prepare("
        SELECT employee_id, first_name, last_name, is_admin, 'employee' AS user_type
        FROM employees
        WHERE employee_id = ?
    ");
    if ($stmt === false) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die("Erreur lors de l'exécution de la requête: " . $stmt->error);
    }
    return $result->fetch_assoc();
}

// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté en tant qu'employé
if (isset($_SESSION['employee_id'])) {
    $employee_id = $_SESSION['employee_id'];
    $employee_info = get_employee_info($conn, $employee_id);

    if ($employee_info) {
        $_SESSION['employee_info'] = $employee_info; // Stocker toutes les informations de l'employé dans $_SESSION
        $_SESSION['is_admin'] = $employee_info['is_admin']; // Stocker is_admin dans $_SESSION si nécessaire
    }
}
