<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Callback;

use PrestaFaker\Prestashop\FeatureValue;

class RelationBrandModel
{
    static private $brand = null;

    static public function getBrand(FeatureValue $object)
    {
        self::$brand = $object->getProvider()->getFirst();

        return $object->get()[self::$brand];
    }

    static public function getModel(FeatureValue $object)
    {
        $model = $object->getProvider()->getFirstByBrand(self::$brand);

        return $object->get()[$model];
    }
}