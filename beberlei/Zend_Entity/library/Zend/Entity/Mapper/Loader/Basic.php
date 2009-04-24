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
    public function initSelect(Zend_Db_Select $select)
    {
        $select->from($this->_table);
    }

    public function initColumns(Zend_Db_Select $select)
    {
        $select->columns($this->_sqlColumnAliasMap);
    }

    /**
     *
     * @param <type> $stmt
     * @param <type> $entityManager
     * @param <type> $fetchMode
     * @return <type> 
     */
    public function processResultset(Zend_Db_Statement_Interface $stmt, Zend_Entity_Manager $entityManager, $fetchMode=Zend_Entity_Manager::FETCH_ENTITIES)
    {
        $unitOfWork = $entityManager->getUnitOfWork();

        $collection = array();
        while($row = $stmt->fetch(Zend_Db::FETCH_ASSOC)) {
            $entity = $this->loadRow($row, $entityManager);
            $collection[] = $entity;

            if($unitOfWork->isManagingCurrentTransaction() == true) {
                $unitOfWork->registerClean($entity);
            }
        }
        $stmt->closeCursor();

        // TODO: Late Select Binding per Entity should be done here
        $this->initializeLateBoundObjects($collection, $entityManager, $fetchMode);

        if($fetchMode == Zend_Entity_Manager::FETCH_ENTITIES) {
            return new Zend_Entity_Collection($collection);
        } else if($fetchMode == Zend_Entity_Manager::FETCH_ARRAY) {
            return $collection;
        }
    }

    protected function renameColumnToPropertyKeys($row)
    {
        $state = array();
        foreach($this->_columnsToPropertyNames AS $columnName => $propertyName) {
            if(!array_key_exists($columnName, $row)) {
                throw new Exception("In rename column to property the column '".$columnName."' does not exist in resultset.");
            }
            $state[$propertyName] = $row[$columnName];
            unset($row[$columnName]);
        }
        return $state;
    }

    protected function createEntity(array $row)
    {
        $entityClass = $this->_class;
        $entity      = new $entityClass();
        $entity->setState($row);
        return $entity;
    }
}