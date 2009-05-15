<?php

require_once "Interface.php";

abstract class Zend_Entity_Mapper_Loader_Abstract implements Zend_Entity_Mapper_Loader_Interface
{
    /**
     * @var string
     */
    protected $_table;

    /**
     * @var string
     */
    protected $_class;

    /**
     * @var Zend_Entity_Mapper_Definition_PrimaryKey
     */
    protected $_primaryKey;

    /**
     * @var array
     */
    protected $_sqlColumnAliasMap = array();

    /**
     * @var array
     */
    protected $_columnsToPropertyNames = array();

    /**
     * @var array
     */
    protected $_lateSelectedRelations   = array();

    /**
     * @var array
     */
    protected $_lateSelectedCollections = array();

    /**
     * @var boolean
     */
    protected $_hasLateLoadingObjects   = false;

    /**
     * @var array
     */
    protected $_lazyLoadRelations   = array();

    /**
     * @var array
     */
    protected $_lazyLoadCollections = array();

    /**
     * @var boolean
     */
    protected $_hasLazyLoads        = false;

    /**
     * Construct new Loader based on an entity definition.
     * 
     * @param Zend_Entity_Mapper_Definition_Entity $entityDefinition
     */
    public function __construct(Zend_Entity_Mapper_Definition_Entity $entityDefinition)
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
                $relation = $extension->getRelation();
                if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_SELECT) {
                    $this->_lateSelectedCollections[] = $extension;
                    $this->_hasLateLoadingObjects     = true;
                } else if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_LAZY) {
                    $this->_lazyLoadCollections[]     = $extension;
                    $this->_hasLazyLoads              = true;
                } elseif($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_JOIN) {
                    // TODO: Implement Join saving of information
                }
            }
        }
    }

    abstract protected function renameColumnToPropertyKeys($row);

    protected function createLazyLoadEntity(Zend_Entity_Manager $manager, $class, $id)
    {
        $identityMap = $manager->getIdentityMap();
        if($identityMap->hasObject($class, $id)) {
            $lazyEntity = $identityMap->getObject($class, $id);
        } else {
            $callback          = array($manager, "findByKey");
            $callbackArguments = array($class, $id);
            $lazyEntity = new Zend_Entity_Mapper_LazyLoad_Entity($callback, $callbackArguments);
            $identityMap->addObject($class, $id, $lazyEntity);
        }
        return $lazyEntity;
    }

    protected function createLazyLoadCollection(Zend_Entity_Manager $manager, $class, $select)
    {
        $callback          = array($manager, "find");
        $callbackArguments = array($class, $select);
        return new Zend_Entity_Mapper_LazyLoad_Collection($callback, $callbackArguments);
    }

    public function createEntityFromRow(array $row, Zend_Entity_Manager_Interface $entityManager)
    {
        $entityClass = $this->_class;

        $identityMap = $entityManager->getIdentityMap();
        $key         = $this->_primaryKey->retrieveKeyValuesFromProperties($row);
        $keyHash     = Zend_Entity_Mapper_Definition_Utility::hashKeyIdentifier($key);
        if($identityMap->hasObject($entityClass, $keyHash) == true) {
            $entity = $identityMap->getObject($entityClass, $keyHash);
        } else {
            $entity = $this->createEntity($row);
            // Set this before loadRelationsIntoEntity() to circumvent infinite loop on backreferences and stuff
            $identityMap->addObject($entityClass, $keyHash, $entity);

            $this->loadRow($entity, $row, $entityManager);
        }
        return $entity;
    }

    protected function createEntity(array $row)
    {
        $entityClass = $this->_class;
        return new $entityClass();
    }

    public function loadRow(Zend_Entity_Interface $entity, array $row, Zend_Entity_Manager_Interface $entityManager)
    {
        $state = $this->renameColumnToPropertyKeys($row);
        unset($row);

        // All no-existant properties are lazy loaded now:
        if($this->hasLazyBoundObjects()) {
            $state = $this->initializeLazyBoundObjects($state, $entityManager);
        }
        if($this->hasLateBoundObjects()) {
            $state = $this->initializeLateBoundObjects($state, $entityManager);
        }
        $entity->setState($state);
    }

    protected function hasLazyBoundObjects()
    {
        return $this->_hasLazyLoads;
    }

    protected function initializeLazyBoundObjects(array $entityState, Zend_Entity_Manager $entityManager)
    {
        foreach($this->_lazyLoadRelations AS $relation) {
            $propertyName = $relation->getPropertyName();
            $foreignKeyValue = $entityState[$propertyName];
            $entityState[$propertyName] = $this->createLazyLoadEntity($entityManager, $relation->getClass(), $foreignKeyValue);
        }
        foreach($this->_lazyLoadCollections AS $collectionDef) {
            $relation   = $collectionDef->getRelation();
            $foreignDefinition = $entityManager->getResource()->getDefinitionByEntityName($relation->getClass());

            $keyValue = $entityState[$this->_primaryKey->getPropertyName()];

            $select = $entityManager->select($relation->getClass());
            $db = $select->getAdapter();

            $intersectTable = $collectionDef->getTable();
            if($foreignDefinition->getTable() !== $collectionDef->getTable()) {
                
                $foreignPrimaryKey = $foreignDefinition->getPrimaryKey()->getKey();

                $intersectOnLhs = $db->quoteIdentifier($intersectTable.".".$relation->getColumnName());
                $intersectOnRhs = $db->quoteIdentifier($foreignDefinition->getTable().".".$foreignPrimaryKey);
                $intersectOn = $intersectOnLhs." = ".$intersectOnRhs;
                $select->join($intersectTable, $intersectOn, array());
            }
            $select->where( $db->quoteIdentifier($intersectTable.".".$collectionDef->getKey())." = ?", $keyValue);

            if($collectionDef->getOrderBy() !== null) {
                $select->order($collectionDef->getOrderBy());
            }

            if($collectionDef->getWhere() !== null) {
                $select->where($collectionDef->getWhere());
            }

            $propertyName = $collectionDef->getPropertyName();
            $entityState[$propertyName] = $this->createLazyLoadCollection($entityManager, $relation->getClass(), $select);
        }
        return $entityState;
    }

    protected function hasLateBoundObjects()
    {
        return $this->_hasLateLoadingObjects;
    }

    /**
     * Takes an entity state and replaces all foreign keys with directly fetched related entities.
     * 
     * @param  array $state
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return array
     */
    protected function initializeLateBoundObjects(array $state, Zend_Entity_Manager_Interface $entityManager)
    {
        foreach($this->_lateSelectedRelations AS $relation) {
            $propertyName = $relation->getPropertyName();
            $foreignKeyValue = $state[$propertyName];
            $relatedEntity = $entityManager->findByKey($relation->getClass(), $foreignKeyValue);
            $state[$propertyName] = $relatedEntity;
        }
        foreach($this->_lateSelectedCollections AS $collection) {
            
        }
        return $state;
    }
}