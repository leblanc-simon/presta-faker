<?php

namespace PrestaFaker\Webservice\Sql;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class SqlAbstract
{
    protected $table_prefix;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @param string $table_prefix
     * @param EventDispatcher $dispatcher
     */
    public function __construct($table_prefix = 'ps_', EventDispatcher $dispatcher)
    {
        $this->table_prefix = $table_prefix;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $sql
     * @param array $replace
     * @return string
     */
    protected function replaceSql($sql, array $replace)
    {
        $keys = array_map(function ($key) {
            return ':'.$key.':';
        }, array_keys($replace));

        $values = array_map(function ($value) {
            return "'".str_replace("'", "''", $value)."'";
        }, array_values($replace));

        // table prefix
        $keys[] = ':prefix:';
        $values[] = $this->table_prefix;

        return str_replace($keys, $values, $sql);
    }
}