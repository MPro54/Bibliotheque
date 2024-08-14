<?php
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION['employee_id'])) {
    header("Location: /PortCartier/login.php");
    exit();
}

// Inclure les fichiers nécessaires
include("../includes/nav.php");
include("../database/db_connection.php");

// Initialisation des variables
$error_message = "";
$success_message = "";
$loans = [];

// Traitement du formulaire de recherche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_term'])) {
    $search_term = $_POST['search_term'];

    // Vérifier si le champ de recherche est vide
    if (empty($search_term)) {
        $error_message = "Aucun terme de recherche spécifié.";
    } else {
        $search_query = "%{$search_term}%";

        // Requête SQL pour rechercher les emprunts par informations sur le document ou le client
        $query_search = "SELECT loans.loan_id, loans.loan_date, loans.due_date, loans.quantity, documents.title, members.first_name, members.last_name 
                         FROM loans
                         JOIN documents ON loans.document_id = documents.document_id
                         JOIN members ON loans.member_id = members.member_id
                         WHERE loans.return_date IS NULL
                         AND (documents.title LIKE ? 
                            OR members.first_name LIKE ? 
                            OR members.last_name LIKE ?)";
        $stmt_search = $conn->prepare($query_search);
        $stmt_search->bind_param('sss', $search_query, $search_query, $search_query);
        $stmt_search->execute();
        $result_search = $stmt_search->get_result();
        $loans = $result_search->fetch_all(MYSQLI_ASSOC);
    }
} else {
    // Récupérer tous les emprunts actifs si aucun terme de recherche n'est spécifié
    $query_all = "SELECT loans.loan_id, loans.loan_date, loans.due_date, loans.quantity, documents.title, members.first_name, members.last_name 
                  FROM loans
                  JOIN documents ON loans.document_id = documents.document_id
                  JOIN members ON loans.member_id = members.member_id
                  WHERE loans.return_date IS NULL";
    $result_all = $conn->query($query_all);
    $loans = $result_all->fetch_all(MYSQLI_ASSOC);
}

// Traitement du formulaire de retour de document
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_loan_id']) && isset($_POST['return_date'])) {
    $return_loan_id = $_POST['return_loan_id'];
    $return_date = $_POST['return_date'];

    // Début de la transaction pour assurer l'intégrité des données
    $conn->begin_transaction();

    // Mettre à jour la date de retour du document
    $query_return = "UPDATE loans SET return_date = ? WHERE loan_id = ?";
    $stmt_return = $conn->prepare($query_return);
    $stmt_return->bind_param('si', $return_date, $return_loan_id);
    if ($stmt_return->execute()) {
        // Récupérer la quantité empruntée pour ce prêt
        $query_loan_details = "SELECT document_id, quantity FROM loans WHERE loan_id = ?";
        $stmt_loan_details = $conn->prepare($query_loan_details);
        $stmt_loan_details->bind_param('i', $return_loan_id);
        $stmt_loan_details->execute();
        $result_loan_details = $stmt_loan_details->get_result();
        $loan_details = $result_loan_details->fetch_assoc();

        // Mettre à jour le stock d'inventaire
        $query_update_inventory = "UPDATE inventory SET available_quantity = available_quantity + ? WHERE document_id = ?";
        $stmt_update_inventory = $conn->prepare($query_update_inventory);
        $stmt_update_inventory->bind_param('ii', $loan_details['quantity'], $loan_details['document_id']);
        if ($stmt_update_inventory->execute()) {
            $success_message = "Le document a été retourné avec succès. Le stock d'inventaire a été mis à jour.";
            // Valider la transaction
            $conn->commit();

            // Recharger les emprunts actifs après la mise à jour
            $query_all = "SELECT loans.loan_id, loans.loan_date, loans.due_date, loans.quantity, documents.title, members.first_name, members.last_name 
                          FROM loans
                          JOIN documents ON loans.document_id = documents.document_id
                          JOIN members ON loans.member_id = members.member_id
                          WHERE loans.return_date IS NULL";
            $result_all = $conn->query($query_all);
            $loans = $result_all->fetch_all(MYSQLI_ASSOC);
        } else {
            // Rollback en cas d'erreur
            $conn->rollback();
            $error_message = "Erreur lors de la mise à jour du stock d'inventaire. Veuillez réessayer.";
        }
    } else {
        $error_message = "Erreur lors du retour du document. Veuillez réessayer.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Retours de Documents</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>

<div class="container py-3">
    <div class="panel panel-default margintop">
        <div class="panel-heading w-100 text-center">
            <h4>Liste des Retours de Documents</h4>
        </div>
        <div class="panel-body">

            <!-- Formulaire de recherche -->
            <form action="list_return.php" method="POST" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Rechercher par titre, nom ou prénom..." name="search_term">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                    </div>
                </div>
            </form>

            <!-- Affichage des messages d'erreur et de succès -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <!-- Affichage des documents à retourner -->
            <?php if (!empty($loans)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Prêt</th>
                            <th>Titre du Document</th>
                            <th>Nom du Membre</th>
                            <th>Date d'Emprunt</th>
                            <th>Date de Retour Prévue</th>
                            <th>Quantité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><?= htmlspecialchars($loan['loan_id']); ?></td>
                                <td><?= htmlspecialchars($loan['title']); ?></td>
                                <td><?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']); ?></td>
                                <td><?= htmlspecialchars($loan['loan_date']); ?></td>
                                <td><?= htmlspecialchars($loan['due_date']); ?></td>
                                <td><?= htmlspecialchars($loan['quantity']); ?></td>
                                <td>
                                    <form action="list_return.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir retourner ce document ?');">
                                        <input type="hidden" name="return_loan_id" value="<?= htmlspecialchars($loan['loan_id']); ?>">
                                        <input type="date" name="return_date" required>
                                        <button type="submit" class="btn btn-primary">Retourner</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info" role="alert">Aucun prêt en cours trouvé.</div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="/PortCartier/js/jquery-3.3.1.js"></script>
<script src="/PortCartier/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Fermer la connexion et libérer les ressources
if (isset($result_search)) {
    $result_search->free();
}
if (isset($result_all)) {
    $result_all->free();
}
if (isset($stmt_search)) {
    $stmt_search->close();
}
if (isset($stmt_return)) {
    $stmt_return->close();
}
$conn->close();
?>
