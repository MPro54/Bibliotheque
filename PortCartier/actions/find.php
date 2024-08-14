<?php
// Connexion à la base de données
include("../database/db_connection.php");

// Récupérer les paramètres de recherche
$search = $_GET['search'] ?? '';
$order = $_GET['order'] ?? '';
$stock = $_GET['stock'] ?? '';
// Récupère les paramètres de recherche, de tri et de stock depuis l'URL (méthode GET).
// Si les paramètres ne sont pas définis, les initialise à des chaînes vides.

// Construire la requête SQL avec les filtres
$sql = "SELECT 
            documents.title,
            documents.author_producer,
            documents.publication_year,
            audience_ratings.name AS audience_rating,
            documents.description,
            genres.name AS genre,
            documents.isbn,
            documents.image_path,
            CASE
                WHEN inv.quantity > 0 THEN 'En stock'
                ELSE 'Rupture de stock'
            END AS stock_status
        FROM 
            documents
        JOIN 
            audience_ratings ON documents.audience_rating_id = audience_ratings.rating_id
        JOIN 
            genres ON documents.genre_id = genres.genre_id
        LEFT JOIN 
            inventory inv ON documents.document_id = inv.document_id
        WHERE 
            1 = 1"; // Utilisation de 1 = 1 pour faciliter l'ajout des conditions WHERE
// Construit la requête SQL de base pour récupérer les documents et leurs informations associées.
// Utilise "1 = 1" pour faciliter l'ajout dynamique de conditions supplémentaires.

// Ajouter les filtres à la requête SQL
if (!empty($search)) {
    $sql .= " AND (documents.title LIKE '%" . $conn->real_escape_string($search) . "%' 
                   OR documents.author_producer LIKE '%" . $conn->real_escape_string($search) . "%')";
}

// Ajoute une condition à la requête SQL pour filtrer les résultats par titre ou auteur/producteur
// si le paramètre de recherche est défini.

if ($stock === 'in_stock') {
    $sql .= " AND inv.quantity > 0";
} elseif ($stock === 'out_of_stock') {
    $sql .= " AND inv.quantity = 0";
}
// Ajoute des conditions à la requête SQL pour filtrer les résultats par état de stock
// (en stock ou en rupture de stock)

// Ajouter le tri à la requête SQL
if ($order === 'asc') {
    $sql .= " ORDER BY documents.title ASC";
} elseif ($order === 'desc') {
    $sql .= " ORDER BY documents.title DESC";
} elseif ($order === 'new_to_old') {
    $sql .= " ORDER BY documents.publication_year DESC";
} elseif ($order === 'old_to_new') {
    $sql .= " ORDER BY documents.publication_year ASC";
}

// Exécution de la requête
$result = $conn->query($sql);
// Exécute la requête SQL et stocke le résultat dans $result.

// Vérification s'il y a des résultats
if ($result && $result->num_rows > 0) {
    // Si des résultats sont trouvés, les affiche
    echo '<div class="row">';
    while ($row = $result->fetch_assoc()) {
        // Affiche chaque résultat sous forme de carte Bootstrap
        echo '<div class="col-md-4 mb-4">';
        echo '<div class="card">';
        echo '<img class="card-img-top img-fluid" src="' . htmlspecialchars($row["image_path"]) . '" alt="Image du livre">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">' . htmlspecialchars($row["title"]) . '</h4>';
        echo '<p class="card-text"><strong>Auteur:</strong> ' . htmlspecialchars($row["author_producer"]) . '</p>';
        echo '<p class="card-text"><strong>Année de publication:</strong> ' . htmlspecialchars($row["publication_year"]) . '</p>';
        echo '<p class="card-text"><strong>Genre:</strong> ' . htmlspecialchars($row["genre"]) . '</p>';
        echo '<p class="card-text"><strong>Classification:</strong> ' . htmlspecialchars($row["audience_rating"]) . '</p>';
        echo '<p class="card-text"><strong>ISBN:</strong> ' . htmlspecialchars($row["isbn"]) . '</p>';
        echo '<p class="card-text"><strong>Description:</strong><br>' . htmlspecialchars($row["description"]) . '</p>';
        echo '<p class="card-text"><strong>État de stock:</strong> ' . htmlspecialchars($row["stock_status"]) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
   // Si aucun résultat n'est trouvé, affiche un message indiquant qu'aucun livre n'a été trouvé.
    echo "<p>Aucun item trouvé.</p>";
}

// Fermer la connexion à la base de données
$conn->close();


