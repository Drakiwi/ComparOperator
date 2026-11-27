<?php
// Connexion a la base
require_once __DIR__ . '/database.php';

// Les villes pour le menu
$reponse = $db->query('SELECT DISTINCT location FROM destination ORDER BY location');
$locations = $reponse->fetchAll(PDO::FETCH_COLUMN);

// Le tour operateur demande avec ?id=
$id = (int) ($_GET['id'] ?? 0);
$reponse = $db->prepare('SELECT * FROM tour_operator WHERE id = ?');
$reponse->execute([$id]);
$to = $reponse->fetch(PDO::FETCH_ASSOC);

if ($to) {
    // Ses destinations
    $reponse = $db->prepare('SELECT * FROM destination WHERE tour_operator_id = ?');
    $reponse->execute([$id]);
    $destinations = $reponse->fetchAll(PDO::FETCH_ASSOC);

    // Ses avis
    $reponse = $db->prepare('SELECT * FROM review WHERE tour_operator_id = ?');
    $reponse->execute([$id]);
    $avis = $reponse->fetchAll(PDO::FETCH_ASSOC);

    // Note moyenne (0 si personne n'a note)
    $note = 0;
    if ($to['grade_count'] > 0) {
        $note = $to['grade_total'] / $to['grade_count'];
    }
    $etoiles = (int) round($note);
} else {
    $destinations = [];
    $avis = [];
    $note = 0;
    $etoiles = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fiche du tour opérateur : destinations, prix et avis.">
    <title>ComparOperator - <?= $to ? htmlspecialchars($to['name']) : 'Tour opérateur' ?></title>
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
                    <?php foreach ($locations as $loc): ?>
                        <li><a class="dropdown-item" href="comparateur.php?location=<?= urlencode($loc) ?>"><?= htmlspecialchars($loc) ?></a></li>
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
            <?= $to ? htmlspecialchars($to['name']) : 'Tour opérateur introuvable' ?>
            <?php if ($to && $to['is_premium']): ?>
                <span class="badge-premium">PREMIUM</span>
            <?php endif; ?>
        </h1>
        <?php if ($to): ?>
            <p>
                <span class="stars">
                    <?= str_repeat('★', $etoiles) ?><span class="empty"><?= str_repeat('★', 5 - $etoiles) ?></span>
                </span>
                <?= number_format($note, 1) ?>/5 - <?= $to['grade_count'] ?> note(s)
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
            <!-- Colonne gauche : destinations -->
            <div class="info-panel">
                <h2 class="to-subtitle">Destinations proposées</h2>
                <?php foreach ($destinations as $d): ?>
                    <div class="list-price">
                        <a href="comparateur.php?location=<?= urlencode($d['location']) ?>"><?= htmlspecialchars($d['location']) ?></a>
                        <span><?= $d['price'] ?> €</span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($destinations)): ?>
                    <p class="no-review">Aucune destination pour le moment.</p>
                <?php endif; ?>

                <?php if ($to['is_premium']): ?>
                    <a class="btn-brand mt-3 d-inline-block" href="<?= htmlspecialchars($to['link']) ?>"
                       target="_blank" rel="noopener">Visiter le site officiel</a>
                <?php endif; ?>
            </div>

            <!-- Colonne droite : avis -->
            <div class="info-panel">
                <h2 class="to-subtitle">Avis des voyageurs (<?= count($avis) ?>)</h2>
                <?php foreach ($avis as $a): ?>
                    <div class="review">
                        <div class="avatar"><?= htmlspecialchars(strtoupper(mb_substr($a['author'], 0, 1))) ?></div>
                        <div>
                            <div class="who"><?= htmlspecialchars($a['author']) ?></div>
                            <p class="msg"><?= htmlspecialchars($a['message']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($avis)): ?>
                    <p class="no-review">Aucun avis pour le moment.</p>
                <?php endif; ?>

                <button type="button" class="btn-ghost btn-small mt-2 d-inline-block">
                    Laisser un avis ou une note
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>

</body>
</html>
