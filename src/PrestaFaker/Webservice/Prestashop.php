<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Webservice;

use Monolog\Logger;
use PrestaFaker\Core\Listener;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Prestashop implements WebserviceInterface
{
    /**
     * @var \PrestaShopWebservice
     */
    private $ws = null;

    /**
     * @var EventDispatcher
     */
    private $dispatcher = null;

    /**
     * @var string
     */
    private $url = null;

    /**
     * @var string
     */
    private $key = null;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        $this->extractUrlAndKey();
    }

    public function setOptions(array $options = [])
    {
        if (isset($options['ws']) === false || ($options['ws'] instanceof \PrestaShopWebservice) === false) {
            throw new \RuntimeException('ws option is required');
        }

        $this->ws = $options['ws'];
        return $this;
    }

    public function insert($object, $resource, array $values = array())
    {
        $xml = $this->buildXml($values, $object);
        $this->dispatcher->dispatch('ws.after.buildXml', Listener::buildEvent($xml, Logger::DEBUG));

        $datas = array(
            'resource' => $resource,
            'postXml' => $xml,
        );

        try {
            $return = $this->ws->add($datas);
            $this->dispatcher->dispatch('ws.after.insertSuccess', Listener::buildEvent($return->$object->id));
            return $return->$object->id;
        } catch (\PrestaShopWebserviceException $e) {
            $this->dispatcher->dispatch('ws.after.insertError', Listener::buildEvent($e->getMessage(), Logger::ERROR));
            return false;
        }
    }


    public function addImage($image, $id, $type = 'products')
    {
        $curl = curl_init($this->url.'/api/images/'.$type.'/'.$id);

        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->key.':');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('image' => '@'.$image));
        curl_exec($curl);

        $infos = curl_getinfo($curl);

        curl_close($curl);

        if (isset($infos['http_code']) === true && 200 === $infos['http_code']) {
            $this->dispatcher->dispatch('ws.after.insertImageSuccess', Listener::buildEvent('Image for '.$type.' '.$id.' is added'));
            return true;
        }

        $this->dispatcher->dispatch('ws.after.insertImageError', Listener::buildEvent('Failed to insert Image for '.$type.' '.$id, Logger::ERROR));
        return false;
    }


    private function buildXml(array $values, $object = null)
    {
        return '<?xml version="1.0" ?><prestashop xmlns:xlink="http://www.w3.org/1999/xlink">'.$this->buildInternalXml($values, $object).'</prestashop>';
    }

    private function buildInternalXml(array $values, $object)
    {
        $xml = '';
        foreach ($values as $key => $value) {
            if (is_array($value) === true && 'associations' === $key) {
                $xml .= '<'.$key.'>';
                foreach ($value as $subkey => $value) {
                    $xml .= '<'.('category' !== $subkey ? $subkey : 'categorie').'s>';
                    foreach ($value as $subvalue) {
                        $xml .= $this->buildInternalXml($subvalue, $subkey);
                    }
                    $xml .= '</'.('category' !== $subkey ? $subkey : 'categorie').'s>';
                }
                $xml .= '</'.$key.'>';
            } elseif (is_array($value) === true && $this->isAssociativeArray($value) === true) {
                $xml .= '<'.$key.'s>'.$this->buildInternalXml($value, $key).'</'.$key.'s>';
            } elseif (is_array($value) === true && $this->isAssociativeArray($value) === false) {
                $xml .= '<'.$key.'>';
                foreach ($value as $id => $label) {
                    $xml .= '<language id="'.$id.'"><![CDATA['.$label.']]></language>';
                }
                $xml .= '</'.$key.'>';
            } else {
                $xml .= '<'.$key.'><![CDATA['.$value.']]></'.$key.'>';
            }
        }

        return '<'.$object.'>'.$xml.'</'.$object.'>';
    }


    private function isAssociativeArray(array $array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }


    private function extractUrlAndKey()
    {
        $reflection = new \ReflectionClass(get_class($this->ws));

        $url_property = $reflection->getProperty('url');
        $key_property = $reflection->getProperty('key');
        $url_property->setAccessible(true);
        $key_property->setAccessible(true);
        $this->url = $url_property->getValue($this->ws);
        $this->key = $key_property->getValue($this->ws);
    }
}