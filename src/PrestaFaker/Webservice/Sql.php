<?php

namespace PrestaFaker\Webservice;

use Monolog\Logger;
use PrestaFaker\Core\Listener;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Sql implements WebserviceInterface
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher = null;

    /**
     * @var string
     */
    private $sql_filename = null;

    /**
     * @var string
     */
    private $images_folder = null;

    /**
     * @var string
     */
    private $table_prefix = 'ps_';

    /**
     * @var array
     */
    private $class_relations = [];

    /**
     * @param array
     */
    private $ids = [
        'product' => 0,
        'category' => 2,
        'product_feature' => 0,
        'product_feature_value' => 0,
    ];

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function setOptions(array $options = [])
    {
        if (isset($options['filename']) === false) {
            throw new \RuntimeException('filename option is required');
        }

        if (isset($options['image_folder']) === false) {
            throw new \RuntimeException('image_folder option is required');
        }

        if (isset($options['relations']) === false || is_array($options['relations']) === false) {
            throw new \RuntimeException('relations option is required and must be an array');
        }

        if (isset($options['table_prefix']) === true) {
            $this->table_prefix = $options['table_prefix'];
        }

        $this->sql_filename = $options['filename'];
        $this->images_folder = $options['image_folder'];
        $this->class_relations = $options['relations'];
    }


    public function insert($object, $resource, array $values = [])
    {
        $id = ++$this->ids[$object];
        $sql = $this->buildSql($values, $object, $id);
        $this->dispatcher->dispatch('ws.after.buildSql', Listener::buildEvent($sql, Logger::DEBUG));

        try {
            $this->write($sql);
            $this->dispatcher->dispatch('ws.after.insertSuccess', Listener::buildEvent($id));
            return $id;
        } catch (\PrestaShopWebserviceException $e) {
            $this->dispatcher->dispatch('ws.after.insertError', Listener::buildEvent($e->getMessage(), Logger::ERROR));
            return false;
        }
    }


    public function addImage($image, $id, $type = 'products')
    {
        return true;
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

    /**
     * @param string $data
     * @throws \Exception
     */
    private function write($data)
    {
        if (empty($data) === true) {
            return;
        }

        if (file_put_contents($this->sql_filename, $data."\n", FILE_APPEND) === false) {
            throw new \Exception(sprintf('Impossible to write %s', $this->sql_filename));
        }
    }

    /**
     * @param array $values
     * @param string $object
     * @param string $id
     * @return string
     */
    private function buildSql(array $values, $object, $id)
    {
        if (isset($this->class_relations[$object]) === false) {
            $this->dispatcher->dispatch('ws.buildSql', Listener::buildEvent('None class to manage '.$object, Logger::ERROR));
            return '';
        }

        $object = new $this->class_relations[$object]($this->table_prefix, $this->dispatcher);

        return $object->build($id, $values);
    }
}
