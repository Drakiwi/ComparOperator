<?php

$dsn = "mysql:host=localhost;dbname=comparo;charset=utf8mb4";
$user = "root";
$password = "";

$db = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);