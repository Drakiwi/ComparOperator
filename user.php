<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Entities/TourOperator.php';
require_once __DIR__ . '/Entities/destinations.php';
require_once __DIR__ . '/Entities/review.php';

// TO demande via ?id=
$id = (int) ($_GET['id'] ?? 0);
$to = $id > 0 ? TourOperator::getById($db, $id) : null;

$reviews = $to ? Review::getAllByTourOperator($db, $to->getId()) : [];
$fullStars = $to ? (int) round($to->getGrade()) : 0;
$allLocations = Destination::getLocations($db);

// Destinations du TO + lien vers la page ou l'on peut laisser un avis / une note
$toDests = $to ? $to->getDestinations($db) : [];
$detailLink = !empty($toDests)
    ? 'comparateur.php?location=' . urlencode($toDests[0]->getLocation())
    : 'index.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fiche du tour opérateur : destinations, prix, notes et avis des voyageurs.">
    <title>ComparOperator - <?= $to ? htmlspecialchars($to->getName()) : 'Tour opérateur' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<nav class="site-nav solid">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="logo" href="index.php">ComparOperator</a>
        <div class="nav-menu">
            <div class="dropdown">
                <button class="nav-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">Explorer</button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php foreach ($allLocations as $loc): ?>
                        <li>
                            <a class="dropdown-item" href="comparateur.php?location=<?= urlencode($loc) ?>">
                                <?= htmlspecialchars($loc) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="comparateur.php">Destinations</a>
        </div>
    </div>
</nav>

<main id="contenu">
<div class="page-hero">
    <div class="container">
        <a class="back-link" href="index.php">← Retour à l'accueil</a>
        <h1>
            <?= $to ? htmlspecialchars($to->getName()) : 'Tour opérateur introuvable' ?>
            <?php if ($to && $to->isPremium()): ?>
                <span class="badge-premium">PREMIUM</span>
            <?php endif; ?>
        </h1>
        <?php if ($to): ?>
            <p>
                <span class="stars">
                    <?= str_repeat('★', $fullStars) ?><span class="empty"><?= str_repeat('★', 5 - $fullStars) ?></span>
                </span>
                <?= number_format($to->getGrade(), 1) ?>/5 - <?= $to->getGradeCount() ?> note(s)
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="wave wave-bottom" style="background:linear-gradient(120deg, #0a4d68, #088395);">
    <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
        <path d="M0,30 C360,60 720,0 1080,30 C1260,45 1380,15 1440,30 L1440,60 L0,60 Z" fill="#faf6ec"/>
    </svg>
</div>

<div class="container pb-4">
    <?php if (!$to): ?>
        <div class="to-empty">
            Ce tour opérateur n'existe pas.
            <div class="mt-3"><a class="btn-brand btn-small" href="index.php">Retour à l'accueil</a></div>
        </div>
    <?php else: ?>
        <div class="detail-grid">
            <!-- Colonne gauche : destinations proposees -->
            <div class="info-panel">
                <h2 class="to-subtitle">Destinations proposées</h2>
                <?php foreach ($toDests as $d): ?>
                    <div class="list-price">
                        <a href="comparateur.php?location=<?= urlencode($d->getLocation()) ?>">
                            <?= htmlspecialchars($d->getLocation()) ?>
                        </a>
                        <span><?= $d->getPrice() ?> €</span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($toDests)): ?>
                    <p class="no-review">Aucune destination pour le moment.</p>
                <?php endif; ?>

                <?php if ($to->isPremium()): ?>
                    <a class="btn-brand mt-3 d-inline-block" href="<?= htmlspecialchars($to->getLink()) ?>"
                       target="_blank" rel="noopener">Visiter le site officiel</a>
                <?php endif; ?>
            </div>

            <!-- Colonne droite : avis -->
            <div class="info-panel">
                <h2 class="to-subtitle">Avis des voyageurs (<?= count($reviews) ?>)</h2>
                <?php foreach ($reviews as $r): ?>
                    <div class="review">
                        <div class="avatar"><?= htmlspecialchars(strtoupper(mb_substr($r->getAuthor(), 0, 1))) ?></div>
                        <div>
                            <div class="who"><?= htmlspecialchars($r->getAuthor()) ?></div>
                            <p class="msg"><?= htmlspecialchars($r->getMessage()) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($reviews)): ?>
                    <p class="no-review">Aucun avis pour le moment.</p>
                <?php endif; ?>

                <!-- renvoie vers la page du TO pour laisser un avis ou une note -->
                <a class="btn-ghost btn-small mt-2 d-inline-block" href="<?= $detailLink ?>">
                    Laisser un avis ou une note
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>

</body>
</html>
