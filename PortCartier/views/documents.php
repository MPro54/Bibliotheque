<?php
session_start(); // Démarrer la session pour vérifier l'état de connexion de l'utilisateur

// Inclusion des fichiers nécessaires
include($_SERVER['DOCUMENT_ROOT'] . "/PortCartier/database/db_connection.php");

// Récupération des paramètres GET
$search = isset($_GET['search']) ? $_GET['search'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : '';
$category_id = isset($_GET['category']) ? $_GET['category'] : '';
$genre_id = isset($_GET['genre']) ? $_GET['genre'] : '';
$rating_id = isset($_GET['rating']) ? $_GET['rating'] : '';

// Mise en mémoire tampon pour capturer le contenu HTML
ob_start();

// Affichage des messages d'erreur ou de succès
if (isset($_SESSION['reserve_error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['reserve_error']) . '</div>';
    unset($_SESSION['reserve_error']);
}

if (isset($_SESSION['reserve_success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['reserve_success']) . '</div>';
    unset($_SESSION['reserve_success']);
}

// Vérifier si l'utilisateur est un employé
$is_employee = isset($_SESSION['employee_id']);

// Requête SQL de base pour récupérer les documents
$sql = "SELECT 
            documents.document_id,
            documents.title,
            documents.author_producer,
            documents.publication_year,
            audience_ratings.name AS audience_rating,
            documents.description,
            genres.name AS genre,
            documents.isbn,
            documents.image_path,
            IFNULL(inv.quantity, 0) AS stock_quantity,
            IFNULL(inv.reserved_quantity, 0) AS reserved_quantity,
            IFNULL(inv.quantity, 0) - IFNULL(inv.reserved_quantity, 0) AS available_quantity,
            CASE
                WHEN IFNULL(inv.quantity, 0) > 0 THEN 'En stock'
                ELSE 'Rupture de stock'
            END AS stock_status
        FROM 
            documents
        JOIN 
            audience_ratings ON documents.audience_rating_id = audience_ratings.rating_id
        JOIN 
            genres ON documents.genre_id = genres.genre_id
        LEFT JOIN 
            (SELECT document_id, SUM(quantity) AS quantity, SUM(reserved_quantity) AS reserved_quantity FROM inventory GROUP BY document_id) AS inv ON documents.document_id = inv.document_id
        WHERE 1=1"; // WHERE 1=1 pour permettre l'ajout dynamique de conditions

// Ajout des conditions de recherche si nécessaire
$params = [];
if (!empty($search)) {
    $sql .= " AND (documents.title LIKE ? OR 
                    documents.author_producer LIKE ? OR
                    CAST(documents.publication_year AS CHAR) LIKE ? OR
                    audience_ratings.name LIKE ? OR 
                    documents.description LIKE ? OR 
                    genres.name LIKE ? OR 
                    documents.isbn LIKE ?)";
    $params = array_fill(0, 7, '%' . $search . '%');
}

// Ajouter les filtres supplémentaires pour catégorie, genre et notation d'audience
if (!empty($category_id)) {
    $sql .= " AND documents.category_id = ?";
    $params[] = $category_id;
}

if (!empty($genre_id)) {
    $sql .= " AND documents.genre_id = ?";
    $params[] = $genre_id;
}

if (!empty($rating_id)) {
    $sql .= " AND documents.audience_rating_id = ?";
    $params[] = $rating_id;
}

// Ajout de l'ordre de tri
switch ($order) {
    case 'asc':
        $sql .= " ORDER BY documents.title ASC";
        break;
    case 'desc':
        $sql .= " ORDER BY documents.title DESC";
        break;
    case 'new_to_old':
        $sql .= " ORDER BY documents.publication_year DESC";
        break;
    case 'old_to_new':
        $sql .= " ORDER BY documents.publication_year ASC";
        break;
    case 'in_stock':
        $sql .= " ORDER BY inv.quantity DESC";
        break;
    default:
        // Pas de tri spécifique par défaut
        break;
}

// Préparation de la requête SQL
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Erreur de préparation de la requête: " . $conn->error;
    exit;
}

// Liaison des paramètres s'il y a une recherche
if (!empty($search)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
} elseif (!empty($params)) {
    $param_types = str_repeat('i', count($params));
    $stmt->bind_param($param_types, ...$params);
}

// Exécution de la requête
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo '<div class="row">';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-md-4 mb-4">';
        echo '<div class="card">';
        echo '<p class="card-text"><strong>État de stock:</strong> ' . htmlspecialchars($row["stock_status"]) . '</p>';
        echo '<p class="card-text"><strong>En stock:</strong> ' . htmlspecialchars($row["stock_quantity"]) . '</p>';
        echo '<p class="card-text"><strong>Réservé:</strong> ' . htmlspecialchars($row["reserved_quantity"]) . '</p>';
        echo '<p class="card-text"><strong>Disponible:</strong> ' . htmlspecialchars($row["available_quantity"]) . '</p>';
        echo '<img class="card-img-top img-fluid" src="' . htmlspecialchars($row["image_path"]) . '" alt="Image">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">' . htmlspecialchars($row["title"]) . '</h4>';
        echo '<p class="card-text"><strong>Auteur/Producteur:</strong> ' . htmlspecialchars($row["author_producer"]) . '</p>';
        echo '<p class="card-text"><strong>Année de publication:</strong> ' . htmlspecialchars($row["publication_year"]) . '</p>';
        echo '<p class="card-text"><strong>Genre:</strong> ' . htmlspecialchars($row["genre"]) . '</p>';
        echo '<p class="card-text"><strong>Classification:</strong> ' . htmlspecialchars($row["audience_rating"]) . '</p>';
        if (!empty($row["isbn"])) {
            echo '<p class="card-text"><strong>ISBN:</strong> ' . htmlspecialchars($row["isbn"]) . '</p>';
        }
        echo '<p class="card-text"><strong>Description:</strong><br>' . htmlspecialchars($row["description"]) . '</p>';

        // Afficher le bouton "Réserver" uniquement si l'utilisateur est un membre
        if (!$is_employee) {
            echo '<button class="btn btn-primary btn-reserve" data-document-id="' . $row["document_id"] . '">Réserver</button>';
        }
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo "<p>Aucun document trouvé.</p>";
}

$stmt->close();
$content = ob_get_clean(); // Récupération du contenu HTML généré

// Inclusion du template pour afficher la page complète
include($_SERVER['DOCUMENT_ROOT'] . "/PortCartier/views/template.php");
?>

<!-- Script JavaScript pour afficher une alerte -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reserveButtons = document.querySelectorAll('.btn-reserve');

    reserveButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            const documentId = button.getAttribute('data-document-id');
            
            const dialog = document.createElement('div');
            dialog.style.position = 'fixed';
            dialog.style.left = '50%';
            dialog.style.top = '50%';
            dialog.style.transform = 'translate(-50%, -50%)';
            dialog.style.padding = '20px';
            dialog.style.backgroundColor = 'white';
            dialog.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
            dialog.style.zIndex = '1000';

            dialog.innerHTML = `
                <p>Entrez la quantité à réserver :</p>
                <input type="number" id="reserveQuantity" min="1" value="1" style="width: 100%; margin-bottom: 10px;">
                <button id="confirmReserve" class="btn btn-primary">Confirmer</button>
                <button id="cancelReserve" class="btn btn-secondary">Annuler</button>
            `;

            document.body.appendChild(dialog);

            document.getElementById('confirmReserve').addEventListener('click', function() {
                const quantity = document.getElementById('reserveQuantity').value;
                if (quantity > 0) {
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.action = '/PortCartier/actions/reserve_action.php';
                    
                    const inputDocumentId = document.createElement('input');
                    inputDocumentId.type = 'hidden';
                    inputDocumentId.name = 'document_id';
                    inputDocumentId.value = documentId;
                    form.appendChild(inputDocumentId);
                    
                    const inputQuantity = document.createElement('input');
                    inputQuantity.type = 'hidden';
                    inputQuantity.name = 'quantity';
                    inputQuantity.value = quantity;
                    form.appendChild(inputQuantity);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
                document.body.removeChild(dialog);
            });

            document.getElementById('cancelReserve').addEventListener('click', function() {
                document.body.removeChild(dialog);
            });
        });
    });
});
</script>
