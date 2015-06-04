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
        'image' => 0,
    ];

    /**
     * @var array
     */
    private $images_paths = [
        'products' => [
            'path' => 'p',
            'object' => 'product',
        ]
    ];

    /**
     * @var array
     */
    private $image_positions = [];

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
        if (isset($this->images_paths[$type]) === false) {
            $this->dispatcher->dispatch(
                'ws.after.insertImageError',
                Listener::buildEvent('None path for '.$type, Logger::ERROR)
            );
            return false;
        }

        if (isset($this->image_positions[$type.'.'.$id]) === true) {
            $this->image_positions[$type.'.'.$id]++;
        } else {
            $this->image_positions[$type.'.'.$id] = 1;
        }

        $id_image = $this->insert('image', 'image', [
            'id_product' => $id,
            'legend' => '',
            'position' => $this->image_positions[$type.'.'.$id],
            'cover' => 1 === $this->image_positions[$type.'.'.$id] ? 1 : 0,
        ]);

        if (false === $id_image) {
            $this->dispatcher->dispatch(
                'ws.after.insertImageError',
                Listener::buildEvent('Fail to insert SQL, don\'t copy image', Logger::ERROR)
            );
            return false;
        }

        $path = $this->buildFolderForImage(
            $this->images_folder.DIRECTORY_SEPARATOR.$this->images_paths[$type]['path'],
            $id_image
        );

        if (is_dir($path) === false) {
            if (@mkdir($path, 0755, true) === false) {
                $this->dispatcher->dispatch(
                    'ws.after.insertImageError',
                    Listener::buildEvent('Impossible to create '.$path, Logger::ERROR)
                );
                return false;
            }
        }

        if (@copy($image, $path.DIRECTORY_SEPARATOR.$id_image.'.jpg') === false) {
            $this->dispatcher->dispatch(
                'ws.after.insertImageError',
                Listener::buildEvent('Fail to copy '.$image, Logger::ERROR)
            );
            return false;
        }

        $this->dispatcher->dispatch('ws.after.insertImageSuccess', Listener::buildEvent('Image for '.$type.' '.$id.' is added'));
        return true;
    }

    /**
     * Build the final path where store the image
     *
     * @param string $base_path
     * @param int $id
     * @return string
     */
    private function buildFolderForImage($base_path, $id)
    {
        $path = $base_path;
        $id = (string)$id;
        $id_length = strlen($id);

        for ($i = 0; $i < $id_length; $i++) {
            $path .= DIRECTORY_SEPARATOR.$id[$i];
        }

        return $path;
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
