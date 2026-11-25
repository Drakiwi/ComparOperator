<?php

require_once 'database.php';
require_once 'Entities/review.php';

$reviews = Review::getAllByTourOperator($db, 2);

foreach ($reviews as $review) {

    echo $review->getAuthor() . '<br>';
    echo $review->getMessage() . '<hr>';

}