<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Entities/TourOperator.php';
require_once __DIR__ . '/Entities/destinations.php';

// Photos des destinations (images libres Wikimedia)
$photos = [
    'Rome'    => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/d/de/Colosseo_2020.jpg/960px-Colosseo_2020.jpg',
    'Londres' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/6/67/London_Skyline_%28125508655%29.jpeg/960px-London_Skyline_%28125508655%29.jpeg',
    'Monaco'  => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/d/da/Panorama_von_Monaco-La_Turbie.jpg/960px-Panorama_von_Monaco-La_Turbie.jpg',
    'Tunis'   => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/5/55/TunisAveHabibBourguiba.jpg/960px-TunisAveHabibBourguiba.jpg',
];

// Pour chaque ville : l'agence, sa note et le prix (lecture dans la base)
$items = [];
$tousLesTo = TourOperator::getAll($db);
foreach (Destination::getLocations($db) as $ville) {
    foreach ($tousLesTo as $to) {
        foreach ($to->getDestinations($db) as $dest) {
            if ($dest->getLocation() === $ville) {
                $items[] = [
                    'ville'  => $ville,
                    'note'   => $to->getGrade(),
                    'agence' => $to->getName(),
                    'prix'   => $dest->getPrice(),
                    'photo'  => $photos[$ville] ?? null,
                ];
                break 2; // on passe a la ville suivante
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ComparOperator : les destinations avec leur note, leur agence et leur prix.">
    <title>ComparOperator - Destinations</title>
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
            <button class="nav-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">Explorer</button>
            <a href="comparateur.php">Destinations</a>
        </div>
    </div>
</nav>

<main id="contenu">
    <!-- En-tete dans le meme style que la page d'accueil -->
    <div class="page-hero">
        <div class="container">
            <a class="back-link" href="index.php">← Retour à l'accueil</a>
            <h1>Destinations</h1>
            <p>Comparez les villes, les notes et les agences au meilleur prix.</p>
        </div>
    </div>

    <div class="wave wave-bottom" style="background:linear-gradient(120deg, #0a4d68, #088395);">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
            <path d="M0,30 C360,60 720,0 1080,30 C1260,45 1380,15 1440,30 L1440,60 L0,60 Z" fill="#faf6ec"/>
        </svg>
    </div>

    <section class="destinations-page">
        <div class="destinations-card">
            <?php foreach ($items as $i => $item): ?>
                <div class="dest-row <?= $i % 2 === 1 ? 'inverse' : '' ?>">
                    <div class="photo" <?php if ($item['photo']): ?>style="background-image: url('<?= $item['photo'] ?>')"<?php endif; ?>></div>
                    <div class="info">
                        <ul>
                            <li>Nom de la ville : <strong><?= htmlspecialchars($item['ville']) ?></strong></li>
                            <li>Note global : <strong><?= number_format($item['note'], 1) ?>/5</strong></li>
                            <li>Agence proposant le voyage : <strong><?= htmlspecialchars($item['agence']) ?></strong></li>
                            <li>Prix en euros : <strong><?= $item['prix'] ?> €</strong></li>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($items)): ?>
                <p class="destinations-vide">Aucune destination pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>
