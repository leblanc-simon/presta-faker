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

class Model
    extends Base
    implements LevelInterface
{
    static protected $models = array(
        // English
        'Aston Martin DB7',
        'Aston Martin DB9',
        'Aston Martin Vanquish',
        'Bentley Continental GT',
        'Bentley Continental GTC',
        'Bentley Mulsanne2010',
        'Jaguar XJR-15',
        'Jaguar S-Type',
        'Jaguar F-Type',
        // French
        'Citroën Traction Avant',
        'Citroën Berlingo',
        'Citroën C-Crosser',
        'Citroën DS4',
        'Citroën C-ZERO',
        'Citroën C1',
        'Citroën C4 Picasso',
        'Peugeot 107',
        'Peugeot 208',
        'Peugeot 408',
        'Peugeot 508',
        'Peugeot RCZ',
        'Peugeot 4007',
        'Peugeot Expert Tepee',
        'Renault Megane',
        'Renault Clio',
        'Renault Kangoo',
        'Renault Laguna Coupe',
        'Renault Master',
        'Renault Twingo',
        'Renault Twizy',
        'Renault Symbol',
        'Renault Koleos',
        // German
        'Audi A1',
        'Audi A4',
        'Audi A5',
        'Audi A6',
        'Audi A7',
        'Audi A8',
        'Audi TT',
        'Audi R8',
        'Audi Q3',
        'Audi Q5',
        'Audi Q7',
        'BMW E24',
        'BMW M3',
        'BMW M5',
        'BMW Z4 Roadster',
        'BMW X6M',
        'Mercedez Class A',
        'Mercedez Class B',
        'Mercedez Class C',
        'Mercedez Class D',
        'Mercedez Class E',
        'Mercedez Class S',
        'Opel Meriva',
        'Opel Astra',
        'Opel Corsa',
        'Opel Zafira',
        'Opel Omega',
        'Opel Calibra',
        'Opel Vectra',
        'Volkswagen Beetle',
        'Volkswagen Golf',
        'Volkswagen Polo',
        'Volkswagen Touran',
        'Volkswagen Sharan',
        'Volkswagen Santana',
        'Volkswagen Lavida',
        // Italian
        'Fiat 500',
        'Fiat Bravo',
        'Fiat Croma',
        'Fiat Grande Punto',
        'Fiat Idea',
        'Fiat Weekend',
        'Fiat Panda',
        'Alfa Romeo Mi.To',
        'Alfa Romeo Giulietta',
        'Alfa Romeo 159',
        // Sweden
        'Saab 9-3 Berline de Sport',
        'Saab 9-3 Cabriolet II',
        'Saab 9-3 Sport-Hatch',
        'Saab 9-3X, crossover',
        'Saab 9-5 II Berline',
        'Saab 9-5 II Estate Break',
        'Saab 9-4X',
    );

    static protected $models_by_brand = array();

    public function getAll()
    {
        return static::$models;
    }

    public function getMaxLevel()
    {
        return 1;
    }

    public function getFirst()
    {
        return static::randomElement(static::$models);
    }

    public function getFirstByBrand($brand)
    {
        if (isset(static::$models_by_brand[$brand]) === false) {
            static::$models_by_brand[$brand] = array();
            foreach (static::$models as $model) {
                if (strpos($model, $brand) === 0) {
                    static::$models_by_brand[$brand][] = $model;
                }
            }
        }

        if (count(static::$models_by_brand[$brand]) === 0) {
            return null;
        }

        return static::randomElement(static::$models_by_brand[$brand]);
    }

    public function model($brand = null)
    {
        if (null !== $brand) {
            return $this->getFirstByBrand($brand);
        }

        return $this->getFirst();
    }
}