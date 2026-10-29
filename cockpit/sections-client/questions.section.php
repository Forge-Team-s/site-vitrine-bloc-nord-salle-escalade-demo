<?php

declare(strict_types=1);

/**
 * Questions fréquentes, toutes ouvertes. Le socle livre depuis la 2.2.0
 * un type « faq » aux questions repliables : les deux cohabitent.
 *
 * Gabarit : templates-client/blocs/questions.html.twig. La condition qui
 * n'affiche ces champs que pour ce type est posée à l'installation.
 */

return [
    'type' => 'questions',
    'label' => 'Questions fréquentes (toutes ouvertes)',

    // Champs communs du tronc que ce type réutilise.
    'communs' => ['introduction'],

    'champs' => [
        [
            'name' => 'questions',
            'type' => 'set',
            'label' => 'Questions',
            'multiple' => true,
            'width' => '1-1',
            'opts' => [
                'display' => '${data.question || \'Question\'}',
                'fields' => [
                    [
                        'name' => 'question',
                        'type' => 'text',
                        'label' => 'Question',
                        'required' => true,
                        'width' => '1-1',
                        'opts' => ['maxlength' => 100],
                    ],
                    [
                        'name' => 'reponse',
                        'type' => 'text',
                        'label' => 'Réponse',
                        'required' => true,
                        'width' => '1-1',
                        'opts' => ['multiline' => true, 'maxlength' => 500],
                    ],
                ],
            ],
        ],
    ],
];
