<?php

namespace PrestaFaker\Webservice;

use Symfony\Component\EventDispatcher\EventDispatcher;

interface WebserviceInterface
{
    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher);

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = []);

    /**
     * @param string $object
     * @param string $resource
     * @param array $values
     * @return int|bool     the last id or false if failed
     */
    public function insert($object, $resource, array $values = []);

    /**
     * @param $image
     * @param $id
     * @param string $type
     * @return mixed
     */
    public function addImage($image, $id, $type = 'products');
}
