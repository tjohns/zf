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

class Zend_Entity_Mapper_Persister_Simple implements Zend_Entity_Mapper_Persister_Interface
{
    /**
     * @var array
     */
    protected $_properties = array();

    /**
     * @var array
     */
    protected $_toOneRelations = array();

    /**
     * @var string
     */
    protected $_class;

    /**
     * @var Zend_Entity_Mapper_Definition_PrimaryKey
     */
    protected $_primaryKey;

    /**
     * @var string
     */
    protected $_table;

    /**
     * Initialize is called once on each persister to gather information on how to perform the persist operation.
     *
     * @param  Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param  Zend_Entity_Resource_Interface     $defMap
     * @return void
     */
    public function initialize(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $defMap)
    {
        $properties = array();
        foreach($entityDef->getProperties() AS $property) {
            if(!($property instanceof Zend_Entity_Mapper_Definition_Formula)) {
                $properties[] = $property;
            }
        }
        $this->_properties       = $properties;
        foreach($entityDef->getRelations() AS $relation) {
            if($relation->isOwning()) {
                $this->_toOneRelations[] = $relation;
            }
        }
        $this->_class            = $entityDef->getClass();
        $this->_primaryKey       = $entityDef->getPrimaryKey();
        $this->_table            = $entityDef->getTable();
    }

    /**
     * Save entity into persistence based on the persisters scope
     *
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager $entityManager
     * @return void
     */
    public function save(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager)
    {
        $entityState = $entity->getState();
        $dbState = array();
        foreach($this->_properties AS $property) {
            $propertyName = $property->getPropertyName();
            $columnName   = $property->getColumnName();
            // TODO is allowed to be null?
            $propertyValue = $entityState[$propertyName];
            // TODO $propertyValue = $property->convertToSqlValue($propertyValue);
            $dbState[$columnName] = $propertyValue;
        }
        foreach($this->_toOneRelations AS $relation) {
            $propertyName = $relation->getPropertyName();
            // TODO: is allowed to be null?
            // TODO: is still lazy load proxy?
            // TODO: Cascading save/update? Spill over!
            $relatedObject      = $entityState[$propertyName];

            if($relatedObject instanceof Zend_Entity_Mapper_LazyLoad_Entity) {
                $dbState[$relation->getColumnName()] = $relatedObject->getLazyLoadEntityId();
            } else if($relatedObject instanceof Zend_Entity_Interface) {
                $foreignKeyProperty = $relation->getForeignKeyPropertyName();
                $relatedObjectState = $relatedObject->getState();
                $dbState[$relation->getColumnName()] = $relatedObjectState[$foreignKeyProperty];
            } else {
                $dbState[$relation->getColumnName()] = null;
            }
        }

        $dbAdapter = $entityManager->getAdapter();
        $pk        = $this->_primaryKey;
        $tableName = $this->_table;
        if($pk->containValidPrimaryKey($dbState) === false) {
            $dbState = $pk->applyNextSequenceId($dbAdapter, $dbState);
            $dbAdapter->insert($tableName, $dbState);
            $newPrimaryKey = $pk->getSequenceState($dbAdapter, $dbState);
            $entity->setState($newPrimaryKey);

            $identityMap = $entityManager->getIdentityMap();
            $identityMap->addObject(
                $this->_class,
                Zend_Entity_Mapper_Definition_Utility::hashKeyIdentifier($newPrimaryKey),
                $entity
            );
        } else {
            $where   = $pk->buildWhereCondition($dbAdapter, $tableName, $dbState);
            $dbState = $pk->removeSequenceFromState($dbState);
            $dbAdapter->update($tableName, $dbState, $where);
        }
    }

    /**
     * Remove entity from persistence based on the persisters scope
     *
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager $entityManager
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager)
    {
        $entityState = $entity->getState();
        $pk  = $this->_primaryKey;
        if($pk->containValidPrimaryKey($entityState) == false) {
            throw new Exception("Cannot update entity with unknown primary identification state into database.");
        }

        $dbState = array();
        foreach($this->_properties AS $property) {
            if($property instanceof Zend_Entity_Mapper_Definition_PrimaryKey) {
                $propertyName = $property->getPropertyName();
                $columnName   = $property->getColumnName();
                // TODO is allowed to be null?
                $propertyValue = $entityState[$propertyName];
                // TODO $propertyValue = $property->convertToSqlValue($propertyValue);
                $dbState[$columnName] = $propertyValue;
            }
        }

        $db          = $entityManager->getAdapter();
        $tableName   = $this->_table;
        $whereClause = $pk->buildWhereCondition($db, $tableName, $dbState);
        $db->delete($tableName, $whereClause);
        $entity->setState($pk->getEmptyKeyProperties());
    }
}