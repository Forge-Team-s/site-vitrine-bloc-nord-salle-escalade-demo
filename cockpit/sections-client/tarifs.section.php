<?php

declare(strict_types=1);

/**
 * Tarifs : un intitulé, un prix par ligne. Les lignes peuvent être
 * reprises d'une autre page, pour ne saisir les prix qu'une fois.
 *
 * Gabarit : templates-client/blocs/tarifs.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'tarifs',
    'label' => 'Tarifs',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['introduction', 'boutonTexte', 'boutonLien'],

    'champs' => [
        [
            'name' => 'lignes',
            'type' => 'set',
            'label' => 'Lignes de tarif',
            'info' => 'Une ligne par tarif, dans l’ordre d’affichage. Sept lignes au plus restent lisibles sur un téléphone.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.libelle || \'Tarif\'} ${data.prix || \'\'}',
                'fields' => [
                    [
                        'name' => 'libelle',
                        'type' => 'text',
                        'label' => 'Intitulé',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => ['placeholder' => 'Séance simple', 'maxlength' => 40],
                    ],
                    [
                        'name' => 'prix',
                        'type' => 'text',
                        'label' => 'Prix',
                        'required' => true,
                        'width' => '1-2',
                        'opts' => ['placeholder' => '13 €', 'maxlength' => 20],
                    ],
                    [
                        'name' => 'precision',
                        'type' => 'text',
                        'label' => 'Précision',
                        'info' => 'Facultatif, affichée en petit sous l’intitulé : « sur justificatif », « par mois »…',
                        'width' => '1-2',
                        'opts' => ['maxlength' => 60],
                    ],
                    [
                        'name' => 'miseEnAvant',
                        'type' => 'boolean',
                        'label' => 'Mettre en avant',
                        'info' => 'Surligne la ligne en orange.',
                        'width' => '1-2',
                        'opts' => ['default' => false],
                    ],
                ],
            ],
        ],
        [
            'name' => 'reprendre',
            'type' => 'contentItemLink',
            'label' => 'Reprendre les tarifs d’une autre page',
            'info' => 'Facultatif. Choisir la page « Tarifs » pour afficher ici les mêmes lignes : '
                .'les prix ne se saisissent alors qu’à un seul endroit. Les lignes ci-dessus sont ignorées.',
            'width' => '1-1',
            'opts' => ['link' => 'pages', 'display' => '${data.titre || \'Page sans titre\'}'],
        ],
        [
            // « note » appartient au planning : deux champs du même nom au
            // même niveau n'en laisseraient qu'un.
            'name' => 'remarque',
            'type' => 'text',
            'label' => 'Remarque',
            'info' => 'Facultatif, affichée en petit sous les tarifs.',
            'width' => '1-1',
            'opts' => ['multiline' => true, 'maxlength' => 300],
        ],
    ],
];
