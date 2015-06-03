<?php

namespace PrestaFaker\Webservice\Sql;

interface SqlInterface
{
    /**
     * @param string $table_prefix
     */
    public function __construct($table_prefix = 'ps_');

    /**
     * @param $id
     * @param array $values
     * @return string
     */
    public function build($id, array $values);
}