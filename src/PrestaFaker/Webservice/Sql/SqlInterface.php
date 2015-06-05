<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Webservice\Sql;

use Symfony\Component\EventDispatcher\EventDispatcher;

interface SqlInterface
{
    /**
     * @param string $table_prefix
     * @param EventDispatcher $dispatcher
     */
    public function __construct($table_prefix = 'ps_', EventDispatcher $dispatcher);

    /**
     * @param $id
     * @param array $values
     * @return string
     */
    public function build($id, array $values);
}
