<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Prestashop;

class Link
{
    const MAX_LENGTH = 128;

    static public function rewrite($name)
    {
        return substr(strtolower(preg_replace_callback('/([^a-zA-Z0-9-]+)/', function() { return '-'; }, $name)), 0, self::MAX_LENGTH);
    }
}