<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

PrestaFaker\Core\Config::add(array(
    'url' => 'http://localhost/prestashop156',
    'apikey' => '5VT7ZLPC97092JQ5MREQ2ETYL4Y799OI',
    'default_language' => 1,
    'init' => true,
    'init_categories' => true,
    'categories' => '\\PrestaFaker\\Faker\\Provider\\Car\\Category',
    'init_features' => true,
    'features' => array(
        // name => class
        'Marque' => '\\PrestaFaker\\Faker\\Provider\\Car\\Brand',
        'Modèle' => '\\PrestaFaker\\Faker\\Provider\\Car\\Model',
        'Motorisation' => '\\PrestaFaker\\Faker\\Provider\\Car\\Motor',
    ),
    'features_callback' => array(
        'Marque' => array(
            '\\PrestaFaker\\Callback\\RelationBrandModel',
            'getBrand'
        ),
        'Modèle' => array(
            '\\PrestaFaker\\Callback\\RelationBrandModel',
            'getModel'
        ),
    ),
    'product_faker' => '\\PrestaFaker\\Faker\\Provider\\Car\\Product',
    'image_faker' => '\\PrestaFaker\\Faker\\Provider\\Car\\Image',
    'nb_products' => 10000,
    'price_faker' => '\\Faker\\Provider\\Base',
    'text_faker' => '\\Faker\\Provider\\Lorem',
    'min_price' => 1,
    'max_price' => 1000,
    'log_filename' => dirname(__DIR__).DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'import.log',
    'images_dir' => dirname(__DIR__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'images',

    // Webservice
    // - Prestashop webservice
    /*'webservice' => [
        'class' => '\\PrestaFaker\\Webservice\\Prestashop',
        'options' => [
            'ws' => new \PrestaShopWebservice('http://localhost/prestashop156', '5VT7ZLPC97092JQ5MREQ2ETYL4Y799OI', false)
        ]
    ],*/
    // - SQL
    'webservice' => [
        'class' => '\\PrestaFaker\\Webservice\\Sql',
        'options' => [
            'filename' => dirname(__DIR__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'result.sql',
            'image_folder' => '',
            'table_prefix' => 'ps_', // not required
            'relations' => [
                'category' => '\\PrestaFaker\\Webservice\\Sql\\Category', // Caution : run "CALL repair_nested_tree(); after insert SQL"
                'product' => '\\PrestaFaker\\Webservice\\Sql\\Product',
                'product_feature' => '\\PrestaFaker\\Webservice\\Sql\\Feature',
                'product_feature_value' => '\\PrestaFaker\\Webservice\\Sql\\FeatureValue',
            ]
        ]
    ],
));
