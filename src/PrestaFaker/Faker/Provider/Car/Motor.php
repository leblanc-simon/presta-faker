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
use PrestaFaker\Faker\Provider\ILevel;

class Motor
    extends Base
    implements ILevel
{
    static protected $type = array('Essence', 'Diesel', );
    static protected $liter = array('1.5', '1.8', '2.0', '2.1', '2.2', '3.0', );
    static protected $displacement = array('65', '85', '105', '120', '150', '180', '200', '250', );
    static protected $subtype = array('dCi', 'GTI', 'e-HDI', 'TDI', 'AMG', );

    static protected $formats = array(
        '{{typeMotor}} {{literMotor}}',
        '{{typeMotor}} {{literMotor}}',
        '{{typeMotor}} {{literMotor}}',
        '{{typeMotor}} {{literMotor}} {{subtypeMotor}}',
        '{{typeMotor}} {{literMotor}} {{subtypeMotor}}',
        '{{typeMotor}} {{literMotor}} {{subtypeMotor}} {{displacementMotor}}',
    );

    static protected $all = null;

    public function getAll()
    {
        if (static::$all === null) {
            static::$all = array();
            $formats = array_unique(static::$formats);

            foreach ($formats as $format) {
                foreach (static::$type as $type) {
                    foreach (static::$liter as $liter) {
                        foreach (static::$displacement as $displacement) {
                            foreach (static::$subtype as $subtype) {
                                $motor = str_replace(
                                    array('{{typeMotor}}', '{{literMotor}}', '{{displacementMotor}}', '{{subtypeMotor}}'),
                                    array($type, $liter, $displacement, $subtype),
                                    $format
                                );
                                if (in_array($motor, static::$all) === false) {
                                    static::$all[] = $motor;
                                }
                            }
                        }
                    }
                }
            }
        }

        return static::$all;
    }

    public function getMaxLevel()
    {
        return 1;
    }

    public function getFirst()
    {
        $format = static::randomElement(static::$formats);

        return $this->generator->parse($format);
    }

    public function motor()
    {
        return $this->getFirst();
    }

    public function typeMotor()
    {
        return static::randomElement(static::$type);
    }

    public function literMotor()
    {
        return static::randomElement(static::$liter);
    }

    public function displacementMotor()
    {
        return static::randomElement(static::$displacement);
    }

    public function subtypeMotor()
    {
        return static::randomElement(static::$subtype);
    }
}