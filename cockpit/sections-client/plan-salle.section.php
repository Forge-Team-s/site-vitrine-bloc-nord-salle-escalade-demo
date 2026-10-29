<?php

declare(strict_types=1);

/**
 * Plan de la salle : une image du plan et la légende de ses repères.
 *
 * Les numéros sont dans l'image et dans la liste : la légende reste lisible
 * au lecteur d'écran, et sur un téléphone où le plan est petit.
 *
 * Gabarit : templates-client/blocs/plan-salle.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'plan-salle',
    'label' => 'Plan de la salle',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['introduction', 'image', 'alt'],

    'champs' => [
        [
            'name' => 'reperes',
            'type' => 'set',
            'label' => 'Repères du plan',
            'info' => 'Un repère par numéro ou par espace visible sur le plan, dans l’ordre de lecture.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.numero || \'\'} ${data.nom || \'Repère\'}',
                'fields' => [
                    [
                        'name' => 'numero',
                        'type' => 'text',
                        'label' => 'Numéro',
                        'info' => 'Tel qu’il figure sur le plan : « 1 », « 1-7 ». Vide pour un espace sans numéro.',
                        'width' => '1-2',
                        'opts' => ['maxlength' => 8],
                    ],
                    [
                        'name' => 'nom',
                        'type' => 'text',
                        'label' => 'Nom',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => ['maxlength' => 50],
                    ],
                    [
                        'name' => 'description',
                        'type' => 'text',
                        'label' => 'Description',
                        'width' => '1-1',
                        'opts' => ['maxlength' => 160],
                    ],
                ],
            ],
        ],
    ],
];
