PrestaFaker
===========

Utilitaire permettant d'insérer un grand nombre de produit aléatoirement dans Prestashop. [![SensioLabsInsight](https://insight.sensiolabs.com/projects/551e88fd-ed00-457b-8e29-b9e7c0a7efee/mini.png)](https://insight.sensiolabs.com/projects/551e88fd-ed00-457b-8e29-b9e7c0a7efee)

Utilisation
-----------

### TL;DR

* Installer les dépendances avec Composer.
* Modifier le fichier de configuration pour indiquer le nombre de produit à insérer et modifier les classes Faker permettant de générer les catégories, caractéristiques, produits, images, ...
* Puis lancer la tâche bin/run.php pour insérer l'ensemble des données.

### Version longue

#### Cloner le projet

```bash
git clone https://github.com/leblanc-simon/presta-faker.git
```

#### Copier le fichier de configuration et le modifier

```bash
cd presta-faker
cp config/config.php.dist config/config.php
```

Dans ce fichier, les données intéressantes sont :

* nb_products : Le nombre de produit à créer
* webservice : Permet d'indiquer si vous souhaitez insérer les produits via le webservice de Prestashop (très, très, très long)
  ou générer un fichier SQL et le dossier d'images associées qu'il vous restera à copier dans votre instance
* categories, features, features_callback, product_faker, image_faker sont les éléments de configuration qui vous permettront
  d'insérer les produits, catégories de votre choix (actuellement, PrestaFaker insére des produits relatif à l'automobile.)

Attention, si vous utilisez le "webservice" SQL, *après* l'insertion du fichier SQL, il faut exécuter la commande SQL suivante :

```sql
CALL repair_nested_tree();
```

Cette procédure permet de rétablir l'arbre des catégories et est créée lors de l'insertion du fichier SQL généré.

#### Lancer le programme

```bash
php bin/run.php
```

#### Cas du SQL

Une fois la tâche finie, vous pouvez :

* insérer la base de données : ```mysql -u root -p -D [database] < data/result.sql```
* copier les images : ```mv data/img/p/* [your prestashop directory]/img/p/```
* générer les miniatures des images (cela génére des liens symboliques) :  ```bin/img_presta_th.sh [your prestashop directory]/img/p/```


Remerciements
-------------

* [Faker](https://github.com/fzaninotto/Faker)
* [Symfony](http://symfony.com/)
* [Prestashop](http://www.prestashop.com/fr/)
* [Monolog](https://github.com/Seldaek/monolog)
* [Composer](https://getcomposer.org/)

Auteurs
-------

* Simon Leblanc : contact@leblanc-simon.eu

Licence
-------

[MIT](http://opensource.org/licenses/MIT)
