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
use PrestaFaker\Faker\Provider\IProduct;

class Product
    extends Base
    implements IProduct
{
    static protected $formats = array(
        '{{constructor}} {{internalProductName}}',
        '{{constructor}} {{internalProductName}} {{quantity}}',
        '{{constructor}} {{internalProductName}} {{safeColorName}}',
        '{{constructor}} {{internalProductName}} {{safeColorName}} {{quantity}}',
        '{{internalProductName}} ({{constructor}})',
        '{{internalProductName}} {{quantity}} ({{constructor}})',
        '{{internalProductName}} {{safeColorName}} ({{constructor}})',
        '{{internalProductName}} {{safeColorName}} {{quantity}} ({{constructor}})',
    );

    static protected $constructors = array(
        'ATE', 'Aisin Seiki', 'Bendix', 'Bosal', 'Bosch', 'Brembo', 'Contitech', 'Continental AG', 'Dana',
        'Denso', 'Delphi', 'FAE', 'FAG', 'Febi Bilstein', 'Ferodo', 'Herth+buss Jakoparts', 'Johnson Controls',
        'Lear', 'Magneti Marelli', 'Mecafilter', 'Monroe', 'Nipparts', 'Otto Zimmermann Gmbh', 'Sachs',
        'Siemens Vdo Automotive', 'Shell', 'Skf', 'Sumitomo Electric', 'Snr', 'Textar', 'Toyota Boshoku',
        'Arvinmeritor', 'TRW', 'Thyssenkrupp Automotive', 'Valeo', 'Visteon', 'Yazaki', 'ZF Friedrichshafen',
    );

    static protected $internal_product_name_formats = array(
        '{{letter}}{{number}}',
        '{{number}}{{letter}}',
        '{{letter}}-{{number}}',
        '{{number}}-{{letter}}',
        '{{letter}}{{number}}{{letter}}',
        '{{letter}}-{{number}}{{letter}}',
        '{{letter}}{{number}}-{{letter}}',
        '{{letter}}-{{number}}-{{letter}}',
    );

    static protected $quantities = array(
        1, 2, 4, 6, 8, 10, 12, 16, 20, 50,
    );

    public function productName()
    {
        $format = static::randomElement(static::$formats);

        return $this->generator->parse($format);
    }

    public function constructor()
    {
        return static::randomElement(static::$constructors);
    }

    public function internalProductName()
    {
        $format = static::randomElement(static::$internal_product_name_formats);

        return $this->generator->parse($format);
    }

    public function letter()
    {
        $letter = '';
        for ($i = 0, $max = mt_rand(1, 3); $i < $max; $i++) {
            $letter .= static::randomLetter();
        }
        return strtoupper($letter);
    }

    public function number()
    {
        return static::randomNumber(3, 10);
    }

    public function color()
    {
        return $this->generator->colorName;
    }

    public function quantity()
    {
        return 'x'.static::randomElement(static::$quantities);
    }
}