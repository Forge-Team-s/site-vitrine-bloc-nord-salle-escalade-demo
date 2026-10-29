<?php

declare(strict_types=1);

/**
 * Niveaux : les couleurs des circuits de difficulté, du plus facile au plus
 * difficile, avec la cotation qu'elles recouvrent.
 *
 * La couleur se choisit dans une liste fermée : chacune a son texte noir ou
 * blanc, réglé pour rester lisible, ce qu'une couleur libre ne garantirait pas.
 *
 * Gabarit : templates-client/blocs/niveaux.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'niveaux',
    'label' => 'Niveaux de difficulté',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['introduction'],

    'champs' => [
        [
            'name' => 'niveaux',
            'type' => 'set',
            'label' => 'Circuits',
            'info' => 'Du plus facile au plus difficile.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.couleur || \'Couleur\'} ${data.cotation || \'\'}',
                'fields' => [
                    [
                        'name' => 'couleur',
                        'type' => 'select',
                        'label' => 'Couleur des prises',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => [
                            'options' => [
                                ['value' => 'jaune', 'label' => 'Jaune'],
                                ['value' => 'vert', 'label' => 'Vert'],
                                ['value' => 'bleu', 'label' => 'Bleu'],
                                ['value' => 'orange', 'label' => 'Orange'],
                                ['value' => 'rouge', 'label' => 'Rouge'],
                                ['value' => 'violet', 'label' => 'Violet'],
                                ['value' => 'noir', 'label' => 'Noir'],
                                ['value' => 'blanc', 'label' => 'Blanc'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'cotation',
                        'type' => 'text',
                        'label' => 'Cotation',
                        'info' => 'Par exemple « 4 à 5a ».',
                        'width' => '1-2',
                        'opts' => ['maxlength' => 20],
                    ],
                    [
                        'name' => 'description',
                        'type' => 'text',
                        'label' => 'Pour qui',
                        'width' => '1-1',
                        'opts' => ['maxlength' => 120],
                    ],
                ],
            ],
        ],
    ],
];
