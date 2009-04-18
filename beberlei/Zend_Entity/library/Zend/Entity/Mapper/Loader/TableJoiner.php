<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

abstract class Zend_Entity_Mapper_Loader_TableJoiner implements Zend_Entity_Mapper_Loader_Interface
{
    protected $_joinTables = array();

    public function __construct($entityDefinition)
    {
        parent::__construct($entityDefinition);

        foreach($entityDefinition->getExtensions() AS $property) {
            if($property instanceof Zend_Entity_Mapper_Definition_Join) {
                $condition = $entityDefinition->getPrimaryKey()->getKey()." = ".$property->getKey();
                $type = ($property->getOptional() === true) ? "outer" : "inner";
                $joinTable = array('name' => $property->getTable(), 'condition' => $condition, 'columns' => array(), 'type' => $type);
                foreach($property->getProperties() AS $sub) {
                    $joinTable['columns'][$sub->getColumnName()] = $sub->getColumnSqlName();
                    $this->columnsToPropertyNames[$sub->getColumnName()] = $sub->getPropertyName();
                }
                $this->_joinTables[] = $joinTable;
            }
        }
    }

    public function initSelect(Zend_Db_Select $select)
    {
        parent::initSelect($select);
        foreach($this->_joinTables AS $table) {
            if($table['type'] == "inner") {
                $select->joinInner($table['name'], $table['condition'], array());
            } else {
                $select->joinLeft($table['name'], $table['condition'], array());
            }
        }
    }

    public function initColumns(Zend_Db_Select $select)
    {
        parent::initColumns($select);
        foreach($this->_joinTables AS $joinTable) {
            $select->columns($joinTable['columns'], $joinTable['name']);
        }
    }
}