<?php
// Connexion a la base du projet ComparOperator
require_once __DIR__ . '/../../database.php';

session_start();

// Mot de passe administrateur (POC)
define('ADMIN_PASSWORD', 'comparo');

// Deconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header('Location: index.php');
    exit;
}

// Authentification
$authError = '';
if (isset($_POST['login_password'])) {
    if ($_POST['login_password'] === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $authError = 'Mot de passe incorrect.';
    }
}

$isAdmin = !empty($_SESSION['admin']);

// ---------- Traitement des actions (uniquement si admin) ----------
$message = '';
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_to') {
        $name = trim($_POST['name'] ?? '');
        $link = trim($_POST['link'] ?? '');
        if ($name !== '' && $link !== '') {
            $req = $db->prepare('INSERT INTO tour_operator (name, link, grade_count, grade_total, is_premium) VALUES (?, ?, 0, 0, 0)');
            $req->execute([$name, $link]);
            $message = 'Tour opérateur ajouté.';
        } else {
            $message = 'Nom et lien obligatoires.';
        }
    }

    if ($action === 'toggle_premium') {
        $id = (int) ($_POST['id'] ?? 0);
        $req = $db->prepare('UPDATE tour_operator SET is_premium = 1 - is_premium WHERE id = ?');
        $req->execute([$id]);
        $message = 'Statut premium mis à jour.';
    }

    if ($action === 'add_destination') {
        $toId = (int) ($_POST['to_id'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $price = (int) ($_POST['price'] ?? 0);
        if ($toId > 0 && $location !== '' && $price > 0) {
            $req = $db->prepare('INSERT INTO destination (location, price, tour_operator_id) VALUES (?, ?, ?)');
            $req->execute([$location, $price, $toId]);
            $message = 'Destination ajoutée à ce tour opérateur.';
        } else {
            $message = 'Tous les champs sont obligatoires.';
        }
    }

    if ($action === 'delete_destination') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $req = $db->prepare('DELETE FROM destination WHERE id = ?');
            $req->execute([$id]);
            $message = 'Offre supprimée.';
        }
    }
}

// ---------- Donnees ----------
$locations = [];
$tourOperators = [];
$destinations = [];
if ($isAdmin) {
    $locations = $db->query('SELECT DISTINCT location FROM destination ORDER BY location')->fetchAll(PDO::FETCH_COLUMN);
    if (empty($locations)) {
        $locations = ['Rome', 'Londres', 'Monaco', 'Tunis', 'Tokyo', 'Marrakech', 'Bangkok', 'Madrid', 'Dubaï', 'Istanbul', 'Lisbonne'];
    }
    $tourOperators = $db->query('SELECT * FROM tour_operator ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    $destinations = $db->query('SELECT d.id, d.location, d.price, t.name AS to_name, t.id AS to_id
                                FROM destination d
                                JOIN tour_operator t ON t.id = d.tour_operator_id
                                ORDER BY d.location, d.price')->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ComparOperator - Administration">
    <title>ComparOperator - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-page">

<!-- Hero : meme plage que l'accueil -->
<header class="hero">
    <nav class="site-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="logo" href="../../index.php">ComparOperator</a>
            <div class="nav-menu">
                <a href="../../index.php">Accueil</a>
                <a href="../../comparateur.php">Destinations</a>
                <a href="index.php" class="active">Administration</a>
            </div>
        </div>
    </nav>

    <div class="container hero-content">
        <h1 class="hero-title">Bienvenu<br>Administrateur !</h1>
        <p class="hero-sub">Ici, vous pouvez gérer les tours opérateurs et les différentes destinations</p>

        <?php if ($isAdmin): ?>
            <div class="trust-row admin-pills">
                <a class="trust-item" href="#agences">Gestion des agences</a>
                <a class="trust-item" href="#destinations">Gestion des destinations</a>
                <a class="trust-item" href="#premium">Ajouter "l'étiquette" Premium</a>
            </div>
        <?php endif; ?>
    </div>
</header>

<div class="wave wave-bottom" style="background:#088395;">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
        <path d="M0,40 C360,0 720,80 1080,40 C1260,20 1380,60 1440,40 L1440,80 L0,80 Z" fill="#faf6ec"/>
    </svg>
</div>

<main id="contenu">

<?php if (!$isAdmin): ?>
    <!-- Connexion -->
    <section class="admin-section">
        <div class="admin-band"><h2>Connexion administrateur</h2></div>
        <div class="admin-card">
            <div style="max-width:520px; margin:0 auto;">
                <?php if ($authError !== ''): ?>
                    <div class="flash error"><?= htmlspecialchars($authError) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="admin-field">
                        <label for="login_password">Mot de passe</label>
                        <input type="password" id="login_password" name="login_password" required>
                    </div>
                    <button type="submit" class="btn-brand">Se connecter</button>
                </form>
                <p class="text-muted small mt-3">Mot de passe par défaut : <code>comparo</code></p>
            </div>
        </div>
    </section>
<?php else: ?>

    <?php if ($message !== ''): ?>
        <div class="flash"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Ajouter un tour-operateur -->
    <section class="admin-section">
        <div class="admin-band"><h2>Ajouter un tour-opérateur</h2></div>
        <div class="admin-card">
            <form method="post" class="admin-form">
                <input type="hidden" name="action" value="add_to">
                <div class="admin-field">
                    <label for="to_name">Nom du tour-opérateur</label>
                    <input type="text" id="to_name" name="name" required>
                </div>
                <div class="admin-field">
                    <label for="to_link">Lien du site de l'opérateur</label>
                    <input type="url" id="to_link" name="link" required>
                </div>
                <button type="submit" class="btn-brand">Ajouter l'opérateur</button>
            </form>
        </div>
    </section>

    <!-- Ajouter une destination -->
    <section class="admin-section">
        <div class="admin-band"><h2>Ajouter une destination</h2></div>
        <div class="admin-card">
            <form method="post" class="admin-form">
                <input type="hidden" name="action" value="add_destination">
                <div class="admin-field">
                    <label for="to_id">Tour-opérateur</label>
                    <select id="to_id" name="to_id" required>
                        <?php foreach ($tourOperators as $to): ?>
                            <option value="<?= (int)$to['id'] ?>"><?= htmlspecialchars($to['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field">
                    <label for="location">Destination</label>
                    <select id="location" name="location" required>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field">
                    <label for="price">Prix</label>
                    <input type="number" id="price" name="price" min="1" required>
                </div>
                <button type="submit" class="btn-brand">Ajouter la destination</button>
            </form>
        </div>
    </section>

    <!-- Gestion des agences -->
    <section class="admin-section" id="agences">
        <div class="admin-band"><h2>Gestion des agences</h2></div>
        <div class="admin-card">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Nom</th><th>Lien</th><th>Note</th><th>Premium</th></tr></thead>
                    <tbody>
                    <?php foreach ($tourOperators as $to): ?>
                        <?php $note = $to['grade_count'] > 0 ? $to['grade_total'] / $to['grade_count'] : 0; ?>
                        <tr>
                            <td><?= htmlspecialchars($to['name']) ?></td>
                            <td><a href="<?= htmlspecialchars($to['link']) ?>" target="_blank" rel="noopener">site officiel</a></td>
                            <td><?= number_format($note, 1) ?>/5 (<?= (int)$to['grade_count'] ?> note(s))</td>
                            <td><?= $to['is_premium'] ? 'Oui' : 'Non' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Gestion des destinations -->
    <section class="admin-section" id="destinations">
        <div class="admin-band"><h2>Gestion des destinations</h2></div>
        <div class="admin-card">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Destination</th><th>Tour-opérateur</th><th>Prix</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($destinations as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['location']) ?></td>
                            <td><?= htmlspecialchars($d['to_name']) ?></td>
                            <td><?= (int)$d['price'] ?> €</td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="delete_destination">
                                    <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                                    <button type="submit" class="btn-ghost btn-small" onclick="return confirm('Supprimer cette offre ?');">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Etiquette Premium -->
    <section class="admin-section" id="premium">
        <div class="admin-band"><h2>Ajouter "l'étiquette" Premium</h2></div>
        <div class="admin-card">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Tour-opérateur</th><th>Statut</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($tourOperators as $to): ?>
                        <tr>
                            <td><?= htmlspecialchars($to['name']) ?></td>
                            <td><?= $to['is_premium'] ? '<span class="badge-premium">PREMIUM</span>' : 'Standard' ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_premium">
                                    <input type="hidden" name="id" value="<?= (int)$to['id'] ?>">
                                    <button type="submit" class="btn-brand btn-small">
                                        <?= $to['is_premium'] ? 'Retirer premium' : 'Mettre premium' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="container py-3 text-end">
        <a href="index.php?logout=1" class="btn-ghost btn-small">Déconnexion</a>
    </div>

<?php endif; ?>

</main>
</body>
</html>
