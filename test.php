<?php
// Petit test : affiche le tour operateur n°2 et ses destinations
require_once __DIR__ . '/database.php';

$reponse = $db->prepare('SELECT * FROM tour_operator WHERE id = ?');
$reponse->execute([2]);
$to = $reponse->fetch(PDO::FETCH_ASSOC);

if ($to) {
    echo '<h2>' . htmlspecialchars($to['name']) . '</h2>';

    $reponse = $db->prepare('SELECT * FROM destination WHERE tour_operator_id = ?');
    $reponse->execute([2]);
    foreach ($reponse->fetchAll(PDO::FETCH_ASSOC) as $d) {
        echo htmlspecialchars($d['location']) . ' - ' . $d['price'] . ' €<br>';
    }
}
