<?php

class Zend_Entity_Mapper_Select extends Zend_Db_Select
{
    /**
     * Prevents Wildcards to cluster the loaded columns, because Mapper enforces required columns anyways.
     *
     * @param string $type
     * @param string $name
     * @param string $cond
     * @param string|array $cols
     * @param string $schema
     */
    protected function _join($type, $name, $cond, $cols, $schema = null)
    {
        if($cols == Zend_Db_Select::SQL_WILDCARD) {
            $cols = array();
        }
        return parent::_join($type, $name, $cond, $cols, $schema);
    }
}
