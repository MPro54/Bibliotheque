<?php
include($_SERVER['DOCUMENT_ROOT'] . "/PortCartier/database/db_connection.php"); // Inclure la connexion à la base de données
include($_SERVER['DOCUMENT_ROOT'] . "/PortCartier/includes/data_access_functions.php"); // Inclure les fonctions d'accès aux données
?>

<form action="<?php echo "../views/documents.php"; ?>" method="get" class="mb-4">
    <div class="form-row">
        <div class="col-md-3 mb-3">
            <label for="search">Rechercher</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Rechercher..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label for="order">Affichage</label>
            <select name="order" id="order" class="form-control">
                <option value="">par défaut...</option>
                <option value="asc" <?php echo (isset($_GET['order']) && $_GET['order'] == 'asc') ? 'selected' : ''; ?>>A à Z</option>
                <option value="desc" <?php echo (isset($_GET['order']) && $_GET['order'] == 'desc') ? 'selected' : ''; ?>>Z à A</option>
                <option value="new_to_old" <?php echo (isset($_GET['order']) && $_GET['order'] == 'new_to_old') ? 'selected' : ''; ?>>Du plus récent au plus vieux</option>
                <option value="old_to_new" <?php echo (isset($_GET['order']) && $_GET['order'] == 'old_to_new') ? 'selected' : ''; ?>>Du plus vieux au plus récent</option>
                <option value="in_stock" <?php echo (isset($_GET['order']) && $_GET['order'] == 'in_stock') ? 'selected' : ''; ?>>En stock</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="category">Catégorie</label>
            <select name="category" id="category" class="form-control">
                <option value="">Toutes les catégories</option>
                <?php
                $categories = getAllCategoriesFromDatabase($conn);
                foreach ($categories as $cat) {
                    echo "<option value=\"" . htmlspecialchars($cat['category_id']) . "\" " . (isset($_GET['category']) && $_GET['category'] == $cat['category_id'] ? 'selected' : '') . ">" . htmlspecialchars($cat['name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="genre">Genre</label>
            <select name="genre" id="genre" class="form-control">
                <option value="">Tous les genres</option>
                <?php
                $genres = getAllGenresFromDatabase($conn);
                foreach ($genres as $genre) {
                    echo "<option value=\"" . htmlspecialchars($genre['genre_id']) . "\" " . (isset($_GET['genre']) && $_GET['genre'] == $genre['genre_id'] ? 'selected' : '') . ">" . htmlspecialchars($genre['name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="rating">Notation d'audience</label>
            <select name="rating" id="rating" class="form-control">
                <option value="">Toutes les notations</option>
                <?php
                $ratings = getAllAudienceRatingsFromDatabase($conn);
                foreach ($ratings as $rating) {
                    echo "<option value=\"" . htmlspecialchars($rating['rating_id']) . "\" " . (isset($_GET['rating']) && $_GET['rating'] == $rating['rating_id'] ? 'selected' : '') . ">" . htmlspecialchars($rating['name']) . "</option>";
                }
                ?>
            </select>
        </div>


        <div class="col-md-3">
            <label></label>
            <button type="submit" class="btn btn-primary" id="submitBtn" <?php echo (isset($_GET['category']) && $_GET['category'] == '0') ? 'disabled' : ''; ?>>Rechercher</button>
        </div>
    </div>
</form>

