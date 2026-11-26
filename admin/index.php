<?php

require_once('../database.php');
require_once __DIR__ . '/../Entities/TourOperator.php';
require_once __DIR__ . '/../Entities/destinations.php';

$locations = ['Rome', 'Londres', 'Monaco', 'Tunis', 'Tokyo', 'Marrakech', 'Bangkok', 'Madrid', 'Dubaï',  'Istanbul', 'Lisbonne'];

$message = '';

if(isset($_SESSION['admin_message'])){
    $message = $_SESSION['admin_message'];
    unset($_SESSION['admin_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])){

    if ($_POST['action'] === 'ajouter_to') {

        $name = trim($_POST['name'] ?? '');
        $link = trim($_POST['link'] ?? '');

        if ($name !== '' && filter_var($link, FILTER_VALIDATE_URL)) {
            $to = new TourOperator($name, $link);

            if ($to->create($db)){
                $_SESSION['admin_message'] = 'Tour-opérateur ajouté avec succès !';
            } else{
                $_SESSION['admin_message'] = 'Erreur lors de l’ajout du tour-opérateur.';
            }
        } else{
            $_SESSION['admin_message'] = 'Veuillez remplir correctement les champs.';
        }
    }

    if ($_POST['action'] === 'ajouter_destination') {

        $location = $_POST['location'] ?? '';
        $price = (float) ($_POST['price'] ?? 0);
        $tourOperatorId = (int) ($_POST['tour_operator_id'] ?? 0);

        $to = TourOperator::getById($db, $tourOperatorId);

        if (in_array($location, $locations, true) && $price > 0 && $to !== null) {

            $destination = new Destination($location, $price, $tourOperatorId);

            if ($destination->create($db)){
                $_SESSION['admin_message'] = 'Destination ajoutée avec succès !';
            } else{
                $_SESSION['admin_message'] = 'Erreur lors de l’ajout de la destination.';
            }

        } else {
            $_SESSION['admin_message'] = 'Informations invalides pour la destination.';
        }
    }

    if ($_POST['action'] === 'premium') {

        $tourOperatorId = (int) ($_POST['tour_operator_id'] ?? 0);
        $premium = ($_POST['premium'] ?? '0') === '1';

        $to = TourOperator::getById($db, $tourOperatorId);

        if ($to !== null) {
            if ($to->setPremium($db, $premium)){
                $_SESSION['admin_message'] = 'Statut Premium modifié avec succès !';
            } else {
                $_SESSION['admin_message'] = 'Erreur lors de la modification du statut.';
            }
        }
    }

    header('Location: index.php');
    exit;
}

$tousLesTo = TourOperator::getAll($db);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administration - ComparOperator</title>

    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

<header class="hero">

    <nav class="site-nav">
        <div class="container d-flex justify-content-between align-items-center">

            <a class="logo" href="../index.php">ComparOperator</a>

            <div class="nav-menu">
                <a href="../index.php">Accueil</a>
                <a href="../comparateur.php">Destinations</a>
                <a href="index.php">Administration</a>
            </div>

        </div>
    </nav>

    <div class="container hero-content">

        <h1 class="hero-title">Espace administrateur</h1>

        <p class="hero-sub">
            Gérez les tour-opérateurs et leurs destinations.
        </p>

        <div class="trust-row">
            <span class="trust-item">Gestion des opérateurs</span>
            <span class="trust-item">Gestion des destinations</span>
            <span class="trust-item">Offres Premium</span>
        </div>

    </div>

</header>

<main id="contenu">

    <div class="wave wave-bottom">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0,40 C360,80 720,0 1080,40 C1260,60 1380,20 1440,40 L1440,80 L0,80 Z" fill="#088395"/>
        </svg>
    </div>

<section class="section-ocean">

    <div class="container">

        <?php if ($message): ?>
            <p class="flash"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <h2 class="section-title">Ajouter un tour-opérateur</h2>

        <div class="admin-card mb-5">

            <form method="POST">

                <input type="hidden" name="action" value="ajouter_to">

                <div class="mb-3">
                    <label class="form-label">Nom du tour-opérateur</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lien du site de l'opérateur</label>
                    <input type="url" name="link" class="form-control" required>
                </div>

                <button type="submit" class="btn-brand">
                    Ajouter l'opérateur
                </button>

            </form>

        </div>

    <h2 class="section-title">Ajouter une destination</h2>

    <div class="admin-card mb-5">

        <form method="POST">

            <input type="hidden" name="action" value="ajouter_destination">

            <div class="mb-3">
                <label class="form-label">Tour-opérateur</label>

            <select name="tour_operator_id" class="form-select" required>

                <?php foreach ($tousLesTo as $to): ?>
                    <option value="<?= $to->getId() ?>">
                        <?= htmlspecialchars($to->getName()) ?>
                    </option>
                <?php endforeach; ?>

            </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Destination</label>

            <select name="location" class="form-select" required>

                <?php foreach ($locations as $location): ?>
                    <option value="<?= htmlspecialchars($location) ?>">
                        <?= htmlspecialchars($location) ?>
                    </option>
                <?php endforeach; ?>

            </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Prix</label>
                <input type="number" name="price" class="form-control" min="1" step="0.01" required>
            </div>

            <button type="submit" class="btn-brand">
                Ajouter la destination
            </button>

        </form>

    </div>

    <h2 class="section-title">Gérer les opérateurs Premium</h2>

    <div class="admin-list">

        <?php foreach ($tousLesTo as $to): ?>

            <div class="admin-card mb-3">

                <h3><?= htmlspecialchars($to->getName()) ?></h3>

                <p>
                    Statut :
                    <?= $to->isPremium() ? 'Premium' : 'Standard' ?>
                </p>

                <form method="POST">

                    <input type="hidden" name="action" value="premium">

                    <input type="hidden" name="tour_operator_id" value="<?= $to->getId() ?>">

                    <input type="hidden" name="premium" value="<?= $to->isPremium() ? '0' : '1' ?>">

                    <button type="submit" class="btn-brand">
                        <?= $to->isPremium() ? 'Retirer Premium' : 'Passer en Premium' ?>
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</section>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
