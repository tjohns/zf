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
     * @var Zend_Entity_Definition_PrimaryKey
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
    protected $_relations   = array();

    /**
     * @var array
     */
    protected $_collections = array();

    /**
     * @var array
     */
    protected $_elementsCollection = array();

    /**
     * @var Zend_Entity_StateTransformer_Abstract
     */
    protected $_stateTransformer = null;

    /**
     * @var Zend_Entity_Definition_Property
     */
    protected $_versionProperty = null;

    protected $_mappingInstruction = null;

    /**
     * Construct new Loader based on an entity definition.
     * 
     * @param Zend_Entity_Definition_Entity $entityDefinition
     */
    public function __construct(Zend_Entity_Definition_Entity $entityDefinition, Zend_Entity_Mapper_MappingInstruction $mappingInstruction)
    {
        $this->_mappingInstruction = $mappingInstruction;

        $this->_table = $entityDefinition->getTable();
        $this->_class = $entityDefinition->getClass();
        $this->_primaryKey = $entityDefinition->getPrimaryKey();
        $this->_versionProperty = $entityDefinition->getVersionProperty();

        $propertyNames = array();
        foreach($entityDefinition->getProperties() AS $property) {
            if($property instanceof Zend_Entity_Definition_AbstractRelation) {
                // Setup retrieval of the foreign key value
                $columnName = $property->getColumnName();
                $this->_sqlColumnAliasMap[$columnName] = $property->getColumnSqlName();
                $this->_columnNameToProperty[$columnName] = $property;

                $this->_relations[] = $property;
                $propertyNames[] = $property->getPropertyName();
            } elseif($property instanceof Zend_Entity_Definition_Collection) {
                if($property->getCollectionType() == Zend_Entity_Definition_Collection::COLLECTION_RELATION) {
                    $this->_collections[] = $property;
                } else if($property->getCollectionType() == Zend_Entity_Definition_Collection::COLLECTION_ELEMENTS) {
                    $this->_elementsCollection[] = $property;
                }
                $propertyNames[] = $property->getPropertyName();
            } else {
                $columnName = $property->getColumnName();
                $this->_sqlColumnAliasMap[$columnName] = $property->getColumnSqlName();
                $this->_columnNameToProperty[$columnName] = $property;
                $propertyNames[] = $property->getPropertyName();
            }
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

    public function createEntityFromRow(array $row, Zend_Entity_Manager_Interface $entityManager)
    {
        $identityMap = $entityManager->getIdentityMap();
        $key = $this->_primaryKey->retrieveKeyValuesFromProperties($row);
        if($identityMap->hasObject($this->_class, $key) == true) {
            $entity = $identityMap->getObject($this->_class, $key);
        } else {
            $versionId = null;
            if($this->_versionProperty !== null) {
                $versionColumnName = $this->_versionProperty->getColumnName();
                if(isset($row[$versionColumnName])) {
                    $versionId = $row[$versionColumnName];
                } else {
                    throw new Zend_Entity_Exception(
                        "Missing version column '".$versionColumnName."' in entity resultset"
                    );
                }
            }

            $entity = $this->createEntity($row);
            // Set this before loadRelationsIntoEntity() to circumvent infinite loop on backreferences and stuff
            $identityMap->addObject($this->_class, $key, $entity, $versionId);

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

        $state = $this->initializeRelatedObjects($state, $entityManager);
        $this->_stateTransformer->setState($entity, $state);
        
        $entityManager->getEventListener()->postLoad($entity);
    }
    
    protected function initializeRelatedObjects(array $entityState, Zend_Entity_Manager $entityManager)
    {
        foreach($this->_relations AS $relation) {
            /* @var $relation Zend_Entity_Definition_AbstractRelation */
            $propertyName = $relation->getPropertyName();
            if($relation->getFetch() == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $entityState[$propertyName] = $entityManager->getReference($relation->getClass(), $entityState[$propertyName]);
            } else if($relation->getFetch() == Zend_Entity_Definition_Property::FETCH_SELECT) {
                $entityState[$propertyName] = $entityManager->load($relation->getClass(), $entityState[$propertyName]);
            }
        }
        foreach($this->_collections AS $collectionDef) {
            /* @var $collectionDef Zend_Entity_Definition_Collection */
            $relation   = $collectionDef->getRelation();
            $foreignDefinition = $entityManager->getMetadataFactory()->getDefinitionByEntityName($relation->getClass());

            $keyValue = $entityState[$this->_primaryKey->getPropertyName()];

            $db = $entityManager->getAdapter();
            $query = $entityManager->createNativeQuery($relation->getClass());

            $intersectTable = $collectionDef->getTable();
            if($foreignDefinition->getTable() !== $collectionDef->getTable()) {
                
                $foreignPrimaryKey = $foreignDefinition->getPrimaryKey()->getKey();

                $intersectOnLhs = $db->quoteIdentifier($intersectTable.".".$relation->getColumnName());
                $intersectOnRhs = $db->quoteIdentifier($foreignDefinition->getTable().".".$foreignPrimaryKey);
                $intersectOn = $intersectOnLhs." = ".$intersectOnRhs;
                $query->join($intersectTable, $intersectOn, array());
            }
            $query->where( $db->quoteIdentifier($intersectTable.".".$collectionDef->getKey())." = ?", $keyValue);

            if($collectionDef->getOrderBy() !== null) {
                $query->order($collectionDef->getOrderBy());
            }

            if($collectionDef->getWhere() !== null) {
                $query->where($collectionDef->getWhere());
            }

            $propertyName = $collectionDef->getPropertyName();

            if($collectionDef->getFetch() == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $callback = array($query, "getResultList");
                $entityState[$propertyName] = new Zend_Entity_LazyLoad_Collection($callback, array());
            } else if($collectionDef->getFetch() == Zend_Entity_Definition_Property::FETCH_SELECT) {
                $entityState[$propertyName] = new Zend_Entity_Collection($query->getResultList());
            }
        }
        foreach($this->_elementsCollection AS $elementDef) {
            $propertyName = $elementDef->getPropertyName();
            $pk = $this->_primaryKey->getPropertyName();

            /* @var $elementDef Zend_Entity_Definition_Collection */
            $db = $entityManager->getAdapter();
            $select = $db->select();
            $select->from($elementDef->getTable());
            $select->where($elementDef->getKey()." = ?", $entityState[$pk]);

            if($elementDef->getFetch() == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $entityState[$propertyName] = new Zend_Entity_LazyLoad_ElementHashMap($select);
            } else {
                $stmt = $select->query();

                $elements = array();
                foreach($stmt->fetchAll() AS $row) {
                    $elements[$row[$elementDef->getMapKey()]] = $row[$elementDef->getElement()];
                }

                $entityState[$propertyName] = new Zend_Entity_Collection_ElementHashMap($elements);
            }
        }

        return $entityState;
    }
}