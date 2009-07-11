<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend_Entity
 * @package    Mapper
 * @subpackage Loader
 * @copyright  Copyright (c) 2005-2009 Benjamin Eberlei
 * @license    http://www.opensource.org/licenses/bsd-license.php     New BSD License
 * @author     Benjamin Eberlei (kontakt@beberlei.de)
 */


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
    protected $_columnNameToProperty = array();

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
     * @var Zend_Entity_Mapper_StateTransformer_Abstract
     */
    protected $_stateTransformer = null;

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

        $propertyNames = array();
        foreach($entityDefinition->getProperties() AS $property) {
            // TODO: Implement Lazy Load Properties
            $columnName = $property->getColumnName();
            $this->_sqlColumnAliasMap[$columnName] = $property->getColumnSqlName();
            $this->_columnNameToProperty[$columnName] = $property;
            $propertyNames[] = $property->getPropertyName();
        }
        foreach($entityDefinition->getRelations() AS $relation) {
            if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_SELECT) {
                // Setup retrieval of the foreign key value
                $columnName = $relation->getColumnName();
                $this->_sqlColumnAliasMap[$columnName] = $relation->getColumnSqlName();
                $this->_columnNameToProperty[$columnName] = $relation;
                
                // Save Relation to the later retrieval stack
                $this->_lateSelectedRelations[] = $relation;
                $this->_hasLateLoadingObjects = true;
            } elseif($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_LAZY) {
                // Setup retrieval of the foreign key value
                $columnName = $relation->getColumnName();
                $this->_sqlColumnAliasMap[$columnName] = $relation->getColumnSqlName();
                $this->_columnNameToProperty[$columnName] = $relation;

                // Prepare for Lazy Load building.
                $this->_lazyLoadRelations[] = $relation;
                $this->_hasLazyLoads = true;
            }
            $propertyNames[] = $relation->getPropertyName();
        }
        foreach($entityDefinition->getExtensions() AS $extension) {
            if($extension instanceof Zend_Entity_Mapper_Definition_Collection) {
                if($extension->getCollectionType() == Zend_Entity_Mapper_Definition_Collection::COLLECTION_RELATION) {
                    $relation = $extension->getRelation();
                    if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_SELECT) {
                        $this->_lateSelectedCollections[] = $extension;
                        $this->_hasLateLoadingObjects = true;
                    } else if($relation->getFetch() == Zend_Entity_Mapper_Definition_Property::FETCH_LAZY) {
                        $this->_lazyLoadCollections[]     = $extension;
                        $this->_hasLazyLoads = true;
                    }
                }
            }
            $propertyNames[] = $extension->getPropertyName();
        }

        $this->_stateTransformer = $entityDefinition->getStateTransformer();
    }

    protected function renameAndCastColumnToPropertyKeys($row)
    {
        $state = array();
        foreach($this->_columnNameToProperty AS $columnName => $property) {
            if(!array_key_exists($columnName, $row)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "In rename column to property the column '".$columnName."' does not exist in resultset."
                );
            }
            $state[$property->getPropertyName()] = $property->castColumnToPhpType($row[$columnName]);
        }
        return $state;
    }


    protected function createLazyLoadCollection(Zend_Entity_Manager $manager, $class, $select)
    {
        $callback = array($manager, "find");
        $callbackArguments = array($class, $select);
        return new Zend_Entity_Mapper_LazyLoad_Collection($callback, $callbackArguments);
    }

    public function createEntityFromRow(array $row, Zend_Entity_Manager_Interface $entityManager)
    {
        $identityMap = $entityManager->getIdentityMap();
        $key = $this->_primaryKey->retrieveKeyValuesFromProperties($row);
        if($identityMap->hasObject($this->_class, $key) == true) {
            $entity = $identityMap->getObject($this->_class, $key);
        } else {
            $entity = $this->createEntity($row);
            // Set this before loadRelationsIntoEntity() to circumvent infinite loop on backreferences and stuff
            $identityMap->addObject($this->_class, $key, $entity);

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
        $state = $this->renameAndCastColumnToPropertyKeys($row);
        unset($row);

        if($this->hasLazyBoundObjects()) {
            $state = $this->initializeLazyBoundObjects($state, $entityManager);
        }
        if($this->hasLateBoundObjects()) {
            $state = $this->initializeLateBoundObjects($state, $entityManager);
        }
        $this->_stateTransformer->setState($entity, $state);
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
            $entityState[$propertyName] = $entityManager->getReference($relation->getClass(), $foreignKeyValue);
        }
        foreach($this->_lazyLoadCollections AS $collectionDef) {
            $relation   = $collectionDef->getRelation();
            $foreignDefinition = $entityManager->getMetadataFactory()->getDefinitionByEntityName($relation->getClass());

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
            $relatedEntity = $entityManager->load($relation->getClass(), $foreignKeyValue);
            $state[$propertyName] = $relatedEntity;
        }
        foreach($this->_lateSelectedCollections AS $collection) {
            
        }
        return $state;
    }
}