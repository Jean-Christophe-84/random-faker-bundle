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
Tu peux toutefois personnaliser certains comportements via les paramètres Symfony.

## Utilisation

### 1. Ajoute un attribut à ton entité

	// src/Entity/Product.php
	
	// ...
	use RandomFakerBundle\Attribute\FakerNumber;
	use RandomFakerBundle\Attribute\FakerOrder;
	use RandomFakerBundle\Attribute\AvailableFormatters\Person\FakerName;
	// ...
	
	// ...
	#[FakerNumber(number: 10)]
	#[FakerNumber(order: 10)]
	// ...
	class Product
	{
		// ...
		
		// ...
		#[FakerName]
		// ...
		public string $name;
		
		// ...
	}

FakerNumber : indique le nombre de lignes que l'on souhaite générer
<br/>
FakerOrder : indique l'ordre d'appel (comme la fonction "getOrder()" dans les fixtures)

=> Dans le cas où deux entités ont le même ordre, cela prendra les entités par ordre alphabétique
<br/>
=> Du moment que ces deux fonctions sont ajoutées à la classe, l'entité sera prise en compte

Faker... :
- Toutes les fonctions du bundle "fakerphp/faker" sont disponibles, il suffit de mettre "Faker" + "NomdeLaMethode" avec la première lettre en majusucle (FakerNumberBetween, FakerDateTime, etc...)
- Vous pouvez personnaliser chaque fonction comme celle du bundle en ajoutant des paramètres => FakerName(gender: "female")
   
Deux autres fonctions ont été ajoutées :
- FakerIgnore(ignore="true") => permet d'ignorer un champ. Il est possible de mettre le paramètre "ignore" à "false", mais cela n'aura aucune conséquence car cela permettra de ne pas ignorer le champ (fonction par défaut)
- FakerNullable(nullable="false") => particulièrement utile si un champ est nullable="true" mais que vous souhaitez absolument remplir ce champ à chaque fois. Dans le cas ou vous mettez la paramètre "nullable" à true, cela va mettre le champ "null" à chaque fois => équivalent à FakerIgnore(ignore: true) si le champ est nullable
	
Dans le cas où vous ne mettez rien sur le champ, il sera automatiquement rempli en fonction des paramètres du champ (voir la fonction "getRandomFaker" dans le helper)

Pour le champ généralement appelé "id", s'il possède l'attribut "#[ORM\GeneratedValue]", cela générera automatiquement un id. Il n'est donc pas possible de rajouter un attribut Faker.

<span style="color: red; font-size: 16px; font-weight: bold;">Attention : les champs "unique" ne sont pas gérés</span>
<br>
=> Si tel est le cas, je vous conseille d'ignorer ce champ et de le remplir ensuite (via une autre commande par exemple)

<span style="color: red; font-size: 16px; font-weight: bold;">Attention également aux associations</span>
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

	php bin/console attributes:fixtures:random

**Contributions bienvenues !**  
Si tu rencontres un bug ou veux proposer une amélioration, ouvre une issue ou une pull request.


