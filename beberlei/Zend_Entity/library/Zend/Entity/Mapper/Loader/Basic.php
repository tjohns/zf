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

require_once "Abstract.php";

class Zend_Entity_Mapper_Loader_Basic extends Zend_Entity_Mapper_Loader_Abstract
{
    /**
     * @param Zend_Db_Select $select
     */
    public function initSelect(Zend_Db_Select $select)
    {
        $select->from($this->_table);
    }

    /**
     * @param Zend_Db_Select $select
     */
    public function initColumns(Zend_Db_Select $select)
    {
        $select->columns($this->_sqlColumnAliasMap);
    }

    /**
     * @param  array $resultSet
     * @param  Zend_Entity_Manager $entityManager
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
     */
    public function processResultset($resultSet, Zend_Entity_Manager $entityManager, $fetchMode=Zend_Entity_Manager::FETCH_ENTITIES)
    {
        $collection = array();
        foreach($resultSet AS $row) {
            if($fetchMode == Zend_Entity_Manager::FETCH_ARRAY) {
                $entity = $this->renameAndCastColumnToPropertyKeys($row);
            } else {
                $entity = $this->createEntityFromRow($row, $entityManager);
            }
            $collection[] = $entity;
        }
        return $collection;
    }
}