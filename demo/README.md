# Démonstration Bloc Nord

Site de démonstration monté sur le socle : Bloc Nord, une salle d'escalade de bloc **fictive**
à Lomme. Le nom, les tarifs, les horaires, les événements et les coordonnées sont inventés ;
un bandeau le rappelle en tête de chaque page.

## Charger la démo

Après l'installation décrite dans le [README](../README.md) :

```bash
php bin/install-cockpit.php --force   # assemble les sections propres au site
php bin/cockpit-init.php
php demo/charger-demo.php
composer serve-admin                  # http://localhost:8090
composer serve                        # http://localhost:8080
```

Le script remplace le contenu générique (Atelier Bloom) : identité, logo, pages, menu,
mentions légales et actualités. Les pages et l'actualité génériques sont dépubliées, pas
effacées.

Il refuse de tourner sur un site qui porte un autre nom, et ne touche à rien si la démo est
déjà chargée. `--reinitialiser` remet le contenu de démonstration.

## Pages

| Adresse | Contenu |
|---|---|
| `/` | photo, offre de lancement, tarifs, horaires, prochains événements, chiffres clés |
| `/tarifs` | les sept tarifs, location de chaussons |
| `/planning` | la semaine en un coup d'œil, cours et accès libre |
| `/la-salle` | chiffres, espaces, plan de la salle |
| `/premiere-visite` | questions fréquentes, niveaux de difficulté, horaires |
| `/nous-contacter` | formulaire, coordonnées |

## Sections propres au site

Chaque type a son gabarit dans `templates-client/blocs/`, ses champs dans
`cockpit/sections-client/` (format du socle 2.2) et ses styles dans
`public/assets/css/client.css`. Aucune n'existe dans la démo Atelier de Bloom.

| Section | Ce que le client modifie |
|---|---|
| Annonce | offre ou fermeture exceptionnelle ; « Afficher l'annonce » la retire sans l'effacer |
| Tarifs | une ligne par tarif ; l'accueil reprend les lignes de la page Tarifs, saisies une seule fois |
| Planning | les cours et créneaux réservés ; le reste des horaires d'ouverture s'affiche en accès libre |
| L'essentiel | horaires (repris de l'identité du site), adresse, liens |
| Chiffres clés | trois ou quatre chiffres |
| Espaces | une carte par espace, photo facultative |
| Questions fréquentes | questions et réponses, toutes ouvertes |
| Plan de la salle | le plan en image et la légende de ses repères |
| Niveaux | les couleurs des circuits, de la plus facile à la plus difficile |
| Prochains événements | date, heure, genre ; un événement passé n'est plus affiché |

Les horaires d'ouverture ne se saisissent qu'à un endroit, l'identité du site : le pied de
page, la section « L'essentiel » et le planning les reprennent.

## Gabarits recopiés

`templates-client/base.html.twig` est une copie du gabarit du socle 2.2.1, avec deux ajouts : le
bandeau de démonstration (`templates-client/partials/bandeau-demo.html.twig`, à retirer d'une
ligne) et le logo de l'identité du site dans l'en-tête. Après une mise à jour du socle,
comparer avec `git diff vX.Y.Z -- templates/base.html.twig`.

## Visuels

| Fichiers | Origine |
|---|---|
| `bandeau-salle`, `zone-bloc`, `cours-adultes`, `espace-detente`, `location-chaussons` | illustrations fournies avec le brief |
| `logo`, `vestiaires`, `zone-entrainement`, `zone-enfants`, `zone-debutant`, `plan-salle` | dessinés pour la démo, sources vectorielles dans `demo/sources/` |

Tous se remplacent depuis l'administration, image par image, sans toucher au site.

## Couleurs

L'orange vif du logo est trop clair pour porter un texte blanc ou servir de couleur de lien
sur fond blanc. Il sert de fond, toujours sous du texte noir (6,6:1). Les liens prennent
l'orange foncé `#C2410C` (5,2:1 sur blanc), saisi dans l'identité du site.

## Mise à jour du socle, de 2.0.6 à 2.2.1

Chaque étape a sa branche et son commit, dans l'ordre :

| Branche | Étape |
|---|---|
| `maj-socle-2.0.6` | première fusion : `git merge v2.0.6 --allow-unrelated-histories`, version du site gardée sur les conflits |
| `maj-socle-2.1.0` | fusion manuelle de `v2.1.0`, qui apporte `bin/maj-socle.php` |
| `maj-socle-2.2.0` | `php bin/maj-socle.php --vers=2.2.0` : arrivée de `cockpit/sections-client/` |
| `maj-socle-2.2.1` | `php bin/maj-socle.php --vers=2.2.1` |
| `section-annonce` … `section-planning` | un type par commit, de `pages.model.php` vers `cockpit/sections-client/` |

Après le dernier, `cockpit/models/pages.model.php` est identique à celui du socle : les
prochaines mises à jour ne peuvent plus y produire de conflit.
