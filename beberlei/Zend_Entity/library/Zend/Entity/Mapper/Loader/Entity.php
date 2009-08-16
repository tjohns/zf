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

class Zend_Entity_Mapper_Loader_Entity extends Zend_Entity_Mapper_Loader_LoaderAbstract
{
    /**
     * @param Zend_Db_Select $select
     */
    public function initSelect(Zend_Db_Select $select)
    {
        $select->from($this->_mappingInstruction->table);
    }

    /**
     * @param Zend_Db_Select $select
     */
    public function initColumns(Zend_Db_Select $select)
    {
        $select->columns($this->_mappingInstruction->sqlColumnAliasMap);
    }

    /**
     * @param  array $resultSet
     * @param  Zend_Entity_Manager $entityManager
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
     */
    public function processResultset($resultSet, Zend_Entity_Manager $entityManager)
    {
        $collection = array();
        foreach($resultSet AS $row) {
            $collection[] = $this->createEntityFromRow($row, $entityManager);
        }
        return $collection;
    }
}