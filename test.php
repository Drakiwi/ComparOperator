<?php

require_once 'database.php';
require_once 'Entities/TourOperator.php';
require_once 'Entities/destinations.php';

$tourOperator = TourOperator::getById($db, 2);

if ($tourOperator) {

    echo '<h2>' . $tourOperator->getName() . '</h2>';

    $destinations = $tourOperator->getDestinations($db);

    foreach ($destinations as $destination) {

        echo $destination->getLocation() . ' - ';
        echo $destination->getPrice() . ' €<br>';

    }
}