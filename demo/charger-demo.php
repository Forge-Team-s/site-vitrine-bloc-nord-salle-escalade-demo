<?php

declare(strict_types=1);

/**
 * Charge le contenu de démonstration de Bloc Nord dans Cockpit : identité,
 * pages, menu, actualités et visuels provisoires.
 *
 * À lancer après bin/cockpit-init.php, sur une installation de démonstration.
 * Le contenu générique posé par cockpit-init (Atelier Bloom) est remplacé ;
 * ses pages et son actualité sont dépubliées, jamais effacées.
 *
 * Refuse de tourner sur un site qui porte un autre nom : il écraserait le
 * contenu d'un vrai client. Relancé sur la démo déjà chargée, il ne touche à
 * rien sans --reinitialiser.
 *
 * Usage : php demo/charger-demo.php [--reinitialiser]
 */

if (PHP_SAPI !== 'cli') {
    exit("Ce script s'exécute en ligne de commande.\n");
}

$root = dirname(__DIR__);
$adminDir = "{$root}/public/admin";

if (!is_file("{$adminDir}/bootstrap.php")) {
    fwrite(STDERR, "\n  Cockpit n'est pas installé. Lancer d'abord : php bin/install-cockpit.php\n\n");
    exit(1);
}

require "{$adminDir}/bootstrap.php";

/** @var Lime\App $app */
$app = Cockpit::instance($adminDir);

set_exception_handler(static function (Throwable $e): void {
    fwrite(STDERR, "\n  Échec : {$e->getMessage()}\n  {$e->getFile()}:{$e->getLine()}\n\n");
    exit(1);
});

function step(string $message): void
{
    echo "  {$message}\n";
}

echo "\nDémonstration Bloc Nord\n\n";

if (!$app->dataStorage->findOne('system/api_keys', ['name' => 'Site public'])) {
    fwrite(STDERR, "  Cockpit n'est pas initialisé. Lancer d'abord : php bin/cockpit-init.php\n\n");
    exit(1);
}

$content = $app->module('content');
$nomActuel = trim((string) ($content->item('settings')['nom'] ?? ''));
$reinitialiser = in_array('--reinitialiser', $argv, true);

if (!in_array($nomActuel, ['', 'Atelier Bloom', 'Bloc Nord'], true)) {
    fwrite(STDERR, "  Le site s'appelle « {$nomActuel} » : ce n'est pas une installation de démonstration.\n"
        ."  Rien n'a été modifié.\n\n");
    exit(1);
}

if ($nomActuel === 'Bloc Nord' && !$reinitialiser) {
    step('La démonstration est déjà chargée — inchangée.');
    step('Pour remettre le contenu de démonstration : php demo/charger-demo.php --reinitialiser');
    echo "\n";
    exit(0);
}

// 1. Visuels ----------------------------------------------------------------
//
// Provisoires : les vraies photos arrivent avant l'ouverture. Elles
// remplaceront celles-ci depuis l'administration, champ par champ.

$visuels = [
    'logo' => ['logo.webp', 'Bloc Nord — logo'],
    'bandeau' => ['bandeau-salle.webp', 'Bloc Nord — vue d’ensemble'],
    'bloc' => ['zone-bloc.webp', 'Bloc Nord — zone de bloc'],
    'cours' => ['cours-adultes.webp', 'Bloc Nord — cours adultes'],
    'detente' => ['espace-detente.webp', 'Bloc Nord — coin détente'],
    'chaussons' => ['location-chaussons.webp', 'Bloc Nord — location de chaussons'],
];

$images = [];

foreach ($visuels as $cle => [$fichier, $titre]) {

    // Retrouvé par son titre et son poids : un visuel retouché dans demo/images
    // est envoyé à nouveau, l'ancien reste dans la médiathèque.
    $existant = $app->dataStorage->findOne('assets', [
        'title' => $titre,
        'size' => filesize(__DIR__."/images/{$fichier}"),
    ]);

    if ($existant) {
        $images[$cle] = $existant;
        continue;
    }

    // L'envoi de Cockpit déplace le fichier : lui en confier une copie.
    $temporaire = $app->path('#tmp:').'/'.$fichier;
    copy(__DIR__."/images/{$fichier}", $temporaire);

    $resultat = $app->module('assets')->upload([
        'name' => [$fichier],
        'full_path' => [$fichier],
        'type' => ['image/webp'],
        'tmp_name' => [$temporaire],
        'error' => [0],
        'size' => [filesize($temporaire)],
    ], [], false);

    $asset = $resultat['assets'][0] ?? null;

    if (!is_array($asset)) {
        throw new RuntimeException("envoi de {$fichier} refusé par Cockpit.");
    }

    $asset['title'] = $titre;
    $app->dataStorage->save('assets', $asset);

    // Relu après l'envoi : les copies allégées y ont été ajoutées.
    $images[$cle] = $app->dataStorage->findOne('assets', ['_id' => $asset['_id']]) ?? $asset;
}

step('Visuels provisoires en place ('.count($images).').');

// 2. Identité ---------------------------------------------------------------

$content->saveItem('settings', array_merge($content->item('settings') ?? [], [
    'nom' => 'Bloc Nord',
    'slogan' => 'Salle d’escalade de bloc à Lomme',
    'description' => 'Salle d’escalade de bloc à Lomme, près de Lille : 900 m², 250 blocs, zone enfants '
        .'et cours. Tarifs, horaires et planning de la semaine.',
    'siret' => '',
    'logo' => $images['logo'],
    'imagePartage' => $images['bandeau'],
    // Les liens sont orange foncé : l'orange vif du logo n'atteint pas le
    // contraste minimal sur fond blanc. Il reste en fond, sous du texte noir.
    'couleurPrincipale' => '#C2410C',
    'couleurTexte' => '#111111',
    'email' => 'contact@blocnord.fr',
    'telephone' => '03 20 00 00 00',
    'adresse' => "Zone d’activités\n8 rue des Cheminots\n59160 Lomme",
    'horaires' => [
        'lundi' => '12h – 23h',
        'mardi' => '12h – 23h',
        'mercredi' => '12h – 23h',
        'jeudi' => '12h – 23h',
        'vendredi' => '12h – 23h',
        'samedi' => '10h – 20h',
        'dimanche' => '10h – 19h',
    ],
    'reseaux' => [
        ['nom' => 'Instagram', 'url' => 'https://www.instagram.com/blocnord/'],
    ],
]));

step('Identité du site renseignée.');

// 3. Pages ------------------------------------------------------------------

/**
 * Espaces insécables devant « € ? ! : » : un prix ou une question ne se
 * coupe pas en fin de ligne sur un téléphone.
 */
function typographie(mixed $valeur): mixed
{
    if (is_string($valeur)) {
        return preg_replace('/ ([€?!:])/u', "\u{00A0}$1", $valeur);
    }

    return is_array($valeur) ? array_map(typographie(...), $valeur) : $valeur;
}

/**
 * Crée la page, ou remplace celle qui porte déjà ce slug en gardant son
 * identifiant — le menu et les liens qui la visent restent valables.
 *
 * @param array<string, mixed> $page
 * @return array<string, mixed>
 */
function enregistrerPage(object $content, array $page): array
{
    $existante = $content->item('pages', ['slug' => $page['slug']]);

    if ($existante !== null) {
        $page['_id'] = $existante['_id'];
    }

    return $content->saveItem('pages', typographie($page) + ['_state' => 1]);
}

$annonceLancement = [
    'type' => 'annonce',
    'actif' => true,
    'ton' => 'offre',
    'titre' => 'Préinscriptions ouvertes',
    'message' => 'Abonnement annuel à 400 € au lieu de 440 € pour les 100 premiers inscrits.',
    'boutonTexte' => 'Se préinscrire',
    'boutonLien' => '/nous-contacter',
];

// Prête à servir : il suffit de la cocher et d'adapter la date.
$annonceFermeture = [
    'type' => 'annonce',
    'actif' => false,
    'ton' => 'alerte',
    'titre' => 'Fermeture exceptionnelle',
    'message' => 'La salle sera exceptionnellement fermée le 1er mai. Réouverture le lendemain aux horaires habituels.',
];

$tarifs = enregistrerPage($content, [
    'titre' => 'Tarifs',
    'slug' => 'tarifs',
    'seoTitre' => 'Tarifs de la salle d’escalade Bloc Nord à Lomme',
    'seoDescription' => 'Séance à 13 €, carte de 10 séances à 110 €, abonnement à 49 € par mois. '
        .'Tarifs étudiant et enfant, location de chaussons à 4 €.',
    'blocs' => [
        $annonceLancement,
        [
            'type' => 'tarifs',
            'titre' => 'Entrées et abonnements',
            'introduction' => 'Sans réservation, aux heures d’ouverture. Un prix par ligne, tout compris.',
            'lignes' => [
                ['libelle' => 'Séance simple', 'prix' => '13 €', 'precision' => '', 'miseEnAvant' => false],
                ['libelle' => 'Carte 10 séances', 'prix' => '110 €', 'precision' => 'soit 11 € la séance', 'miseEnAvant' => false],
                ['libelle' => 'Abonnement mensuel', 'prix' => '49 €', 'precision' => 'par mois', 'miseEnAvant' => true],
                ['libelle' => 'Abonnement annuel', 'prix' => '440 €', 'precision' => 'par an', 'miseEnAvant' => false],
                ['libelle' => 'Séance étudiant', 'prix' => '10 €', 'precision' => 'sur justificatif', 'miseEnAvant' => false],
                ['libelle' => 'Séance enfant', 'prix' => '8 €', 'precision' => 'moins de 14 ans', 'miseEnAvant' => false],
                ['libelle' => 'Location de chaussons', 'prix' => '4 €', 'precision' => 'par séance', 'miseEnAvant' => false],
            ],
            'remarque' => 'Les moins de 14 ans grimpent accompagnés d’un adulte.',
        ],
        [
            'type' => 'texte-image',
            'titre' => 'Pas de chaussons ? Pas de souci',
            'texte' => '<p>Des chaussons de toutes les pointures vous attendent à l’accueil, pour 4 € la '
                .'séance. Une tenue de sport suffit pour le reste : en bloc, ni baudrier ni corde.</p>'
                .'<p><a href="/premiere-visite">Préparer sa première visite</a></p>',
            'image' => $images['chaussons'],
            'alt' => 'Un membre de l’équipe tend une paire de chaussons d’escalade orange et turquoise au comptoir',
            'positionImage' => 'droite',
        ],
    ],
]);

enregistrerPage($content, [
    'titre' => 'Bloc Nord, salle d’escalade de bloc à Lomme',
    'slug' => 'accueil',
    'seoTitre' => 'Bloc Nord, salle d’escalade de bloc à Lomme',
    'seoDescription' => 'Salle d’escalade de bloc de 900 m² à Lomme, près de Lille. Séance à 13 €, '
        .'ouvert tous les jours, accès libre sans réservation. Ouverture début mars.',
    'blocs' => [
        [
            'type' => 'hero',
            'titre' => '',
            'accroche' => '900 m² de bloc, 250 blocs renouvelés toutes les trois semaines. Ouverture début mars.',
            'image' => $images['bandeau'],
            'alt' => 'La salle Bloc Nord : murs de bloc gris, noirs et orange sous une verrière, tapis de réception noirs',
            'boutonTexte' => 'Voir les tarifs',
            'boutonLien' => '/tarifs',
        ],
        $annonceLancement,
        $annonceFermeture,
        [
            'type' => 'tarifs',
            'titre' => 'Tarifs',
            // Les lignes viennent de la page Tarifs : une seule saisie.
            'reprendre' => ['_model' => 'pages', '_id' => $tarifs['_id']],
            'lignes' => [],
            'boutonTexte' => 'Détail des tarifs',
            'boutonLien' => '/tarifs',
        ],
        [
            'type' => 'essentiel',
            'titre' => 'Horaires et accès',
            'afficherHoraires' => true,
            'liens' => [
                ['texte' => 'Planning de la semaine', 'adresse' => '/planning'],
                ['texte' => 'Première visite', 'adresse' => '/premiere-visite'],
            ],
        ],
        [
            'type' => 'chiffres',
            'titre' => 'La salle en chiffres',
            'chiffres' => [
                ['valeur' => '900 m²', 'libelle' => 'de bloc'],
                ['valeur' => '250', 'libelle' => 'blocs'],
                ['valeur' => '14', 'libelle' => 'zones'],
                ['valeur' => '3 sem.', 'libelle' => 'entre deux rotations'],
            ],
        ],
        [
            'type' => 'texte-image',
            'titre' => 'Toujours du nouveau',
            'texte' => '<p>Dévers, dalle, toit : les blocs sont refaits par rotation toutes les trois semaines. '
                .'Une zone débutant aux blocs faciles et bien balisés, une zone enfants séparée, un espace '
                .'entraînement et un coin détente avec café.</p>'
                .'<p><a href="/la-salle">Découvrir la salle</a></p>',
            'image' => $images['bloc'],
            'alt' => 'Un grimpeur en pantalon orange, en appui sur une prise turquoise, au-dessus des tapis',
            'positionImage' => 'droite',
        ],
    ],
]);

enregistrerPage($content, [
    'titre' => 'Planning de la semaine',
    'slug' => 'planning',
    'seoTitre' => 'Planning : cours et accès libre — Bloc Nord',
    'seoDescription' => 'Cours enfants le mercredi et le samedi, cours adultes le mardi et le jeudi, '
        .'accès libre le reste du temps. Toute la semaine en un coup d’œil.',
    'blocs' => [
        $annonceFermeture,
        [
            'type' => 'planning',
            'titre' => 'Cette semaine à Bloc Nord',
            'introduction' => 'En dehors des créneaux en couleur, la salle est en accès libre, sans réservation.',
            'creneaux' => [
                ['jour' => 'mardi', 'debut' => '19h', 'fin' => '20h30', 'categorie' => 'adultes', 'libelle' => '', 'precision' => 'tous niveaux'],
                ['jour' => 'mercredi', 'debut' => '14h', 'fin' => '15h30', 'categorie' => 'enfants', 'libelle' => '', 'precision' => '6-9 ans'],
                ['jour' => 'mercredi', 'debut' => '16h', 'fin' => '17h30', 'categorie' => 'enfants', 'libelle' => '', 'precision' => '10-14 ans'],
                ['jour' => 'jeudi', 'debut' => '19h', 'fin' => '20h30', 'categorie' => 'adultes', 'libelle' => '', 'precision' => 'tous niveaux'],
                ['jour' => 'vendredi', 'debut' => '18h', 'fin' => '21h', 'categorie' => 'clubs', 'libelle' => 'Créneau clubs', 'precision' => 'salle ouverte, une partie réservée'],
                ['jour' => 'samedi', 'debut' => '10h', 'fin' => '11h30', 'categorie' => 'enfants', 'libelle' => '', 'precision' => '6-9 ans'],
            ],
            'note' => 'Le vendredi de 18h à 21h, la salle reste ouverte : une partie seulement est réservée aux clubs.',
        ],
        [
            'type' => 'texte-image',
            'titre' => 'Les cours',
            'texte' => '<p>Cours adultes tous niveaux le mardi et le jeudi de 19h à 20h30.</p>'
                .'<p>Cours enfants le mercredi, de 14h à 15h30 pour les 6-9 ans et de 16h à 17h30 pour les '
                .'10-14 ans, et le samedi de 10h à 11h30 pour les 6-9 ans.</p>'
                .'<p><a href="/nous-contacter">Nous écrire pour un cours</a></p>',
            'image' => $images['cours'],
            'alt' => 'Un moniteur montre une prise orange à deux grimpeurs attentifs au pied du mur',
            'positionImage' => 'gauche',
        ],
    ],
]);

enregistrerPage($content, [
    'titre' => 'La salle',
    'slug' => 'la-salle',
    'seoTitre' => 'La salle : 900 m² de bloc à Lomme — Bloc Nord',
    'seoDescription' => '250 blocs sur 14 zones, une zone débutant, une zone enfants sécurisée, un espace '
        .'entraînement, des vestiaires avec douches et un coin détente.',
    'blocs' => [
        [
            'type' => 'hero',
            'titre' => '',
            'accroche' => 'Dévers, dalle, toit : 250 blocs sur 14 zones, refaits par rotation toutes les trois semaines.',
            'image' => $images['bandeau'],
            'alt' => 'Vue d’ensemble de la salle : murs de bloc, tapis de réception et grandes baies vitrées',
        ],
        [
            'type' => 'chiffres',
            'titre' => 'En bref',
            'chiffres' => [
                ['valeur' => '900 m²', 'libelle' => 'de bloc'],
                ['valeur' => '250', 'libelle' => 'blocs'],
                ['valeur' => '14', 'libelle' => 'zones'],
                ['valeur' => '3 sem.', 'libelle' => 'entre deux rotations'],
            ],
        ],
        [
            'type' => 'espaces',
            'titre' => 'Les espaces',
            'introduction' => 'Photos provisoires : la salle est en travaux. Les vraies photos arrivent avant l’ouverture.',
            'cartes' => [
                [
                    'titre' => 'La zone de bloc',
                    'texte' => 'Dévers, dalle et toit, avec des blocs pour tous les niveaux. Rien à réserver : on vient quand on veut.',
                    'image' => $images['bloc'],
                    'alt' => 'Un grimpeur sur un mur gris et noir aux prises orange et turquoise',
                ],
                [
                    'titre' => 'La zone débutant',
                    'texte' => 'Des blocs faciles et bien balisés pour faire ses premiers pas.',
                    'image' => null,
                    'alt' => '',
                ],
                [
                    'titre' => 'La zone enfants',
                    'texte' => 'Séparée et sécurisée, avec un sol amortissant. À partir de 6 ans.',
                    'image' => null,
                    'alt' => '',
                ],
                [
                    'titre' => 'L’espace entraînement',
                    'texte' => 'Pan Güllich et poutres, pour progresser entre deux séances.',
                    'image' => null,
                    'alt' => '',
                ],
                [
                    'titre' => 'Les vestiaires',
                    'texte' => 'Douches et casiers.',
                    'image' => null,
                    'alt' => '',
                ],
                [
                    'titre' => 'Le coin détente',
                    'texte' => 'Café et canapés, avec vue sur les murs.',
                    'image' => $images['detente'],
                    'alt' => 'Canapés, coussins orange et deux cafés sur une table basse, devant la baie vitrée de la salle',
                ],
            ],
        ],
    ],
]);

enregistrerPage($content, [
    'titre' => 'Première visite',
    'slug' => 'premiere-visite',
    'seoTitre' => 'Première visite : ce qu’il faut savoir — Bloc Nord',
    'seoDescription' => 'Pas de réservation, une tenue de sport et des chaussons (location sur place). '
        .'À partir de 6 ans. Prévoir 10 minutes pour l’inscription.',
    'blocs' => [
        [
            'type' => 'questions',
            'titre' => 'Avant de venir',
            'questions' => [
                [
                    'question' => 'Faut-il réserver ?',
                    'reponse' => 'Non. On vient quand on veut, aux heures d’ouverture.',
                ],
                [
                    'question' => 'Que faut-il apporter ?',
                    'reponse' => 'Une tenue de sport et des chaussons d’escalade. Pas de chaussons ? Location sur place, 4 €. '
                        .'En bloc, ni baudrier ni corde.',
                ],
                [
                    'question' => 'À partir de quel âge ?',
                    'reponse' => 'Dès 6 ans. Les moins de 14 ans grimpent accompagnés d’un adulte.',
                ],
                [
                    'question' => 'Comment se passe l’arrivée ?',
                    'reponse' => 'Une décharge est à signer à la première visite, sur place. Prévoir 10 minutes pour l’inscription.',
                ],
                [
                    'question' => 'Je n’ai jamais grimpé.',
                    'reponse' => 'La zone débutant est faite pour vous : des blocs faciles et bien balisés. '
                        .'Les cours adultes du mardi et du jeudi sont ouverts à tous les niveaux.',
                ],
            ],
        ],
        [
            'type' => 'texte-image',
            'titre' => 'Location de chaussons',
            'texte' => '<p>Toutes les pointures, à l’accueil, pour 4 € la séance.</p>'
                .'<p><a href="/tarifs">Voir tous les tarifs</a></p>',
            'image' => $images['chaussons'],
            'alt' => 'Une paire de chaussons d’escalade tendue au-dessus du comptoir d’accueil',
            'positionImage' => 'droite',
        ],
        [
            'type' => 'essentiel',
            'titre' => 'Horaires et accès',
            'afficherHoraires' => true,
            'liens' => [
                ['texte' => 'Voir les tarifs', 'adresse' => '/tarifs'],
                ['texte' => 'Planning de la semaine', 'adresse' => '/planning'],
            ],
        ],
    ],
]);

enregistrerPage($content, [
    'titre' => 'Nous contacter',
    'slug' => 'nous-contacter',
    'seoTitre' => 'Contact et préinscription — Bloc Nord',
    'seoDescription' => 'Une question, une préinscription, un groupe ou un cours enfant ? '
        .'Écrivez-nous ou retrouvez-nous au 8 rue des Cheminots à Lomme.',
    'blocs' => [
        [
            'type' => 'formulaire',
            'titre' => 'Nous écrire',
            'texte' => '<p>Une question, une préinscription, une venue en groupe, un cours pour votre enfant ? '
                .'Écrivez-nous. Sur Instagram, nous sommes <a href="https://www.instagram.com/blocnord/">@blocnord</a>.</p>',
        ],
        [
            'type' => 'contact',
            'titre' => 'Nous trouver',
            'texte' => '<p>Dans la zone d’activités de Lomme.</p>',
            'afficherHoraires' => true,
        ],
    ],
]);

step('Pages créées : accueil, tarifs, planning, la salle, première visite, contact.');

// Les pages du contenu générique quittent le site sans être effacées.
foreach (['services', 'nous-ecrire'] as $slug) {

    $page = $content->item('pages', ['slug' => $slug]);

    if ($page !== null && (int) ($page['_state'] ?? 0) === 1) {
        $page['_state'] = 0;
        $content->saveItem('pages', $page);
        step("Page générique « {$page['titre']} » dépubliée.");
    }
}

// 4. Menu -------------------------------------------------------------------

$entrees = [];

foreach ([
    'tarifs' => 'Tarifs',
    'planning' => 'Planning',
    'la-salle' => 'La salle',
    'premiere-visite' => 'Première visite',
    'nous-contacter' => 'Contact',
] as $slug => $libelle) {

    $page = $content->item('pages', ['slug' => $slug]);

    if ($page !== null) {
        $entrees[] = ['libelle' => $libelle, 'page' => ['_model' => 'pages', '_id' => $page['_id']]];
    }
}

$content->saveItem('menu', array_merge($content->item('menu') ?? [], ['entrees' => $entrees]));

step('Menu renseigné.');

// 5. Mentions légales -------------------------------------------------------

$content->saveItem('legal', array_merge($content->item('legal') ?? [], [
    'directeurPublication' => 'À renseigner',
    'formeJuridique' => 'À renseigner',
    'tva' => '',
    'hebergeurNom' => 'À renseigner',
    'hebergeurAdresse' => "Nom, adresse postale et téléphone de l’hébergeur,\ntels qu’il les communique.",
    'dureeConservation' => '12 mois',
]));

step('Mentions légales pré-remplies — éditeur et hébergeur à compléter.');

// 6. Actualités -------------------------------------------------------------

$generique = $content->item('articles', ['slug' => 'portes-ouvertes-de-printemps']);

if ($generique !== null && (int) ($generique['_state'] ?? 0) === 1) {
    $generique['_state'] = 0;
    $content->saveItem('articles', $generique);
    step('Actualité générique dépubliée.');
}

$actualites = [
    [
        'titre' => 'Les préinscriptions sont ouvertes',
        'slug' => 'preinscriptions-ouvertes',
        'date' => date('Y-m-d'),
        'categorie' => 'actualite',
        'resume' => 'Abonnement annuel à 400 € au lieu de 440 € pour les 100 premiers inscrits, '
            .'avant l’ouverture de la salle début mars.',
        'image' => $images['bandeau'],
        'alt' => 'La salle Bloc Nord, ses murs de bloc et sa verrière',
        'contenu' => '<p>Bloc Nord ouvre début mars à Lomme. Pour les 100 premiers inscrits, l’abonnement '
            .'annuel passe à 400 € au lieu de 440 €.</p>'
            .'<h2>Comment se préinscrire</h2>'
            .'<p>Écrivez-nous depuis la page <a href="/nous-contacter">Nous contacter</a> : nous revenons vers '
            .'vous pour finaliser l’inscription.</p>',
    ],
    [
        'titre' => 'Ouverture début mars',
        'slug' => 'ouverture-debut-mars',
        'date' => date('Y-m-d', strtotime('-1 day')),
        'categorie' => 'evenement',
        'resume' => '900 m² de bloc, 250 blocs, une zone enfants et un coin détente : la salle ouvre ses portes '
            .'début mars, dans la zone d’activités de Lomme.',
        'image' => $images['bloc'],
        'alt' => 'Un grimpeur sur un bloc aux prises orange et turquoise',
        'contenu' => '<p>Les blocs sont en cours de pose. Nous annoncerons ici la date exacte de l’ouverture, '
            .'puis les compétitions, les ouvertures de nouveaux blocs et les soirées.</p>',
    ],
];

foreach ($actualites as $actualite) {

    $existante = $content->item('articles', ['slug' => $actualite['slug']]);

    if ($existante !== null) {
        $actualite['_id'] = $existante['_id'];
    }

    $content->saveItem('articles', typographie($actualite) + ['_state' => 1]);
}

step('Actualités publiées ('.count($actualites).').');

echo "\n  Site public : http://localhost:8080/\n\n";
