<?php

declare(strict_types=1);

/**
 * Prochains événements : compétitions, ouvertures de nouveaux blocs, soirées.
 *
 * Un agenda court posé dans une page, à côté des actualités : la date se lit
 * d'abord, et un événement passé disparaît de lui-même.
 *
 * Gabarit : templates-client/blocs/evenements.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'evenements',
    'label' => 'Prochains événements',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['introduction', 'boutonTexte', 'boutonLien'],

    'champs' => [
        [
            'name' => 'evenements',
            'type' => 'set',
            'label' => 'Événements',
            'info' => 'Classés par date à l’affichage. Une fois la date passée, l’événement n’est plus montré.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.date || \'Date\'} ${data.titre || \'Événement\'}',
                'fields' => [
                    [
                        'name' => 'date',
                        'type' => 'date',
                        'label' => 'Date',
                        'required' => true,
                        'width' => '1-3',
                        'opts' => [],
                    ],
                    [
                        'name' => 'heure',
                        'type' => 'text',
                        'label' => 'Heure',
                        'width' => '1-3',
                        'opts' => ['placeholder' => '19h', 'maxlength' => 20],
                    ],
                    [
                        'name' => 'genre',
                        'type' => 'select',
                        'label' => 'Genre',
                        'width' => '1-3',
                        'opts' => [
                            'options' => [
                                ['value' => 'competition', 'label' => 'Compétition'],
                                ['value' => 'ouverture', 'label' => 'Nouveaux blocs'],
                                ['value' => 'soiree', 'label' => 'Soirée'],
                                ['value' => 'autre', 'label' => 'Autre'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'titre',
                        'type' => 'text',
                        'label' => 'Titre',
                        'required' => true,
                        'width' => '1-1',
                        'opts' => ['maxlength' => 80],
                    ],
                    [
                        'name' => 'description',
                        'type' => 'text',
                        'label' => 'Description',
                        'width' => '1-1',
                        'opts' => ['multiline' => true, 'maxlength' => 240],
                    ],
                    [
                        'name' => 'lien',
                        'type' => 'text',
                        'label' => 'Lien',
                        'info' => 'Facultatif : une actualité du site, une page d’inscription.',
                        'width' => '1-1',
                        'opts' => ['placeholder' => '/actualites/…'],
                    ],
                ],
            ],
        ],
    ],
];
