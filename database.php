<?php
// Connexion a la base de donnees (WAMP : root sans mot de passe)
$dsn = "mysql:host=localhost;dbname=comparo_simple;charset=utf8mb4";
$user = "root";
$password = "";

$db = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);