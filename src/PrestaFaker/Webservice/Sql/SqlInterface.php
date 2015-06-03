<?php

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