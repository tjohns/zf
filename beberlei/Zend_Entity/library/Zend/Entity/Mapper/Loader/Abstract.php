<?php

require_once "Interface.php";

abstract class Zend_Entity_Mapper_Loader_Abstract implements Zend_Entity_Mapper_Loader_Interface
{
    protected $_table;
    protected $_class;
    protected $_primaryKey;

    protected $_sqlColumnAliasMap = array();
    protected $_columnsToPropertyNames = array();
    
    protected $_lateSelectedRelations   = array();
    protected $_lateSelectedCollections = array();
    protected $_hasLateLoadingObjects   = false;

    protected $_lazyLoadProperties  = array();
    protected $_lazyLoadRelations   = array();
    protected $_lazyLoadCollections = array();
    protected $_hasLazyLoads        = false;

    public function __construct($entityDefinition)
    {
        $this->_table = $entityDefinition->getTable();
        $this->_class = $entityDefinition->getClass();
        $this->_primaryKey = $entityDefinition->getPrimaryKey();

        // TODO: Much code duplication in here
        foreach($entityDefinition->getProperties() AS $property) {
            // TODO: Implement Lazy Load Properties
            $columnName = $property->getColumnName();
            $this->_sqlColumnAliasMap[$columnName] = $property->getColumnSqlName();
            $this->_columnsToPropertyNames[$columnName] = $property->getPropertyName();
        }
        foreach($entityDefinition->getRelations() AS $relation) {
            if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_SELECT) {
                // Setup retrieval of the foreign key value
                $columnName = $relation->getColumnName();
                $this->_sqlColumnAliasMap[$columnName] = $relation->getColumnSqlName();
                $this->_columnsToPropertyNames[$columnName] = $relation->getPropertyName();
                
                // Save Relation to the later retrieval stack
                $this->_lateSelectedRelations[] = $relation;
                $this->_hasLateLoadingObjects   = true;
            } elseif($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_LAZY) {
                // Setup retrieval of the foreign key value
                $columnName = $relation->getColumnName();
                $this->_sqlColumnAliasMap[$columnName] = $relation->getColumnSqlName();
                $this->_columnsToPropertyNames[$columnName] = $relation->getPropertyName();

                // Prepare for Lazy Load building.
                $this->_lazyLoadRelations[]     = $relation;
                $this->_hasLazyLoads            = true;
            } elseif($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_JOIN) {
                // TODO: Save relation related join information
            }
        }
        foreach($entityDefinition->getExtensions() AS $extension) {
            if($extension instanceof Zend_Entity_Mapper_Definition_Collection) {
                if($extension->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_SELECT) {
                    $this->_lateSelectedCollections[] = $extension;
                    $this->_hasLateLoadingObjects     = true;
                } else if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_LAZY) {
                    $this->_lazyLoadCollections[]     = $extension;
                    $this->_hasLazyLoads              = true;
                } elseif($extension->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_JOIN) {
                    // TODO: Implement Join saving of information
                }
            }
        }
    }

    abstract protected function renameColumnToPropertyKeys($row);

    protected function createLazyLoadEntity(Zend_Entity_Manager $manager, $class, $id)
    {
        $identityMap = $manager->getIdentityMap();
        $keyHash = Zend_Entity_Mapper_Definition_Utility::hashKeyIdentifier($id);
        if($identityMap->hasObject($class, $keyHash)) {
            return $identityMap->getObject($class, $keyHash);
        } else {
            $callback          = array($manager, "findByKey");
            $callbackArguments = array($class, $id);
            return new Zend_Entity_Mapper_LazyLoad_Entity($callback, $callbackArguments);
        }
    }

    protected function createLazyLoadCollection(Zend_Entity_Manager $manager, $class, $select)
    {
        $identityMap = $manager->getIdentityMap();
        if($identityMap->hasCollection($select)) {
            return $identityMap->getCollection($select);
        } else {
            $callback          = array($manager, "find");
            $callbackArguments = array($class, $select);
            return new Zend_Entity_Mapper_LazyLoad_Collection($callback, $callbackArguments);
        }
    }

    public function loadRow(array $row, Zend_Entity_Manager $entityManager)
    {
        $entityClass = $this->_class;
        $state       = $this->renameColumnToPropertyKeys($row);
        unset($row);

        // All no-existant properties are lazy loaded now:
        if($this->hasLazyBoundObjects() == true) {
            $state = $this->initializeLazyBoundObjects($state, $entityManager);
        }

        $identityMap = $entityManager->getIdentityMap();
        $key         = $this->_primaryKey->retrieveKeyValuesFromProperties($state);
        $keyHash     = Zend_Entity_Mapper_Definition_Utility::hashKeyIdentifier($key);
        if($identityMap->hasObject($entityClass, $keyHash) == true) {
            $entity = $identityMap->getObject($entityClass, $keyHash);
        } else {
            $entity = $this->createEntity($state);
            // Set this before loadRelationsIntoEntity() to circumvent infinite loop on backreferences and stuff
            $identityMap->addObject($entityClass, $keyHash, $entity);
        }
        return $entity;
    }

    protected function hasLazyBoundObjects()
    {
        return $this->_hasLazyLoads;
    }

    protected function initializeLazyBoundObjects(array $entityState, Zend_Entity_Manager $entityManager)
    {
        foreach($this->_lazyLoadProperties AS $property) {

        }
        foreach($this->_lazyLoadRelations AS $relation) {
            $propertyName = $relation->getPropertyName();

            $foreignKeyValue = $entityState[$propertyName];
            $relatedEntity = $this->createLazyLoadEntity($entityManager, $relation->getClass(), $foreignKeyValue);
            $entityState[$propertyName] = $relatedEntity;
        }
        foreach($this->_lazyLoadCollections AS $colDefinition) {
            $relation   = $colDefinition->getRelation();

            $foreignMapper = $entityManager->getMapperByEntity($relation->getClass());
            $foreignDefinition    = $foreignMapper->getDefinition();
            $foreignPrimaryKey = $foreignDefinition->getPrimaryKey()->getKey();

            $intersectTable = $colDefinition->getTable();
            $keyValue = $entityState[$this->_primaryKey->getPropertyName()];

            $select = $entityManager->select($relation->getClass());
            $db = $select->getAdapter();

            // Two possibilites: Either there exists a mapping table != foreignTable, or we have a simple OneToMany relationship
            if($foreignDefinition->getTable() !== $colDefinition->getTable()) {
                // A mapping table exists between the two entities
                $intersectOnLhs = $db->quoteIdentifier($intersectTable.".".$relation->getColumnName());
                $intersectOnRhs = $db->quoteIdentifier($foreignDefinition->getTable().".".$foreignPrimaryKey);
                $intersectOn = $intersectOnLhs." = ".$intersectOnRhs;
                $select->join($intersectTable, $intersectOn, array());
                $select->where( $db->quoteIdentifier($intersectTable.".".$colDefinition->getKey())." = ?", $keyValue);
            } else {
                // Foreign Entity holds the foreign key in its table.
                $select->where( $db->quoteIdentifier($foreignDefinition->getTable().".".$colDefinition->getKey())." = ?", $keyValue);
            }

            // Check for additional Where clause
            if($colDefinition->getWhere() !== null) {
                $select->where($colDefinition->getWhere());
            }

            $propertyName = $colDefinition->getPropertyName();
            $entityState[$propertyName] = $this->createLazyLoadCollection($entityManager, $relation->getClass(), $select);
        }
        return $entityState;
    }

    protected function hasLateBoundObjects()
    {
        return $this->_hasLateLoadingObjects;
    }

    protected function initializeLateBoundObjects(array $collection, Zend_Entity_Manager $entityManager)
    {
        foreach($collection AS $entity) {
            $entityState = $entity->getState();
            foreach($this->_lateSelectedRelations AS $relation) {
                $propertyName               = $relation->getPropertyName();
                $foreignKeyValue            = $entityState[$propertyName];
                $relatedEntity              = $entityManager->findByKey($relation->getClass(), $foreignKeyValue);
                $entityState[$propertyName] = $relatedEntity;
            }
            $entity->setState($entityState);
        }
        return $collection;
    }
}