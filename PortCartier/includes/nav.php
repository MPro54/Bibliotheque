<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Initialisation des variables
$is_employee = false;
$is_admin = false;
$is_member = false;

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] == 'employee') {
        $is_employee = true;
        $is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    } elseif ($_SESSION['user_type'] == 'member') {
        $is_member = true;
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a href="../views/documents.php" class="navbar-brand">Bibliothèque de Port-Cartier</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-white" href="/PortCartier/views/documents.php">Accueil</a>
                </li>
                <!-- Menu Membres -->
                <?php if ($is_employee || $is_admin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="membersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Membres
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="membersDropdown">
                            <?php if ($is_employee): ?>
                                <li><a class="dropdown-item" href="/PortCartier/views/member.php">Informations sur un membre</a></li>
                                <li><a class="dropdown-item" href="/PortCartier/list/list_members.php">Liste</a></li>
                            <?php endif; ?>
                            <?php if ($is_admin): ?>
                                <li><a class="dropdown-item" href="/PortCartier/views/form_add_user_admin.php">Ajouter membre</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Menu Documents -->
                <?php if ($is_employee || $is_admin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="documentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Documents
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="documentsDropdown">
                            <li><a class="dropdown-item" href="/PortCartier/views/documents.php">Documents</a></li>
                            <?php if ($is_employee): ?>
                                <li><a class="dropdown-item" href="/PortCartier/list/list_documents.php">Liste</a></li>
                            <?php endif; ?>
                            <?php if ($is_admin): ?>
                                <li><a class="dropdown-item" href="/PortCartier/views/form_add_doc_admin.php">Ajouter document</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Menu Gestion -->
                <?php if ($is_employee || $is_admin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gestion
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="managementDropdown">
                            <li><a class="dropdown-item" href="/PortCartier/views/form_reservation.php">Faire une réservation</a></li>
                            <li><a class="dropdown-item" href="/PortCartier/list/list_reservation.php">Liste des réservations actives</a></li>
                            <li><a class="dropdown-item" href="/PortCartier/views/form_loan.php">Faire un prêt</a></li>
                            <li><a class="dropdown-item" href="/PortCartier/list/list_due_loan.php">Liste des prêt en retard</a></li>
                            <li><a class="dropdown-item" href="/PortCartier/list/list_return.php">Gestion des retours effectués</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="/PortCartier/actions/logout_action.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

