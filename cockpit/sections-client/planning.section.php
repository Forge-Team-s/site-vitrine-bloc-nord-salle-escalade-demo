<?php

declare(strict_types=1);

/**
 * Planning de la semaine : seuls les cours et créneaux réservés se
 * saisissent, le reste des horaires d'ouverture s'affiche en accès libre.
 *
 * Gabarit : templates-client/blocs/planning.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

$jours = [
    ['value' => 'lundi', 'label' => 'Lundi'],
    ['value' => 'mardi', 'label' => 'Mardi'],
    ['value' => 'mercredi', 'label' => 'Mercredi'],
    ['value' => 'jeudi', 'label' => 'Jeudi'],
    ['value' => 'vendredi', 'label' => 'Vendredi'],
    ['value' => 'samedi', 'label' => 'Samedi'],
    ['value' => 'dimanche', 'label' => 'Dimanche'],
];

return [
    'type' => 'planning',
    'label' => 'Planning de la semaine',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['introduction', 'boutonTexte', 'boutonLien'],

    'champs' => [
        [
            'name' => 'creneaux',
            'type' => 'set',
            'label' => 'Créneaux de la semaine',
            'info' => 'Cours et créneaux réservés. Le reste des horaires d’ouverture (« Identité du site ») '
                .'s’affiche de lui-même en accès libre.',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.jour || \'Jour\'} ${data.debut || \'\'} ${data.libelle || data.categorie || \'Créneau\'}',
                'fields' => [
                    [
                        'name' => 'jour',
                        'type' => 'select',
                        'label' => 'Jour',
                        'required' => true,
                        'width' => '1-3',
                        'opts' => ['options' => $jours],
                    ],
                    [
                        'name' => 'debut',
                        'type' => 'text',
                        'label' => 'Début',
                        'required' => true,
                        'info' => 'Par exemple 14h ou 15h30.',
                        'width' => '1-3',
                        'opts' => ['placeholder' => '14h', 'maxlength' => 5],
                    ],
                    [
                        'name' => 'fin',
                        'type' => 'text',
                        'label' => 'Fin',
                        'required' => true,
                        'width' => '1-3',
                        'opts' => ['placeholder' => '15h30', 'maxlength' => 5],
                    ],
                    [
                        'name' => 'categorie',
                        'type' => 'select',
                        'label' => 'Genre de créneau',
                        'info' => 'Donne la couleur du créneau.',
                        'required' => true,
                        'width' => '1-3',
                        'opts' => [
                            'options' => [
                                ['value' => 'enfants', 'label' => 'Cours enfants'],
                                ['value' => 'adultes', 'label' => 'Cours adultes'],
                                ['value' => 'clubs', 'label' => 'Créneau réservé'],
                                ['value' => 'evenement', 'label' => 'Événement'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'libelle',
                        'type' => 'text',
                        'label' => 'Intitulé',
                        'info' => 'Vide, le genre de créneau est repris.',
                        'width' => '1-3',
                        'opts' => ['maxlength' => 40],
                    ],
                    [
                        'name' => 'precision',
                        'type' => 'text',
                        'label' => 'Précision',
                        'info' => 'Âge, niveau : « 6-9 ans », « tous niveaux ».',
                        'width' => '1-3',
                        'opts' => ['maxlength' => 40],
                    ],
                ],
            ],
        ],

        [
            'name' => 'note',
            'type' => 'text',
            'label' => 'Note',
            'info' => 'Facultatif, affichée en petit sous la section.',
            'width' => '1-1',
            'opts' => ['multiline' => true, 'maxlength' => 300],
        ],
    ],
];
