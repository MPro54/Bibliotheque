<?php

// Fonction pour récupérer toutes les catégories depuis la base de données
function getAllCategoriesFromDatabase($conn) {
    $query = "SELECT category_id, name FROM categories";
    $result = mysqli_query($conn, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

// Fonction pour récupérer tous les genres depuis la base de données
function getAllGenresFromDatabase($conn) {
    $query = "SELECT genre_id, name FROM genres";
    $result = mysqli_query($conn, $query);
    $genres = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $genres[] = $row;
    }
    return $genres;
}

// Fonction pour récupérer toutes les notations d'audience depuis la base de données
function getAllAudienceRatingsFromDatabase($conn) {
    $query = "SELECT rating_id, name FROM audience_ratings";
    $result = mysqli_query($conn, $query);
    $ratings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ratings[] = $row;
    }
    return $ratings;
}

// Ajoutez d'autres fonctions pour récupérer d'autres types de données si nécessaire
?>
