<?php
// Images des villes (fichiers locaux dans img/villes)
function ville_photo(string $location): ?string
{
    static $photos = [
        'Rome'      => 'rome.jpg',
        'Londres'   => 'londres.jpg',
        'Monaco'    => 'monaco.jpg',
        'Tunis'     => 'tunis.jpg',
        'Tokyo'     => 'tokyo.jpg',
        'Marrakech' => 'marrakech.jpg',
        'Bangkok'   => 'bangkok.jpg',
        'Madrid'    => 'madrid.png',
        'Dubaï'     => 'dubai.jpg',
        'Istanbul'  => 'istanbul.jpg',
        'Lisbonne'  => 'lisbonne.jpg',
    ];

    $fichier = $photos[$location] ?? null;

    // Si la ville n'est pas dans la liste, on cherche un fichier du meme nom
    if ($fichier === null) {
        $slug = strtolower($location);
        $slug = strtr($slug, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'ç' => 'c',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ÿ' => 'y',
            'œ' => 'oe', 'æ' => 'ae',
        ]);
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', $slug), '-');

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (is_file(__DIR__ . '/img/villes/' . $slug . '.' . $ext)) {
                $fichier = $slug . '.' . $ext;
                break;
            }
        }
    }

    return $fichier !== null ? 'img/villes/' . $fichier : null;
}
