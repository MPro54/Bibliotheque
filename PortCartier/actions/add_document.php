<?php
include("../database/db_connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $title = $_POST['title'];
    $author_producer = $_POST['author_producer'];
    $publication_year = $_POST['publication_year'];
    $category_id = $_POST['category_id'];
    $audience_rating_id = $_POST['audience_rating'];
    $genre_id = $_POST['genre_id'];
    $description = $_POST['description'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];

    // Gestion de l'image
    $image_path = '';
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_path']['tmp_name'];
        $fileName = $_FILES['image_path']['name'];
        $fileSize = $_FILES['image_path']['size'];
        $fileType = $_FILES['image_path']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../img/';
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = '../img/' . $fileName;
            } else {
                echo 'Erreur lors du déplacement du fichier vers le répertoire de téléchargement.';
                exit;
            }
        } else {
            echo 'Échec du téléchargement. Types de fichiers autorisés : ' . implode(',', $allowedfileExtensions);
            exit;
        }
    }

    // Insérer les données dans la table documents
    $stmt = $conn->prepare("INSERT INTO documents (title, author_producer, publication_year, category_id, audience_rating_id, genre_id, description, isbn, image_path, reserved_quantity, available_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
    $stmt->bind_param("ssiiissssi", $title, $author_producer, $publication_year, $category_id, $audience_rating_id, $genre_id, $description, $isbn, $image_path, $quantity);

    if ($stmt->execute()) {
        $document_id = $stmt->insert_id;

        // Insérer les données dans la table inventory
        $stmt_inventory = $conn->prepare("INSERT INTO inventory (document_id, quantity, reserved_quantity, available_quantity) VALUES (?, ?, 0, ?)");
        $available_quantity = $quantity; // Initialisé à la quantité
        $stmt_inventory->bind_param("iii", $document_id, $quantity, $available_quantity);

        if ($stmt_inventory->execute()) {
            // Redirection avec succès
            header('Location: ../views/form_add_doc_admin.php?success=true');
            exit;
        } else {
            echo "Erreur lors de l'ajout à l'inventaire.";
        }
    } else {
        echo "Erreur lors de l'ajout du document.";
    }

    $stmt->close();
    $stmt_inventory->close();
    $conn->close();
}
