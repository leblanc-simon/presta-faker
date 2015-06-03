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

use PrestaFaker\Core\Config;
use PrestaFaker\Core\Listener;
use PrestaFaker\Faker\Provider\LevelInterface;
use PrestaFaker\Webservice\WebserviceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Category
{
    private $faker_provider = null;
    private $dispatcher = null;
    private $categories = array();

    public function __construct(LevelInterface $faker_provider, EventDispatcher $dispatcher)
    {
        $this->faker_provider = $faker_provider;
        $this->dispatcher = $dispatcher;
    }


    public function inject(WebserviceInterface $ws)
    {
        $alls = $this->faker_provider->getAll();

        $this->injectData($alls, $ws);
    }


    private function injectData(array $datas, WebserviceInterface $ws, $parent = 2)
    {
        $this->dispatcher->dispatch('category.before.injectData', Listener::buildEvent('Begin injectData'));

        foreach ($datas as $key => $value) {
            $this->dispatcher->dispatch('category.injectData.foreach', Listener::buildEvent(sprintf('inject %s', $key)));

            if (is_array($value) === true) {
                $name = $key;
            } else {
                $name = $value;
            }

            $xml_datas = array(
                'id_parent' => $parent,
                'active' => 1,
                'is_root_category' => $parent === 2 ? 1 : 0,
                'name' => array(Config::get('default_language', 1) => $name),
                'link_rewrite' => array(Config::get('default_language', 1) => Link::rewrite($name)),
            );

            $id = $ws->insert('category', 'categories', $xml_datas);
            if ($id !== false) {
                $this->categories[$name] = $id;
            }

            if (is_array($value) === true) {
                $this->injectData($value, $ws, $id);
            }
        }

        $this->dispatcher->dispatch('category.after.injectData', Listener::buildEvent('End injectData'));
    }


    public function get()
    {
        return $this->categories;
    }


    public function getNameById($id)
    {
        return array_search($id, $this->categories);
    }


    public function getRandomId()
    {
        static $method = null;

        if (null === $method) {
            $level = $this->faker_provider->getMaxLevel();
            switch ($level) {
                case 1:
                    $method = 'getFirst';
                    break;
                case 2:
                    $method = 'getSecond';
                    break;
                case 3:
                    $method = 'getThird';
                    break;
                default:
                    $method = 'getThird';
            }
        }

        return $this->categories[$this->faker_provider->$method()];
    }


    public function getProvider()
    {
        return $this->faker_provider;
    }
}