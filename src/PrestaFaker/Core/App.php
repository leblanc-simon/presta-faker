<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Core;

use Faker\Factory;
use Faker\Provider\Color;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PrestaFaker\Prestashop\Category;
use PrestaFaker\Prestashop\FeatureValue;
use PrestaFaker\Prestashop\Product;
use PrestaFaker\Webservice\WebserviceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class App
{
    /**
     * @var Factory
     */
    static private $faker;

    /**
     * @var WebserviceInterface
     */
    static private $ws;

    /**
     * @var EventDispatcher
     */
    static private $dispatcher;

    static private $prestashop_category = null;
    static private $prestashop_features = array();

    static public function run()
    {
        self::init();

        self::$dispatcher->dispatch('app.begin', Listener::buildEvent('Start application'));

        self::injectPrimaryData();
        self::injectProducts();

        self::$dispatcher->dispatch('app.end', Listener::buildEvent('End application'));
    }


    static private function init()
    {
        self::initLogger();
        self::initWebservice();
        self::initFaker();
    }

    static private function injectPrimaryData()
    {
        if (Config::get('init', true) === false) {
            return;
        }

        $class = Config::get('categories');
        self::$prestashop_category = new Category(new $class(self::$faker), self::$dispatcher);
        if (Config::get('init_categories', false) === true && Config::get('categories', null) !== null) {
            self::$prestashop_category->inject(self::$ws);
        }

        foreach (Config::get('features') as $name => $class) {
            self::$prestashop_features[$name] = new FeatureValue(new $class(self::$faker), self::$dispatcher);
            if (Config::get('init_features', false) === true && is_array(Config::get('features')) === true) {
                self::$prestashop_features[$name]->inject(self::$ws, $name);
            }
        }
    }

    static private function injectProducts()
    {
        $class = Config::get('product_faker');
        $product = new Product(new $class(self::$faker), self::$dispatcher);

        $text_class = Config::get('text_faker');
        $price_class = Config::get('price_faker');
        $image_class = Config::get('image_faker');

        $product->setCategory(self::$prestashop_category);
        $product->setFeatures(self::$prestashop_features);
        $product->setTextProvider(new $text_class(self::$faker));
        $product->setPriceProvider(new $price_class(self::$faker));
        $product->setImageProvider(new $image_class(self::$faker));

        $product->inject(self::$ws, Config::get('nb_products'));
    }

    static private function initLogger()
    {
        self::$dispatcher = new EventDispatcher();
        $logger = new Logger('prestafaker');
        $logger->pushHandler(new StreamHandler(Config::get('log_filename')));
        $listener = new Listener($logger);

        self::$dispatcher->addListener('ws.after.buildXml', array($listener, 'onWebservice'));
        self::$dispatcher->addListener('ws.after.buildSql', array($listener, 'onWebservice'));
        self::$dispatcher->addListener('ws.after.insertSuccess', array($listener, 'onWebservice'));
        self::$dispatcher->addListener('ws.after.insertError', array($listener, 'onWebservice'));
        self::$dispatcher->addListener('ws.after.insertImageSuccess', array($listener, 'onWebservice'));
        self::$dispatcher->addListener('ws.after.insertImageError', array($listener, 'onWebservice'));

        self::$dispatcher->addListener('category.before.injectData', array($listener, 'onCategory'));
        self::$dispatcher->addListener('category.injectData.foreach', array($listener, 'onCategory'));
        self::$dispatcher->addListener('category.after.injectData', array($listener, 'onCategory'));

        self::$dispatcher->addListener('feature.before.createFeature', array($listener, 'onFeature'));
        self::$dispatcher->addListener('feature.after.createFeature', array($listener, 'onFeature'));
        self::$dispatcher->addListener('feature.before.injectData', array($listener, 'onFeature'));
        self::$dispatcher->addListener('feature.injectData.foreach', array($listener, 'onFeature'));
        self::$dispatcher->addListener('feature.after.injectData', array($listener, 'onFeature'));

        self::$dispatcher->addListener('product.set.provider.product', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.set.provider.price', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.set.provider.text', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.set.provider.image', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.set.category', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.set.features', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.before.inject', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.after.inject', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.before.injectData', array($listener, 'onProduct'));
        self::$dispatcher->addListener('product.after.injectData', array($listener, 'onProduct'));

        self::$dispatcher->addListener('app.begin', array($listener, 'onApp'));
        self::$dispatcher->addListener('app.end', array($listener, 'onApp'));
    }

    static private function initWebservice()
    {
        $ws_parameters = Config::get('webservice');
        if (is_array($ws_parameters) === false) {
            throw new \RuntimeException('webservice configuration must be an array');
        }

        if (isset($ws_parameters['class']) === false) {
            throw new \RuntimeException('webservice configuration must contains class parameter');
        }

        $options = [];
        if (isset($ws_parameters['options']) === true) {
            $options = $ws_parameters['options'];
        }

        self::$ws = new $ws_parameters['class'](self::$dispatcher);
        self::$ws->setOptions($options);
    }

    static private function initFaker()
    {
        self::$faker = Factory::create();

        $class = Config::get('product_faker');

        self::$faker->addProvider(new Color(self::$faker));
        self::$faker->addProvider(new $class(self::$faker));

        foreach (Config::get('features') as $name => $class) {
            self::$faker->addProvider(new $class(self::$faker));
        }
    }
}