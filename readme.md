# Site web dynamique sans base de données
Cet outil permet de créer un site web dynamique sans base de donnée. Il n'y a pas d'interface backoffice, mais l'administrateur peut directement écrire le contenu des pages en [HTML](https://developer.mozilla.org/fr/docs/Web/HTML/Reference) ou en [Markdown](https://fr.wikipedia.org/wiki/Markdown) et ajouter des variables au format [FrontMatter](https://yaml.org/spec/1.2/spec.html) dans des fichiers YAML.


## Comment ça marche
L'application scan récursivement le contenu du dossier "/site-content" et génère à la volé les url accessible via le navigateur. Par example le fichier /content/a-propos.html.yaml sera accessible avec l'URL : http://example.fr/a-propos.

Ainsi on peut créer autant de pages que l'on souhaite, on peut même gérer le multi-langue. Par contre il ne faut pas oublier que cet outils est pertinent pour des projets simples, car la lecture de fichiers n'est pas ce qu'il y a de plus performant.

Le contenu des fichiers (sous le FrontMatter) peut être rédigé en Markdown ou en html, le système parsera l'un ou l'autre en fonction de l'extension du fichier : nom-fichier.html.yaml, mon-fichier.markdown.yaml.

Il est très important de noter que LE NOM DES FICHIERS NE DOIT PAS CONTENIR DE CARACTÈRES SPÉCIAUX, D'ACCENTS, D'ESPACES, ET DOIT ÊTRE UNIQUE. Pas 2 fichiers dans le même répertoire avec des noms identique, remplacer les espaces par des tirets.


## Configuration
Il y a un peu de configuration à faire avec les variables d'environnement. Renommer le fichier ".env.example" en ".env" et remplir les clés pour le nom du site, les mails, etc.


## Structure
Le projet est composé d'un dossier "/site-structure", qui sert pour les éléments structurels du site. Il ne contient qu'un fichier YAML pour générer les menus, mais il pourrait contenir des blocs, ou d'autres élément qui serait communs à plusieurs pages. Pour ajouter un nouvel élément il faudra suivre l'exemple des menus et blocs ligne 33 du fichier "/public/index.php".


## Affichage et variables
L'arborescence de dossier est inspiré de Laravel avec le dossier /resources qui contient les assets à compiler et les vues qui sont au format [Blade](https://github.com/jenssegers/blade). Cette version allégée de Blade permet d'avoir les vues imbriqués, les boucles, les conditions... (voir la documentation) Par contre certaines fonctions avancées incluse grâce à Laravel ne sont pas disponible, il faudra les créer à la main dans le fichier /app/helpers.php.

### Variables
Toutes les variables déclarées en FrontMatter (entre les '---') seront disponible dans le template via l'instance de l'objet Page et la clé qui représente la variable:
```yaml
# yaml
---
title: Mon titre
header_title: Titre de l'entête
---
```
```html
<!-- html -->
<title>{{ $page->title }}</title>
<h1>{{ $page->header_title }}</h1>
```
Le contenu du fichier (sous le FrontMatter, après les '---') s'obtient avec la propriété "content", il faudra penser à échapper les caractères pour que html soit interprété :
```html
<!-- html -->
<div>{!! $page->content !!}</div>
```
Il n'y a pas de variables obligatoires, mais 3 variables seront utilisées si elles existent :
 - layout : pour indiquer qu'elle page utiliser pour le rendu (voir chapitre suivant)
 - meta_description : pour la balise meta description de la page (par défaut : APP_META_DESC du ficher .env)
 - title : pour la balise title de la page (par défaut APP_NAME du fichier .env)
Sinon le créateur du site est libre de créer autant de variables qu'il juge utiles.
   
### Affichage
Il y a 2 fichiers obligatoires, /resources/views/layouts/site.blade.php et /resources/views/pages/default.blade.php, qu'il ne faut pas renommer, mais dont le contenu est modifiable. le fichier default.blade.php est inclus dans site.blade.php. La variable "layout" dans le YAML permet d'utiliser un autre fichier, dans ce cas le contenu de la variable doit correspondre au nom du fichier sans extension qui est dans le dossier /resources/views/pages/ : example avec index.markdown.yaml qui contient "home" dans "layout" et /resources/views/pages/home.blade.php.

Le projet de base fournit 2 examples qui propose une solution pour traiter des contenus dynamique de type article de blog. Dans ce cas la page d'accueil affiche une liste de clients et chaque clients à une page.

Il est possible d'ajouter un fichier css et/ou js dans une page spécifique avec la commande spécifique :
```html
@push('css')
 <link rel="stylesheet" href="<?= asset('/css/test.css') ?>">
@endpush
```
Il sera ajouté automatiquement à la place de la commande @stack() dans le fichier site.blade.php

La fonction php asset() dans les fichiers Blade, permet de construire le chemin vers la ressource contenue dans le dossier "/public/". Blade permet d'utiliser les fonctions natives de PHP dans sa syntaxe en plus des fonctions personnalisées.

Le projet dispose d'un formulaire de contact basique avec validation des champs et message flash. Le formulaire se construit dynamiquement dans la vue grâce à la classe /app/FormBuilder, la validation des champs se trouve dans /app/ValidateForm et les messages d'erreurs dans /app/ValidationError. Les principaux champs HTML sont disponibles mais pas tous.


## Les menus et les blocs
On peut ajouter autant de menus et de blocs que nécessaire, ils seront disponibles dans les vues (layout ou page) avec cette variable :
```php
$structure['menus']['nom_du_menu'];
$structure['blocs']['nom_du_bloc'];
```

### Les menus
Il faut insérer les menus dans la variable "menus" du fichier YAML "/site-structure/menus.yaml", en respectant la syntaxe suivante (possibilité d'avoir des sous-menus) :
```yaml
# yaml
---
menus:
  nom_menu:
    Titre:
      link: /lien-cible
      target: _self # ou _blank ...
      title: Titre du lien

    Titre 2 avec sous menu:
      link:
      submenu:
        Sous menu 1:
          link: /elements/element-1
          target: _self
          title: Voir la page
  
  nom_menu_2:
    Titre 2:
      link: /lien-cible
       # ...
---
```

### Les blocs
Les blocs sont des éléments structurel du site qui sont communs à tout ou partie des pages, il est alors utile de pouvoir gérer leur contenu depuis un seul fichier : /site-structure/blocs.yaml. Contrairement au menu il n'y a pas de contrainte de syntaxe tant que le bloc est déclaré dans la variable "blocs:" et que la syntaxe YAML est respecté.


## Assets
Le projet est livré avec laravel-mix et une configuration prête à être lancé.
```shell
npm install && npm run watch
```
Lance un serveur de développement local avec le "hot reload", le navigateur se met à jours automatiquement dès qu'on modifie un fichier blade, scss, ou js.

Il est évidemment possible d'utiliser n'importe qu'elle autre outils de build.


## Helpers
Les fonctions du fichier /app/view_helpers.php seront disponible uniquemtn dans les pages (view), ne pas hésiter à en créer selon les besoins du projet.

## TODO
- [x] Utiliser Slim 4
- [x] Intégrer un formulaire de contact basique
- [x] Gestion des menus et des éléments de structure
- [x] Désactiver le message flash automatiquement ou avec la croix
- [x] Générer un sitemap automatiquement ? Pour des petits sites ce n'est pas indispensable, le faire manuellement n'est pas si long.
- [x] Mettre en place la redirection 404 et le debug
