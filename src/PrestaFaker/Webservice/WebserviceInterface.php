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
