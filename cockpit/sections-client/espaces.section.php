<?php

declare(strict_types=1);

/**
 * Espaces de la salle, une carte chacun, photo facultative.
 *
 * Gabarit : templates-client/blocs/espaces.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'espaces',
    'label' => 'Espaces de la salle',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['introduction'],

    'champs' => [
        [
            'name' => 'cartes',
            'type' => 'set',
            'label' => 'Espaces',
            'info' => 'Un espace par carte. La photo est facultative : elle se remplace à tout moment.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.titre || \'Espace\'}',
                'fields' => [
                    [
                        'name' => 'titre',
                        'type' => 'text',
                        'label' => 'Nom de l’espace',
                        'required' => true,
                        'width' => '1-1',
                        'opts' => ['maxlength' => 50],
                    ],
                    [
                        'name' => 'texte',
                        'type' => 'text',
                        'label' => 'Description',
                        'width' => '1-1',
                        'opts' => ['multiline' => true, 'maxlength' => 300],
                    ],
                    [
                        'name' => 'image',
                        'type' => 'asset',
                        'label' => 'Photo',
                        'width' => '1-2',
                        'opts' => ['filter' => ['type' => 'image']],
                    ],
                    [
                        'name' => 'alt',
                        'type' => 'text',
                        'label' => 'Description de la photo',
                        'info' => 'Obligatoire dès qu’une photo est choisie.',
                        'width' => '1-2',
                        'opts' => ['maxlength' => 150],
                    ],
                ],
            ],
        ],
    ],
];
