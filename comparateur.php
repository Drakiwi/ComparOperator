<?php
// Connexion a la base
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

// La session sert a retenir les tour operateurs deja notes
session_start();

$message = '';

// ---------- Traitement d'un avis ou d'une note ----------
if (isset($_POST['action'])) {
    $toId = (int) ($_POST['to_id'] ?? 0);

    // Ajouter un avis (un seul avis par nom et par TO)
    if ($_POST['action'] === 'review') {
        $auteur = trim($_POST['auteur'] ?? '');
        $texte = trim($_POST['texte'] ?? '');

        if ($auteur !== '' && $texte !== '') {
            $req = $db->prepare('SELECT COUNT(*) FROM review WHERE tour_operator_id = ? AND author = ?');
            $req->execute([$toId, $auteur]);

            if ((int) $req->fetchColumn() > 0) {
                $message = 'Vous avez déjà laissé un avis pour ce tour opérateur.';
            } else {
                $req = $db->prepare('INSERT INTO review (message, author, tour_operator_id) VALUES (?, ?, ?)');
                $req->execute([$texte, $auteur, $toId]);
                $message = 'Merci, votre avis a été ajouté !';
            }
        }
    }

    // Ajouter une note (une seule note par TO et par session)
    if ($_POST['action'] === 'grade') {
        $note = (int) ($_POST['note'] ?? 0);

        if ($note >= 1 && $note <= 5) {
            if (!isset($_SESSION['notes'])) {
                $_SESSION['notes'] = [];
            }

            if (in_array($toId, $_SESSION['notes'])) {
                $message = 'Vous avez déjà noté ce tour opérateur.';
            } else {
                $req = $db->prepare('UPDATE tour_operator
                                     SET grade_count = grade_count + 1, grade_total = grade_total + ?
                                     WHERE id = ?');
                $req->execute([$note, $toId]);
                $_SESSION['notes'][] = $toId;
                $message = 'Votre note a été enregistrée !';
            }
        }
    }
}

// Ville choisie (clic sur une destination) ou liste de toutes les villes
$location = $_GET['location'] ?? '';

$tourOperateurs = [];
$villes = [];
$meilleureOffre = null;
$offreMieuxNotee = null;
$meilleureNote = 0;

if ($location !== '') {
    // Les TO qui proposent cette ville (du moins cher au plus cher)
    $req = $db->prepare('SELECT t.id, t.name, t.link, t.grade_count, t.grade_total, t.is_premium, d.price
                         FROM destination d
                         JOIN tour_operator t ON t.id = d.tour_operator_id
                         WHERE d.location = ?
                         ORDER BY d.price ASC, t.name ASC');
    $req->execute([$location]);
    $tourOperateurs = $req->fetchAll(PDO::FETCH_ASSOC);

    // La meilleure offre = le prix le plus bas (la liste est deja triee)
    $meilleureOffre = $tourOperateurs[0] ?? null;

    // L'offre la mieux notee (pour l'indiquer aussi)
    $offreMieuxNotee = null;
    $meilleureNote = -1;
    foreach ($tourOperateurs as $offre) {
        if ($offre['grade_count'] > 0) {
            $moyenne = $offre['grade_total'] / $offre['grade_count'];
            if ($moyenne > $meilleureNote) {
                $meilleureNote = $moyenne;
                $offreMieuxNotee = $offre;
            }
        }
    }
} else {
    // Une ligne par ville : la meilleure offre et le nombre d'offres
    $req = $db->query('SELECT location, price, agence, grade_count, grade_total, nb_offres
                       FROM (
                           SELECT d.location, d.price, t.name AS agence, t.grade_count, t.grade_total,
                                  COUNT(*) OVER (PARTITION BY d.location) AS nb_offres,
                                  ROW_NUMBER() OVER (PARTITION BY d.location ORDER BY d.price ASC, t.name ASC) AS rang
                           FROM destination d
                           JOIN tour_operator t ON t.id = d.tour_operator_id
                       ) AS offres
                       WHERE rang = 1
                       ORDER BY location');
    $villes = $req->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ComparOperator : destinations, tour opérateurs, prix, notes et avis.">
    <title>ComparOperator - <?= $location !== '' ? htmlspecialchars($location) : 'Destinations' ?></title>
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

<?php if ($location !== ''): ?>

    <!-- ===== Page des tour operateurs pour une ville ===== -->
    <div class="page-hero">
        <div class="container">
            <a class="back-link" href="index.php">← Retour à l'accueil</a>
            <h1>Tour opérateurs pour <?= htmlspecialchars($location) ?></h1>
            <p>Comparez les prix, les notes et les avis.</p>
        </div>
    </div>

    <div class="wave wave-bottom" style="background:linear-gradient(120deg, #0a4d68, #088395);">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
            <path d="M0,30 C360,60 720,0 1080,30 C1260,45 1380,15 1440,30 L1440,60 L0,60 Z" fill="#faf6ec"/>
        </svg>
    </div>

    <div class="container pb-4">
        <?php if ($message !== ''): ?>
            <div class="flash"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($tourOperateurs)): ?>
            <div class="offers-summary">
                <div class="offers-summary-main">
                    <span class="offers-count"><?= count($tourOperateurs) ?> offres</span>
                    <span>pour <strong><?= htmlspecialchars($location) ?></strong></span>
                </div>
                <div class="offers-summary-best">
                    <span class="badge-best">Meilleure offre</span>
                    <span><strong><?= htmlspecialchars($meilleureOffre['name']) ?></strong> — <?= $meilleureOffre['price'] ?> €</span>
                    <?php if ($offreMieuxNotee !== null): ?>
                        <span class="summary-sep">·</span>
                        <span>Mieux noté : <strong><?= htmlspecialchars($offreMieuxNotee['name']) ?></strong> (<?= number_format($meilleureNote, 1) ?>/5)</span>
                    <?php endif; ?>
                    <span class="offers-sort">Classées par prix croissant</span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($tourOperateurs)): ?>
            <div class="to-empty">Aucun tour opérateur pour cette destination.</div>
        <?php endif; ?>

        <?php foreach ($tourOperateurs as $to): ?>
            <?php
            // Note moyenne = total des notes / nombre de notes
            $note = 0;
            if ($to['grade_count'] > 0) {
                $note = $to['grade_total'] / $to['grade_count'];
            }
            $etoiles = (int) round($note);

            // Cette offre est-elle la moins chere ? La mieux notee ?
            $estMeilleureOffre = $meilleureOffre !== null && $to['id'] === $meilleureOffre['id'];
            $estMieuxNotee = $offreMieuxNotee !== null && $to['id'] === $offreMieuxNotee['id'];

            // Les avis de ce TO
            $req = $db->prepare('SELECT * FROM review WHERE tour_operator_id = ?');
            $req->execute([$to['id']]);
            $avis = $req->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <article class="to-card <?= $to['is_premium'] ? 'is-premium' : '' ?> <?= $estMeilleureOffre ? 'is-best' : '' ?>">
                <div class="to-head">
                    <div>
                        <h2 class="to-name">
                            <?= htmlspecialchars($to['name']) ?>
                            <?php if ($to['is_premium']): ?>
                                <span class="badge-premium">PREMIUM</span>
                            <?php endif; ?>
                            <?php if ($estMeilleureOffre): ?>
                                <span class="badge-best">MEILLEURE OFFRE</span>
                            <?php endif; ?>
                            <?php if ($estMieuxNotee): ?>
                                <span class="badge-top">MIEUX NOTÉ</span>
                            <?php endif; ?>
                        </h2>
                        <div class="stars">
                            <?= str_repeat('★', $etoiles) ?><span class="empty"><?= str_repeat('★', 5 - $etoiles) ?></span>
                        </div>
                        <div class="grade-note">
                            <?= number_format($note, 1) ?>/5 - <?= $to['grade_count'] ?> note(s)
                        </div>
                    </div>
                    <div class="price-tag">
                        <div class="price-label">À partir de</div>
                        <div class="amount"><?= $to['price'] ?> €</div>
                        <div class="price-label">par personne</div>
                    </div>
                </div>

                <div class="to-actions">
                    <?php if ($to['is_premium']): ?>
                        <a class="btn-brand" href="<?= htmlspecialchars($to['link']) ?>" target="_blank" rel="noopener">
                            Site officiel
                        </a>
                    <?php endif; ?>
                    <a class="btn-ghost" href="user.php?id=<?= $to['id'] ?>">Voir la fiche</a>
                </div>

                <hr class="to-sep">
                <h3 class="to-subtitle">Avis des voyageurs</h3>

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

                <!-- Laisser un avis -->
                <div class="form-box">
                    <form method="post">
                        <input type="hidden" name="action" value="review">
                        <input type="hidden" name="to_id" value="<?= $to['id'] ?>">
                        <div class="mb-2">
                            <input type="text" name="auteur" class="form-control" placeholder="Votre nom" required>
                        </div>
                        <div class="mb-2">
                            <textarea name="texte" class="form-control" placeholder="Votre avis" required></textarea>
                        </div>
                        <button type="submit" class="btn-brand btn-small">Envoyer mon avis</button>
                    </form>
                </div>

                <!-- Donner une note -->
                <div class="form-box">
                    <form method="post">
                        <input type="hidden" name="action" value="grade">
                        <input type="hidden" name="to_id" value="<?= $to['id'] ?>">
                        <div class="grade-options">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label class="grade-option">
                                    <input type="radio" name="note" value="<?= $i ?>" required>
                                    <span><?= $i ?> ★</span>
                                </label>
                            <?php endfor; ?>
                        </div>
                        <button type="submit" class="btn-brand btn-small">Envoyer ma note</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

<?php else: ?>

    <!-- ===== Page Destinations (vue d'ensemble) ===== -->
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
            <?php foreach ($villes as $i => $ville): ?>
                <?php
                $note = 0;
                if ($ville['grade_count'] > 0) {
                    $note = $ville['grade_total'] / $ville['grade_count'];
                }
                ?>
                <?php $photo = ville_photo($ville['location']); ?>
                <div class="dest-row <?= $i % 2 === 1 ? 'inverse' : '' ?>">
                    <div class="photo" <?php if ($photo): ?>style="background-image: url('<?= htmlspecialchars($photo, ENT_QUOTES) ?>')"<?php endif; ?>></div>
                    <div class="info">
                        <ul>
                            <li>Nom de la ville : <strong><?= htmlspecialchars($ville['location']) ?></strong></li>
                            <li>Nombre d'offres : <strong><?= (int) $ville['nb_offres'] ?></strong></li>
                            <li>Meilleure offre : <strong><?= htmlspecialchars($ville['agence']) ?></strong></li>
                            <li>Prix en euros : <strong><?= $ville['price'] ?> €</strong></li>
                            <li>Note de l'agence : <strong><?= number_format($note, 1) ?>/5</strong></li>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($villes)): ?>
                <p class="destinations-vide">Aucune destination pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>

<?php endif; ?>

</main>

</body>
</html>
