# Démonstration Bloc Nord

Contenu de démonstration du site de Bloc Nord, salle d'escalade de bloc à Lomme.

## Charger la démo

Après l'installation décrite dans le [README](../README.md) :

```bash
php bin/install-cockpit.php --force   # pose les sections propres au site
php bin/cockpit-init.php
php demo/charger-demo.php
composer serve-admin                  # http://localhost:8090
composer serve                        # http://localhost:8080
```

Le script remplace le contenu générique (Atelier Bloom) : identité, pages, menu, mentions
légales et actualités. Les pages et l'actualité génériques sont dépubliées, pas effacées.

Il refuse de tourner sur un site qui porte un autre nom, et ne touche à rien si la démo est
déjà chargée. `--reinitialiser` remet le contenu de démonstration.

## Pages

| Adresse | Contenu |
|---|---|
| `/` | photo, offre de lancement, tarifs, horaires, chiffres clés |
| `/tarifs` | les sept tarifs, location de chaussons |
| `/planning` | la semaine en un coup d'œil, cours et accès libre |
| `/la-salle` | chiffres, espaces |
| `/premiere-visite` | questions fréquentes, horaires |
| `/nous-contacter` | formulaire, coordonnées |

## Sections propres au site

Gabarits dans `templates-client/blocs/`, champs dans `cockpit/models/pages.model.php`, styles
dans `public/assets/css/client.css`.

| Section | Ce que le client modifie |
|---|---|
| Annonce | offre ou fermeture exceptionnelle ; « Afficher l'annonce » la retire sans l'effacer |
| Tarifs | une ligne par tarif ; l'accueil reprend les lignes de la page Tarifs, saisies une seule fois |
| Planning | les cours et créneaux réservés ; le reste des horaires d'ouverture s'affiche en accès libre |
| L'essentiel | horaires (repris de l'identité du site), adresse, liens |
| Chiffres clés | trois ou quatre chiffres |
| Espaces | une carte par espace, photo facultative |
| Questions fréquentes | questions et réponses |

Les horaires d'ouverture ne se saisissent qu'à un endroit, l'identité du site : le pied de
page, la section « L'essentiel » et le planning les reprennent.

## Visuels

Les cinq images de `demo/images/` sont provisoires : la salle n'est pas encore équipée. Les
vraies photos se remplacent depuis l'administration, image par image, sans toucher au site.

## Couleurs

L'orange vif du logo est trop clair pour porter un texte blanc ou servir de couleur de lien
sur fond blanc. Il sert de fond, toujours sous du texte noir (6,6:1). Les liens prennent
l'orange foncé `#C2410C` (5,2:1 sur blanc), saisi dans l'identité du site.

## À compléter avant la mise en ligne

- numéro de téléphone (provisoire : 03 20 00 00 00) et adresse e-mail ;
- logo, quand le fichier est disponible ;
- éditeur, forme juridique, SIRET et hébergeur dans les mentions légales ;
- planning définitif : un créneau adulte peut encore bouger.
