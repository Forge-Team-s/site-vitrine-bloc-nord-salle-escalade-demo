<?php

/**
 * Site pages.
 *
 * Fixed structure: the customer fills it in, never edits it. Installed to
 * public/admin/storage/content/ by bin/install-cockpit.php.
 *
 * A page is a list of blocks. One block type = one entry in the `type` list,
 * a few fields shown by a `condition`, and a Twig partial of the same name in
 * templates/blocs/. Adding a type is documented in the README.
 *
 * Publication is not a field: Cockpit tracks it natively on `_state`
 * (1 published, 0 unpublished, -1 archived) with its own button in the admin.
 * The public site only ever serves items with `_state` = 1.
 */

// Headings only, plus links and lists: the page title is the only level-one
// heading, and the toolbar must not offer anything that would break that.
$toolbar = 'format | link | listBullet listOrdered';

// Conditions are evaluated in the admin against the block being edited.
$isHero = "data.type === 'hero'";
$isTexteImage = "data.type === 'texte-image'";
$isContact = "data.type === 'contact'";
$isFormulaire = "data.type === 'formulaire'";
$isTemoignages = "data.type === 'temoignages'";
$hasImage = "['hero', 'texte-image'].includes(data.type)";
$hasTexte = "['texte-image', 'contact', 'formulaire'].includes(data.type)";

// Sections propres à Bloc Nord, rendues par templates-client/blocs/.
$isAnnonce = "data.type === 'annonce'";
$isTarifs = "data.type === 'tarifs'";
$isPlanning = "data.type === 'planning'";
$isEssentiel = "data.type === 'essentiel'";
$isChiffres = "data.type === 'chiffres'";
$isEspaces = "data.type === 'espaces'";
$isQuestions = "data.type === 'questions'";
$hasIntroduction = "['temoignages', 'tarifs', 'planning', 'espaces', 'questions'].includes(data.type)";
$hasBouton = "['hero', 'annonce', 'tarifs', 'planning'].includes(data.type)";
$hasNote = "['tarifs', 'planning'].includes(data.type)";
$hasHoraires = "['contact', 'essentiel'].includes(data.type)";

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
    'name' => 'pages',
    'label' => 'Pages',
    'info' => 'Les pages du site.',
    'type' => 'collection',
    'group' => null,
    'preview' => [],
    'meta' => [
        'unique' => ['slug'],
    ],
    '_created' => 1754179200,
    '_modified' => 1754352000,

    'fields' => [
        [
            'name' => 'titre',
            'type' => 'text',
            'label' => 'Titre de la page',
            'info' => 'Apparaît en titre principal en haut de la page et dans le menu.',
            'required' => true,
            'localize' => false,
            'multiple' => false,
            'group' => 'Contenu',
            'width' => '1-1',
            'opts' => [],
        ],
        [
            'name' => 'slug',
            'type' => 'text',
            'label' => 'Adresse de la page',
            'info' => "Termine l'adresse de la page : « services » donne /services. Lettres minuscules et tirets.",
            'required' => true,
            'localize' => false,
            'multiple' => false,
            'group' => 'Contenu',
            'width' => '1-1',
            'opts' => ['placeholder' => 'services'],
        ],

        // ── Blocs ─────────────────────────────────────────────────────────
        [
            'name' => 'blocs',
            'type' => 'set',
            'label' => 'Contenu de la page',
            'info' => 'Les sections affichées sous le titre, dans cet ordre.',
            'required' => false,
            'localize' => false,
            'multiple' => true,
            'group' => 'Contenu',
            'width' => '1-1',
            'opts' => [
                'display' => '${data.titre || data.type || \'Section\'}',
                'fields' => [
                    [
                        'name' => 'type',
                        'type' => 'select',
                        'label' => 'Type de section',
                        'required' => true,
                        'width' => '1-1',
                        'opts' => [
                            'options' => [
                                ['value' => 'hero', 'label' => 'Bandeau d’ouverture'],
                                ['value' => 'texte-image', 'label' => 'Texte et image'],
                                ['value' => 'contact', 'label' => 'Coordonnées'],
                                ['value' => 'formulaire', 'label' => 'Formulaire de contact'],
                                ['value' => 'temoignages', 'label' => 'Témoignages'],
                                ['value' => 'annonce', 'label' => 'Annonce (offre, fermeture exceptionnelle)'],
                                ['value' => 'tarifs', 'label' => 'Tarifs'],
                                ['value' => 'planning', 'label' => 'Planning de la semaine'],
                                ['value' => 'essentiel', 'label' => 'L’essentiel : horaires et accès'],
                                ['value' => 'chiffres', 'label' => 'Chiffres clés'],
                                ['value' => 'espaces', 'label' => 'Espaces de la salle'],
                                ['value' => 'questions', 'label' => 'Questions fréquentes'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'titre',
                        'type' => 'text',
                        'label' => 'Titre de la section',
                        'info' => 'Apparaît en tête de la section.',
                        'width' => '1-1',
                        'opts' => [],
                    ],
                    [
                        'name' => 'accroche',
                        'type' => 'text',
                        'label' => 'Accroche',
                        'info' => 'La phrase affichée sous le titre du bandeau.',
                        'width' => '1-1',
                        'condition' => $isHero,
                        'opts' => ['multiline' => true, 'maxlength' => 200],
                    ],
                    [
                        'name' => 'texte',
                        'type' => 'wysiwyg',
                        'label' => 'Texte',
                        'info' => 'Le corps de la section.',
                        'width' => '1-1',
                        'condition' => $hasTexte,
                        'opts' => ['toolbar' => $toolbar],
                    ],
                    [
                        'name' => 'image',
                        'type' => 'asset',
                        'label' => 'Image',
                        'info' => 'Apparaît dans la section. Format paysage conseillé.',
                        'width' => '1-2',
                        'condition' => $hasImage,
                        'opts' => ['filter' => ['type' => 'image']],
                    ],
                    [
                        'name' => 'alt',
                        'type' => 'text',
                        'label' => 'Description de l’image',
                        'info' => 'Lue à voix haute par les lecteurs d’écran, et affichée si l’image ne charge pas. Décrire ce que l’on voit.',
                        'width' => '1-2',
                        'condition' => $hasImage,
                        'opts' => ['maxlength' => 150],
                    ],
                    [
                        'name' => 'positionImage',
                        'type' => 'select',
                        'label' => 'Position de l’image',
                        'width' => '1-2',
                        'condition' => $isTexteImage,
                        'opts' => [
                            'options' => [
                                ['value' => 'droite', 'label' => 'À droite du texte'],
                                ['value' => 'gauche', 'label' => 'À gauche du texte'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'boutonTexte',
                        'type' => 'text',
                        'label' => 'Texte du bouton',
                        'info' => 'Laisser vide pour ne pas afficher de bouton.',
                        'width' => '1-2',
                        'condition' => $hasBouton,
                        'opts' => ['maxlength' => 40],
                    ],
                    [
                        'name' => 'boutonLien',
                        'type' => 'text',
                        'label' => 'Adresse du bouton',
                        'info' => 'Adresse d’une page du site, par exemple /services.',
                        'width' => '1-2',
                        'condition' => $hasBouton,
                        'opts' => ['placeholder' => '/services'],
                    ],
                    // ── Témoignages ─────────────────────────────────────
                    // Exemple commenté d'un type de section : voir le partial
                    // templates/blocs/temoignages.html.twig et
                    // docs/guide-integration.md.
                    [
                        'name' => 'introduction',
                        'type' => 'text',
                        'label' => 'Phrase d’introduction',
                        'info' => 'Apparaît sous le titre de la section.',
                        'width' => '1-1',
                        'condition' => $hasIntroduction,
                        'opts' => ['multiline' => true, 'maxlength' => 200],
                    ],
                    [
                        'name' => 'temoignages',
                        'type' => 'set',
                        'label' => 'Témoignages',
                        'info' => 'Chaque entrée devient un témoignage affiché dans la section.',
                        'multiple' => true,
                        'width' => '1-1',
                        'condition' => $isTemoignages,
                        'opts' => [
                            'display' => '${data.auteur || \'Témoignage\'}',
                            'fields' => [
                                [
                                    'name' => 'citation',
                                    'type' => 'text',
                                    'label' => 'Ce que dit la personne',
                                    'required' => true,
                                    'width' => '1-1',
                                    'opts' => ['multiline' => true, 'maxlength' => 400],
                                ],
                                [
                                    'name' => 'auteur',
                                    'type' => 'text',
                                    'label' => 'Nom',
                                    'width' => '1-2',
                                    'opts' => [],
                                ],
                                [
                                    'name' => 'fonction',
                                    'type' => 'text',
                                    'label' => 'Fonction ou ville',
                                    'info' => 'Affichée sous le nom, en plus discret.',
                                    'width' => '1-2',
                                    'opts' => [],
                                ],
                                [
                                    'name' => 'portrait',
                                    'type' => 'asset',
                                    'label' => 'Portrait',
                                    'info' => 'Facultatif. Image carrée conseillée.',
                                    'width' => '1-2',
                                    'opts' => ['filter' => ['type' => 'image']],
                                ],
                                [
                                    'name' => 'alt',
                                    'type' => 'text',
                                    'label' => 'Description du portrait',
                                    'info' => 'Obligatoire dès qu’un portrait est choisi.',
                                    'width' => '1-2',
                                    'opts' => ['maxlength' => 150],
                                ],
                            ],
                        ],
                    ],

                    [
                        'name' => 'afficherHoraires',
                        'type' => 'boolean',
                        'label' => 'Afficher les horaires',
                        'info' => 'Les horaires saisis dans « Identité du site » apparaissent sous les coordonnées.',
                        'width' => '1-1',
                        'condition' => $hasHoraires,
                        'opts' => ['default' => true],
                    ],

                    // ── Annonce ─────────────────────────────────────────
                    [
                        'name' => 'actif',
                        'type' => 'boolean',
                        'label' => 'Afficher l’annonce',
                        'info' => 'Décocher pour la retirer du site sans l’effacer : il suffira de la recocher la prochaine fois.',
                        'width' => '1-2',
                        'condition' => $isAnnonce,
                        'opts' => ['default' => true],
                    ],
                    [
                        'name' => 'ton',
                        'type' => 'select',
                        'label' => 'Genre d’annonce',
                        'width' => '1-2',
                        'condition' => $isAnnonce,
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
                        'condition' => $isAnnonce,
                        'opts' => ['multiline' => true, 'maxlength' => 220],
                    ],

                    // ── Tarifs ──────────────────────────────────────────
                    [
                        'name' => 'lignes',
                        'type' => 'set',
                        'label' => 'Lignes de tarif',
                        'info' => 'Une ligne par tarif, dans l’ordre d’affichage. Sept lignes au plus restent lisibles sur un téléphone.',
                        'multiple' => true,
                        'width' => '1-1',
                        'condition' => $isTarifs,
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
                        'condition' => $isTarifs,
                        'opts' => ['link' => 'pages', 'display' => '${data.titre || \'Page sans titre\'}'],
                    ],

                    // ── Planning ────────────────────────────────────────
                    [
                        'name' => 'creneaux',
                        'type' => 'set',
                        'label' => 'Créneaux de la semaine',
                        'info' => 'Cours et créneaux réservés. Le reste des horaires d’ouverture (« Identité du site ») '
                            .'s’affiche de lui-même en accès libre.',
                        'multiple' => true,
                        'width' => '1-1',
                        'condition' => $isPlanning,
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
                        'condition' => $hasNote,
                        'opts' => ['multiline' => true, 'maxlength' => 300],
                    ],

                    // ── L’essentiel ─────────────────────────────────────
                    [
                        'name' => 'liens',
                        'type' => 'set',
                        'label' => 'Liens',
                        'info' => 'Affichés en boutons sous les horaires.',
                        'multiple' => true,
                        'width' => '1-1',
                        'condition' => $isEssentiel,
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

                    // ── Chiffres clés ───────────────────────────────────
                    [
                        'name' => 'chiffres',
                        'type' => 'set',
                        'label' => 'Chiffres',
                        'info' => 'Trois ou quatre chiffres, affichés en grand.',
                        'multiple' => true,
                        'width' => '1-1',
                        'condition' => $isChiffres,
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

                    // ── Espaces ─────────────────────────────────────────
                    [
                        'name' => 'cartes',
                        'type' => 'set',
                        'label' => 'Espaces',
                        'info' => 'Un espace par carte. La photo est facultative : elle se remplace à tout moment.',
                        'multiple' => true,
                        'width' => '1-1',
                        'condition' => $isEspaces,
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

                    // ── Questions fréquentes ────────────────────────────
                    [
                        'name' => 'questions',
                        'type' => 'set',
                        'label' => 'Questions',
                        'multiple' => true,
                        'width' => '1-1',
                        'condition' => $isQuestions,
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
            ],
        ],

        // ── Référencement ─────────────────────────────────────────────────
        [
            'name' => 'seoTitre',
            'type' => 'text',
            'label' => 'Titre dans les résultats de recherche',
            'info' => "Apparaît en bleu dans Google et dans l'onglet du navigateur. Vide, le titre de la page est repris.",
            'required' => false,
            'localize' => false,
            'multiple' => false,
            'group' => 'Référencement',
            'width' => '1-1',
            'opts' => ['maxlength' => 60, 'showCount' => true],
        ],
        [
            'name' => 'seoDescription',
            'type' => 'text',
            'label' => 'Résumé dans les résultats de recherche',
            'info' => 'Apparaît sous le titre dans Google. Environ 155 caractères.',
            'required' => false,
            'localize' => false,
            'multiple' => false,
            'group' => 'Référencement',
            'width' => '1-1',
            'opts' => ['multiline' => true, 'maxlength' => 160, 'showCount' => true],
        ],
    ],
];
