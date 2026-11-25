<?php

require_once 'database.php';
require_once 'Entities/destinations.php';

$destination = new Destination('Lisbonne', 1800, 2);

if ($destination->create($db)) {

    echo 'Destination ajoutée !';

} else {

    echo 'Erreur lors de l\'ajout.';

}