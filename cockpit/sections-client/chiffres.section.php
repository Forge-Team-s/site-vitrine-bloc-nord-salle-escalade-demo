<?php

declare(strict_types=1);

/**
 * Chiffres clés, affichés en grand : surface, nombre de blocs, rotation.
 *
 * Gabarit : templates-client/blocs/chiffres.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'chiffres',
    'label' => 'Chiffres clés',

    'champs' => [
        [
            'name' => 'chiffres',
            'type' => 'set',
            'label' => 'Chiffres',
            'info' => 'Trois ou quatre chiffres, affichés en grand.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.valeur || \'Chiffre\'} ${data.libelle || \'\'}',
                'fields' => [
                    [
                        'name' => 'valeur',
                        'type' => 'text',
                        'label' => 'Valeur',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => ['placeholder' => '250', 'maxlength' => 12],
                    ],
                    [
                        'name' => 'libelle',
                        'type' => 'text',
                        'label' => 'Ce qu’elle compte',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => ['placeholder' => 'blocs', 'maxlength' => 40],
                    ],
                ],
            ],
        ],
    ],
];
