<?php

declare(strict_types=1);

/**
 * L'essentiel : horaires d'ouverture repris de l'identité du site,
 * adresse et liens.
 *
 * Gabarit : templates-client/blocs/essentiel.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'essentiel',
    'label' => 'L’essentiel : horaires et accès',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['afficherHoraires'],

    'champs' => [
        [
            'name' => 'liens',
            'type' => 'set',
            'label' => 'Liens',
            'info' => 'Affichés en boutons sous les horaires.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.texte || \'Lien\'}',
                'fields' => [
                    [
                        'name' => 'texte',
                        'type' => 'text',
                        'label' => 'Texte du lien',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => ['maxlength' => 40],
                    ],
                    [
                        'name' => 'adresse',
                        'type' => 'text',
                        'label' => 'Adresse',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => ['placeholder' => '/planning'],
                    ],
                ],
            ],
        ],
    ],
];
