<?php

declare(strict_types=1);

namespace Tests\Site;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * Les sections propres à Bloc Nord qui calculent quelque chose : le planning
 * déduit l'accès libre des horaires, l'accueil reprend les tarifs d'une autre
 * page, l'annonce se retire sans être effacée.
 */
final class SectionsBlocNordTest extends TestCase
{
    private const HORAIRES = [
        'lundi' => '12h – 23h',
        'mardi' => '12h – 23h',
        'mercredi' => '12h – 23h',
        'jeudi' => '12h – 23h',
        'vendredi' => '12h – 23h',
        'samedi' => '10h – 20h',
        'dimanche' => '',
    ];

    /** @param array<string, mixed> $bloc */
    private function rendre(string $type, array $bloc, string $titre = 'Titre'): string
    {
        $racine = dirname(__DIR__, 2);
        $twig = new Environment(new FilesystemLoader(["{$racine}/templates-client", "{$racine}/templates"]), [
            'strict_variables' => false,
            'autoescape' => 'html',
        ]);
        $twig->addFunction(new TwigFunction('image', static fn () => null));
        $twig->addFunction(new TwigFunction('tel_href', static fn (string $t) => $t));

        $html = $twig->render("blocs/{$type}.html.twig", [
            'bloc' => $bloc + ['type' => $type],
            'site' => ['horaires' => self::HORAIRES, 'adresse' => '8 rue des Cheminots'],
            'titre' => $titre,
            'niveau' => 2,
            'premier' => false,
        ]);

        // Le texte seul, espaces resserrés, pour comparer ce qui est lu.
        return trim((string) preg_replace('/\s+/u', ' ', strip_tags($html)));
    }

    #[Test]
    public function le_planning_remplit_les_heures_douverture_en_acces_libre(): void
    {
        $texte = $this->rendre('planning', ['creneaux' => [
            ['jour' => 'mercredi', 'debut' => '14h', 'fin' => '15h30', 'categorie' => 'enfants', 'precision' => '6-9 ans'],
        ]]);

        $this->assertStringContainsString('Mercredi 12h – 23h 12h – 14h Accès libre 14h – 15h30 Cours enfants 6-9 ans 15h30 – 23h Accès libre', $texte);
        $this->assertStringContainsString('Lundi 12h – 23h 12h – 23h Accès libre', $texte);
    }

    #[Test]
    public function un_jour_sans_horaires_est_annonce_ferme(): void
    {
        $texte = $this->rendre('planning', ['creneaux' => []]);

        $this->assertStringContainsString('Dimanche Fermé', $texte);
        $this->assertStringNotContainsString('Dimanche Fermé Accès libre', $texte);
    }

    #[Test]
    public function un_creneau_illisible_ou_a_lenvers_est_ecarte(): void
    {
        $texte = $this->rendre('planning', ['creneaux' => [
            ['jour' => 'mardi', 'debut' => 'soir', 'fin' => '21h', 'categorie' => 'adultes'],
            ['jour' => 'jeudi', 'debut' => '20h', 'fin' => '19h', 'categorie' => 'adultes'],
        ]]);

        $this->assertStringNotContainsString('Cours adultes', $texte);
    }

    #[Test]
    public function un_creneau_qui_deborde_est_ramene_aux_heures_douverture(): void
    {
        $texte = $this->rendre('planning', ['creneaux' => [
            ['jour' => 'samedi', 'debut' => '19h', 'fin' => '21h', 'categorie' => 'evenement', 'libelle' => 'Soirée'],
        ]]);

        $this->assertStringContainsString('19h – 20h Soirée', $texte);
    }

    #[Test]
    public function les_tarifs_peuvent_etre_repris_dune_autre_page(): void
    {
        $texte = $this->rendre('tarifs', [
            'lignes' => [['libelle' => 'Ignoré', 'prix' => '1 €']],
            'reprendre' => ['titre' => 'Tarifs', 'blocs' => [
                ['type' => 'texte-image'],
                ['type' => 'tarifs', 'lignes' => [['libelle' => 'Séance simple', 'prix' => '13 €']]],
            ]],
        ]);

        $this->assertStringContainsString('Séance simple 13 €', $texte);
        $this->assertStringNotContainsString('Ignoré', $texte);
    }

    #[Test]
    public function sans_page_liee_les_lignes_saisies_sont_affichees(): void
    {
        $texte = $this->rendre('tarifs', [
            'lignes' => [['libelle' => 'Séance simple', 'prix' => '13 €']],
            // Un lien que l'API n'a pas résolu : rien à reprendre.
            'reprendre' => ['_model' => 'pages', '_id' => 'abc'],
        ]);

        $this->assertStringContainsString('Séance simple 13 €', $texte);
    }

    #[Test]
    public function une_annonce_decochee_disparait(): void
    {
        $this->assertSame('', $this->rendre('annonce', ['actif' => false, 'message' => 'Fermé le 1er mai']));
        $this->assertStringContainsString('Fermé le 1er mai', $this->rendre('annonce', ['actif' => true, 'message' => 'Fermé le 1er mai']));
    }

    #[Test]
    public function lessentiel_regroupe_les_jours_aux_memes_horaires(): void
    {
        $texte = $this->rendre('essentiel', ['afficherHoraires' => true]);

        $this->assertStringContainsString('Lundi – vendredi 12h – 23h Samedi 10h – 20h Dimanche Fermé', $texte);
    }
}
