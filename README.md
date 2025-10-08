<h1 align="center">
RandomFakerBundle
</h1>
<div align="center">

[![Dependency](https://img.shields.io/badge/mysql-8.0-green.svg)](https://dev.mysql.com/doc/relnotes/mysql/8.0/en/)
[![Minimum PHP Version](https://img.shields.io/badge/php-8.4-green)](https://www.php.net/supported-versions.php)
[![Dependency](https://img.shields.io/badge/symfony-6.4-green.svg)](https://symfony.com/releases)

</div>

# Bundle Symfony pour les Fixtures avancées

Ce bundle permet de simplifier et d’automatiser la création de fixtures pour les entités Symfony grâce à des attributs personnalisées. Il offre la possibilité de déclencher la génération de fixtures depuis n’importe où dans ton application (controller, commande, script shell, etc...).

## Fonctionnalités

- **Attributs personnalisés** pour marquer les entités à inclure dans les fixtures.
- **Commande Symfony** pour générer et charger les fixtures à la demande.
- **Flexible** : déclenchement possible depuis un controller, une commande, ou même un script shell.
- **Facile à intégrer** dans tout projet Symfony.

## Installation

Ajoute le bundle à ton projet avec Composer :

	composer require jean-christophe-84/random-faker-bundle

Active le bundle dans `config/bundles.php` :

	// config/bundles.php
	return [
        // ...
        RandomFakerBundle\RandomFakerBundle::class => ['dev' => true, 'test' => true],
	];


## Configuration

Aucune configuration obligatoire n’est nécessaire.  
Tu peux toutefois personnaliser certains comportements via les paramètres du fichier YAML.

## Utilisation

### 1. Ajoute un fichier .yaml avec le même nom que ton entité et au même endroit que ton fichier .php

	// src/Entity/Product.yaml
    /** Nombre de lignes à générer (obligatoire) */
    records: 10
    /** Ordre d'appel (obligatoire : similaire à la fonction "getOrder()" dans les fixtures) */
    /** Dans le cas où deux entités ont le même ordre, cela prendra les entités par ordre alphabétique */
    order: 70
    fields:
      name: /** nom de la variable dans le fichier .php */
        name:  /** nom de la fonction dans la librairie FakerPHP */
      position:
        numberBetween:
          parameters: /** Possibilité d'ajouter des paramètres */
            /** Il faut absolument reprendre le nom de la variable dans la librairie FakerPHP */
            int1: 0
            int2: 2147483647
      extension:
        ignore: /** Il est possible d'ignore un champ (fonction supplémentaire) */
          parameters:
            ignore: true /** Pour cela, il faut mettre le paramètres à true */
    /** Il est possible de mettre le paramètre "ignore" à "false", mais cela n'aura aucune conséquence car cela permettra de ne pas ignorer le champ (fonction par défaut) */
      slug:
        nullable: /** Il est possible de forcer un champ à ne pas être nullable (fonction supplémentaire) */
          parameters:
            nullable: false /** Mettre à "false" permet de forcer son remplissage */
        slug: /** Il est possible d'ajouter plusieurs fonctions "FakerSlug" en l'occurence ici */

Toutes les fonctions du bundle "fakerphp/faker" sont disponibles

Dans le cas où vous ne mettez rien sur le champ, il sera automatiquement rempli en fonction des paramètres du champ (voir la fonction "getRandomFaker" dans le helper)

Pour le champ généralement appelé "id", s'il possède l'attribut "GeneratedValue", cela générera automatiquement un id. Il n'est donc pas possible de rajouter un attribut Faker.

<span style="color: red; font-size: 16px; font-weight: bold;">Attention aux "UniqueEntity"</span>
<br/>
Seuls les champs "unique" sont traités.
<br/>
Le "UniqueEntity" n'est pas pris en compte.
<br/>
Il est donc conseillé de les traiter à part.

<span style="color: red; font-size: 16px; font-weight: bold;">Attention aux associations</span>
<br/>
Seuls les champs "ManyToOne" sont traités.
<br/>
Il est donc impératif d'avoir créer l'entité lié avant l'entité courante
<br/>
=> Par exemple, si vous avez une entité "Produit" dans laquelle se trouve un champ "Fournisseur" en "ManyToOne", l'ordre mis dans l'entité "Fournisseur" doit être inférieur à celui dans l'entité "Produit"
<br/>
Pour les relations "OneToOne" et "ManyToMany", il est conseillé de les traiter à part.


### 2. Génère les fixtures

Utilise la commande Symfony fournie par le bundle pour générer et charger les fixtures :

	php bin/console fixtures:random

**Contributions bienvenues !**  
Si tu rencontres un bug ou veux proposer une amélioration, ouvre une issue ou une pull request.


