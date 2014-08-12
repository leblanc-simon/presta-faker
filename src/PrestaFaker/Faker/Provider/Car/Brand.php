<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Faker\Provider\Car;

use Faker\Provider\Base;
use PrestaFaker\Faker\Provider\LevelInterface;

class Brand
    extends Base
    implements LevelInterface
{
    static protected $brands = array(
        // English
        'Aston Martin',
        'Bentley',
        'Jaguar',
        // French
        'Citroën',
        'Peugeot',
        'Renault',
        // German
        'Audi',
        'BMW',
        'Mercedez',
        'Opel',
        'Volkswagen',
        // Italian
        'Fiat',
        'Alfa Romeo',
        // Sweden
        'Saab',
    );

    public function getAll()
    {
        return static::$brands;
    }

    public function getMaxLevel()
    {
        return 1;
    }

    public function getFirst()
    {
        return static::randomElement(static::$brands);
    }

    public function brand()
    {
        return $this->getFirst();
    }
}