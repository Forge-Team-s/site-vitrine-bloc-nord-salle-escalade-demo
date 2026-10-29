<?php

declare(strict_types=1);

/**
 * Annonce : offre de lancement, fermeture exceptionnelle, changement
 * d'horaires. Se retire du site sans être effacée.
 *
 * Gabarit : templates-client/blocs/annonce.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'annonce',
    'label' => 'Annonce (offre, fermeture exceptionnelle)',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['boutonTexte', 'boutonLien'],

    'champs' => [
        [
            'name' => 'actif',
            'type' => 'boolean',
            'label' => 'Afficher l’annonce',
            'info' => 'Décocher pour la retirer du site sans l’effacer : il suffira de la recocher la prochaine fois.',
            'width' => '1-2',
            'opts' => ['default' => true],
        ],
        [
            'name' => 'ton',
            'type' => 'select',
            'label' => 'Genre d’annonce',
            'width' => '1-2',
            'opts' => [
                'options' => [
                    ['value' => 'offre', 'label' => 'Offre ou nouvelle (orange)'],
                    ['value' => 'alerte', 'label' => 'Fermeture ou changement d’horaires (noir)'],
                ],
            ],
        ],
        [
            'name' => 'message',
            'type' => 'text',
            'label' => 'Message',
            'info' => 'Une ou deux phrases. Exemple : « Salle fermée le 1er mai. »',
            'width' => '1-1',
            'opts' => ['multiline' => true, 'maxlength' => 220],
        ],
    ],
];
