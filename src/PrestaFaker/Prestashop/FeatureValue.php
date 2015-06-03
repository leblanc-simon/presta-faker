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

use Monolog\Logger;
use PrestaFaker\Core\Listener;
use PrestaFaker\Faker\Provider\LevelInterface;
use PrestaFaker\Core\Config;
use PrestaFaker\Webservice\WebserviceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FeatureValue
{
    private $faker_provider = null;
    private $dispatcher = null;
    private $id = null;
    private $feature_values = array();

    public function __construct(LevelInterface $faker_provider, EventDispatcher $dispatcher)
    {
        $this->faker_provider = $faker_provider;
        $this->dispatcher = $dispatcher;
    }


    public function inject(WebserviceInterface $ws, $feature_name)
    {
        if ($this->createFeature($ws, $feature_name) === false) {
            throw new \RuntimeException('Impossible to insert the feature');
        }

        $alls = $this->faker_provider->getAll();
        $this->injectData($alls, $ws);
    }


    private function createFeature(WebserviceInterface $ws, $feature_name)
    {
        $this->dispatcher->dispatch('feature.before.createFeature', Listener::buildEvent('Begin createFeature'));
        $xml_datas = array(
            'name' => array(Config::get('default_language', 1) => $feature_name),
        );

        $id = $ws->insert('product_feature', 'product_features', $xml_datas);
        if ($id !== false) {
            $this->id = $id;
            $this->dispatcher->dispatch('feature.after.createFeature', Listener::buildEvent(sprintf('end createFeature %s', $id)));
            return true;
        }

        $this->dispatcher->dispatch('feature.after.createFeature', Listener::buildEvent('end createFeature with error', Logger::ERROR));
        return false;
    }


    private function injectData(array $datas, WebserviceInterface $ws)
    {
        $this->dispatcher->dispatch('feature.before.injectData', Listener::buildEvent('Begin injectData'));

        foreach ($datas as $key => $value) {
            $this->dispatcher->dispatch('feature.injectData.foreach', Listener::buildEvent(sprintf('injectData : %s', $value)));

            $xml_datas = array(
                'id_feature' => $this->id,
                'custom' => 0,
                'value' => array(Config::get('default_language', 1) => $value),
            );

            $id = $ws->insert('product_feature_value', 'product_feature_values', $xml_datas);
            if ($id !== false) {
                $this->feature_values[$value] = $id;
            } else {

            }
        }

        $this->dispatcher->dispatch('feature.after.injectData', Listener::buildEvent('End injectData'));
    }


    public function get()
    {
        return $this->feature_values;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProvider()
    {
        return $this->faker_provider;
    }

    public function getRandomId()
    {
        return $this->feature_values[$this->faker_provider->getFirst()];
    }
}