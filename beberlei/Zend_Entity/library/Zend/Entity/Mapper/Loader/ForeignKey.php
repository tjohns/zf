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

class Zend_Entity_Mapper_Loader_ForeignKey extends Zend_Entity_Mapper_Loader_Basic
{
    protected $joinStack = array();

    protected $relationColumnsToPropertyNames = array();

    protected $columnsToPropertyNames = array();

    protected $relationLoader = array();

    public function __construct($entityDefinition, Zend_Entity_Resource_Interface $definitionMap)
    {
        parent::__construct($entityDefinition);
        foreach($entityDefinition->getRelations() AS $relation) {
            if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_JOIN &&
                !($relation instanceof Zend_Entity_Mapper_Definition_Collection) ) {
                $this->joinStack[] = array(
                    'relation' => $relation,
                    'definition' => $definitionMap->getDefinitionByEntityName($relation->getClass())
                );
            }
        }
    }

    public function initSelect(Zend_Db_Select $select)
    {
        $select->from($this->entityDefinition->getTable());
        foreach($this->joinStack AS $joinRelation) {
            $relation = $joinRelation['relation'];
            $definition = $joinRelation['definition'];
            
            $whereClauseLhs = $this->entityDefinition->getTable().".".$relation->getColumnName();
            $whereClauseRhs = $definition->getTable().".".$relation->getForeignKey();
            $select->joinLeft($definition->getTable(), $whereClauseLhs." = ".$whereClauseRhs);
        }
    }

    public function initColumns(Zend_Db_Select $select)
    {
        $columns = array();
        foreach($this->entityDefinition->getProperties() AS $property) {
            $columnName = $property->getColumnName();
            if($columnName !== null) {
                $columnLong = $this->entityDefinition->getTable()."___".$columnName;
                $columns[$columnLong] = $columnName;
                $this->columnsToPropertyNames[$columnLong] = $property->getPropertyName();
            }
        }
        $select->columns($columns, $this->entityDefinition->getTable());
        foreach($this->joinStack AS $relation) {
            $relationName = $relation['relation']->getPropertyName();
            $relationTable = $relation['definition']->getTable();
            
            $columns = array();
            foreach($relation['definition']->getProperties() AS $property) {
                $columnName = $property->getColumnName();

                if($columnName !== null) {
                    $columnLong = $relationTable."___".$columnName;
                    $columns[$columnLong] = $columnName;
                    $this->relationColumnsToPropertyNames[$columnLong] = array(
                        'propertyName'   => $property->getPropertyName(),
                        'relationName'   => $relationName,
                    );
                }
            }
            $this->relationLoader[$relationName] = $relation['definition']->getLoader();
            $select->columns(
                $columns, $relationTable
            );
        }
    }


    public function processResultset(Zend_Db_Statement_Interface $stmt, Zend_Entity_Manager $entityManager, $fetchMode=Zend_Entity_Manager::FETCH_ENTITIES)
    {
        $map        = $entityManager->getIdentityMap();
        $unitOfWork = $entityManager->getUnitOfWork();
        
        $collection = array();
        while($rawRow = $stmt->fetch(Zend_Db::FETCH_ASSOC)) {
            $objectFields = array();
            $row = array();
            foreach($rawRow AS $columnLongName => $columnValue) {
                if(isset($this->columnsToPropertyNames[$columnLongName])) {
                    $row[$columnLongName] = $columnValue;
                } else if(isset($this->relationColumnsToPropertyNames[$columnLongName])) {
                    $relatedField = $this->relationColumnsToPropertyNames[$columnLongName];
                    $objectFields[$relatedField['relationName']][$relatedField['propertyName']] = $columnValue;
                }
                unset($rawRow[$columnLongName]);
            }

            foreach($objectFields AS $relationName => $relationState) {
                $row[$relationName] = $this->relationLoader[$relationName]->createEntityFromRow($relationState, $entityManager);
            }

            $entity = $this->createEntityFromRow($row, $entityManager);
            $collection[] = $entity;

            if($unitOfWork->isManagingCurrentTransaction() == true) {
                $unitOfWork->registerClean($entity);
            }
        }
        $stmt->closeCursor();
        return new Zend_Entity_Collection($collection);
    }

    protected function renameColumnToPropertyKeys($row)
    {
        $state = parent::renameColumnToPropertyKeys($row);
        foreach($this->joinStack AS $relation) {
            
        }
        return $state;
    }

    protected function createEntity(array $row)
    {
        $entityClass = $this->entityDefinition->getClass();
        $entity      = new $entityClass();
        $entity->setState($row);
        return $entity;
    }
}
