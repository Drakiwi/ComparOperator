<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/Entities/destinations.php';
require_once __DIR__ . '/Entities/review.php';

// Liste des destinations proposees (lues dans la base)
$locations = Destination::getLocations($db);

// Photos des destinations (images libres Wikimedia)
// si une destination n'a pas de photo, on affiche un degrade + un emoji
$photos = [
    'Rome'      => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/d/de/Colosseo_2020.jpg/960px-Colosseo_2020.jpg',
    'Londres'   => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/6/67/London_Skyline_%28125508655%29.jpeg/960px-London_Skyline_%28125508655%29.jpeg',
    'Monaco'    => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/d/da/Panorama_von_Monaco-La_Turbie.jpg/960px-Panorama_von_Monaco-La_Turbie.jpg',
    'Tunis'     => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/5/55/TunisAveHabibBourguiba.jpg/960px-TunisAveHabibBourguiba.jpg',
    'Paris'     => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/4/4b/La_Tour_Eiffel_vue_de_la_Tour_Saint-Jacques%2C_Paris_ao%C3%BBt_2014_%282%29.jpg/960px-La_Tour_Eiffel_vue_de_la_Tour_Saint-Jacques%2C_Paris_ao%C3%BBt_2014_%282%29.jpg',
    'New York'  => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/7/7a/View_of_Empire_State_Building_from_Rockefeller_Center_New_York_City_dllu_%28cropped%29.jpg/960px-View_of_Empire_State_Building_from_Rockefeller_Center_New_York_City_dllu_%28cropped%29.jpg',
    'Tokyo'     => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/b/b2/Skyscrapers_of_Shinjuku_2009_January.jpg/960px-Skyscrapers_of_Shinjuku_2009_January.jpg',
    'Barcelone' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/a/a6/Evening_light_over_Barcelona.jpg/960px-Evening_light_over_Barcelona.jpg',
    'Lisbonne'  => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/4/41/Lisbon_%2836831596786%29_%28cropped%29.jpg/960px-Lisbon_%2836831596786%29_%28cropped%29.jpg',
    'Marrakech' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/0/07/Marrakech%2C_Morocco_%285421595453%29_%285%29.jpg/960px-Marrakech%2C_Morocco_%285421595453%29_%285%29.jpg',
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ComparOperator : comparez les prix, notes et avis des tour opérateurs et trouvez le meilleur voyage.">
    <title>ComparOperator - Comparez vos Tour Operateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<!-- Hero : logo + navigation + recherche -->
<header class="hero">
    <nav class="site-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="logo" href="index.php">ComparOperator</a>
            <div class="nav-menu">
                <div class="dropdown">
                    <button class="nav-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">Explorer</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach ($locations as $loc): ?>
                            <li>
                                <a class="dropdown-item" href="comparateur.php?location=<?= urlencode($loc) ?>">
                                    <?= htmlspecialchars($loc) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($locations)): ?>
                            <li><span class="dropdown-item disabled">Aucune destination</span></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <a href="comparateur.php">Destinations</a>
            </div>
        </div>
    </nav>

    <div class="container hero-content">
        <h1 class="hero-title">Soleil, aventure,<br>et bons plans !</h1>
        <p class="hero-sub">Comparez vos voyages et partez au soleil au meilleur prix.</p>

        <!-- recherche (affichage seul : aucun script, aucun traitement) -->
        <div class="searchbar">
            <span class="field">
                <select name="location" aria-label="Destination">
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            <span class="field">
                <input type="date" name="dates" aria-label="Dates">
            </span>
            <span class="field">
                <input type="number" name="voyageurs" min="1" max="20" value="2" aria-label="Voyageurs">
            </span>
            <button type="button" class="btn-comparer">Comparer</button>
        </div>

        <div class="trust-row">
            <span class="trust-item">Meilleurs prix</span>
            <span class="trust-item">Réservations faciles</span>
            <span class="trust-item">Voyage 100% sécurisé</span>
        </div>
    </div>
</header>

<!-- Contenu principal de la page -->
<main id="contenu">

<!-- Vague de transition vers la section ocean -->
<div class="wave wave-bottom">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
        <path d="M0,40 C360,80 720,0 1080,40 C1260,60 1380,20 1440,40 L1440,80 L0,80 Z" fill="#088395"/>
    </svg>
</div>

<!-- Destinations -->
<section class="section-ocean">
    <div class="container">
        <h2 class="section-title">Découvrez nos destinations phares</h2>
        <p class="section-sub">Explorez les plus beaux endroits du monde au meilleur prix</p>

        <div class="dest-grid">
            <?php foreach ($locations as $loc): ?>
                <?php $photo = $photos[$loc] ?? null; ?>
                <div class="dest-card">
                    <div class="thumb" <?php if ($photo): ?>style="background-image: url('<?= $photo ?>')"<?php endif; ?>>
                        <?php if (!$photo): ?><?= htmlspecialchars(mb_substr($loc, 0, 1)) ?><?php endif; ?>
                    </div>
                    <span class="dest-name"><?= htmlspecialchars($loc) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($locations)): ?>
            <p class="section-sub mt-4">Aucune destination pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Vague de transition ocean -> sable -->
<div class="wave wave-bottom" style="background:#088395;">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
        <path d="M0,40 C360,0 720,80 1080,40 C1260,20 1380,60 1440,40 L1440,80 L0,80 Z" fill="#faf6ec"/>
    </svg>
</div>

<!-- Bandeau de confiance -->
<section class="section-light">
    <div class="container stats-grid">
        <div class="stat-card">
            <div class="stat-big">Économisez en comparant</div>
            <p class="stat-small">Plus d'offres. Plus de sites. Une seule recherche.</p>
        </div>
        <div class="stat-card">
            <div class="stat-big">41 000 000+</div>
            <p class="stat-small">recherches cette semaine</p>
        </div>
        <div class="stat-card">
            <div class="stat-stars">★★★★★</div>
            <div class="stat-big">Les voyageur·euses nous adorent</div>
            <p class="stat-small">Plus d'1 million d'avis sur notre application.</p>
        </div>
    </div>
</section>

</main>

</body>
</html>
