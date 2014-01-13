<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Prestashop;

use Faker\Provider\Base;
use Monolog\Logger;
use PrestaFaker\Core\Config;
use PrestaFaker\Core\Listener;
use PrestaFaker\Faker\Provider\IProduct;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Product
{
    private $faker_provider = null;
    private $dispatcher = null;
    private $category = null;
    private $features = array();
    private $price_provider = null;
    private $text_provider = null;
    private $image_provider = null;

    public function __construct(IProduct $faker_provider, EventDispatcher $dispatcher)
    {
        $this->faker_provider = $faker_provider;
        $this->dispatcher = $dispatcher;

        $this->dispatcher->dispatch('product.set.provider.product', Listener::buildEvent(get_class($faker_provider)));
    }

    public function setCategory(Category $category)
    {
        $this->dispatcher->dispatch('product.set.category', Listener::buildEvent('Setting category'));
        $this->category = $category;
    }

    public function setFeatures(array $features)
    {
        $this->dispatcher->dispatch('product.set.features', Listener::buildEvent(sprintf('Setting features with %d features', count($features))));
        $this->features = $features;
    }

    public function setPriceProvider(Base $price_provider)
    {
        $this->dispatcher->dispatch('product.set.provider.price', Listener::buildEvent(get_class($price_provider)));
        $this->price_provider = $price_provider;
    }

    public function setTextProvider(Base $text_provider)
    {
        $this->dispatcher->dispatch('product.set.provider.text', Listener::buildEvent(get_class($text_provider)));
        $this->text_provider = $text_provider;
    }

    public function setImageProvider(Base $image_provider)
    {
        $this->dispatcher->dispatch('product.set.provider.image', Listener::buildEvent(get_class($image_provider)));
        $this->image_provider = $image_provider;
    }

    public function inject(Webservice $ws, $nb_products)
    {
        $this->dispatcher->dispatch('product.before.inject', Listener::buildEvent(sprintf('inject %d products', $nb_products)));

        if (null === $this->category || null === $this->price_provider || null === $this->text_provider || null === $this->image_provider) {
            throw new \RuntimeException('Complete category_provider, price_provider, text_provider and image_provider');
        }

        // Init image provider
        $this->image_provider->init($this->category->getProvider());

        $reference_length = strlen((string)$nb_products);

        for ($i = 0; $i < $nb_products; $i++) {
            $this->injectData($ws, str_pad($i, $reference_length, '0', STR_PAD_LEFT));
        }

        $this->dispatcher->dispatch('product.after.inject', Listener::buildEvent('end inject'));
    }

    private function injectData(Webservice $ws, $reference)
    {
        $this->dispatcher->dispatch('product.before.injectData', Listener::buildEvent(sprintf('inject the product %s', $reference)));

        $title = $this->faker_provider->productName();
        $category = $this->category->getRandomId();

        $datas = array(
            'id_category_default' => $category,
            'id_shop_default' => 1,
            'reference' => $reference,
            'on_sale' => 0,
            'price' => $this->price_provider->randomFloat(2, Config::get('min_price', 1), Config::get('max_price', 1000)),
            'active' => 1,
            'available_for_order' => 1,
            'show_price' => 1,
            'meta_description' => array(Config::get('default_language', 1) => $this->text_provider->text(250)),
            'meta_keywords' => array(Config::get('default_language', 1) => $this->text_provider->words(5, true)),
            'meta_title' => array(Config::get('default_language', 1) => $title),
            'name' => array(Config::get('default_language', 1) => $title),
            'link_rewrite' => array(Config::get('default_language', 1) => Link::rewrite($title)),
            'description' => array(Config::get('default_language', 1) => $this->text_provider->text(700)),
            'associations' => array('category' => array(array('id' => $category))),
        );

        $features = array();
        $callbacks = Config::get('features_callback', array());
        foreach ($this->features as $name => $feature) {
            if (isset($callbacks[$name]) === true) {
                $id_feature_value = call_user_func($callbacks[$name], $feature);
            } else {
                $id_feature_value = $feature->getRandomId();
            }
            $features[] = array(
                'id' => $feature->getId(),
                'id_feature_value' => $id_feature_value,
            );
        }
        if (count($features) > 0) {
            $datas['associations']['product_feature'] = $features;
        }

        $id = $ws->insert('product', 'products', $datas);
        if (false === $id) {
            $this->dispatcher->dispatch('product.after.injectData', Listener::buildEvent('end inject with error', Logger::ERROR));
        }

        $image = $this->image_provider->image($this->category->getNameById($category));
        if (null !== $image) {
            $ws->addImage($image, $id);
        }

        $this->dispatcher->dispatch('product.after.injectData', Listener::buildEvent('end inject'));
    }
}