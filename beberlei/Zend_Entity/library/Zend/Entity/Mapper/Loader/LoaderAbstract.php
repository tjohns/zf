<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract Loader
 *
 * @category   Zend
 * @package    Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_Mapper_Loader_LoaderAbstract
{
    /**
     * @var Zend_Entity_Mapper_MappingInstruction
     */
    protected $_mappingInstruction = null;

    /**
     * Construct new Loader based on an entity definition.
     * 
     * @param Zend_Entity_Definition_Entity $entityDefinition
     */
    public function __construct(Zend_Entity_Definition_Entity $entityDefinition, Zend_Entity_Mapper_MappingInstruction $mappingInstruction)
    {
        $this->_mappingInstruction = $mappingInstruction;
    }

    /**
     * @param Zend_Db_Select $select
     */
    abstract public function initSelect(Zend_Db_Select $select);

    /**
     * @param Zend_Db_Select $select
     */
    abstract public function initColumns(Zend_Db_Select $select);

    /**
     * @param  array $resultSet
     * @param  Zend_Entity_Manager $entityManager
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
     */
    abstract public function processResultset($resultSet, Zend_Entity_Manager $entityManager);

    /**
     * @todo Gah Code duplication
     * @param  array $row
     * @return array
     */
    protected function renameAndCastColumnToPropertyKeys($row)
    {
        $state = array();
        foreach($this->_mappingInstruction->columnNameToProperty AS $columnName => $property) {
            if(!array_key_exists($columnName, $row)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "In rename column to property the column '".$columnName."' does not exist in resultset."
                );
            }
            $state[$property->propertyName] = $property->castColumnToPhpType($row[$columnName]);
        }
        return $state;
    }

    public function createEntityFromRow(array $row, Zend_Entity_Manager_Interface $entityManager)
    {
        $identityMap = $entityManager->getIdentityMap();
        $key = $this->_mappingInstruction->primaryKey->retrieveKeyValuesFromProperties($row);
        if($identityMap->hasObject($this->_mappingInstruction->class, $key) == true) {
            $entity = $identityMap->getObject($this->_mappingInstruction->class, $key);
        } else {
            $versionId = null;
            if($this->_mappingInstruction->versionProperty !== null) {
                $versionColumnName = $this->_mappingInstruction->versionProperty->columnName;
                if(isset($row[$versionColumnName])) {
                    $versionId = $row[$versionColumnName];
                } else {
                    throw new Zend_Entity_Exception(
                        "Missing version column '".$versionColumnName."' in entity resultset"
                    );
                }
            }

            $entity = $this->createEntity($row);
            // Set this before loading relations to circumvent infinite loop on backreferences and stuff
            $identityMap->addObject($this->_mappingInstruction->class, $key, $entity, $versionId);

            $this->loadRow($entity, $row, $entityManager);
        }
        return $entity;
    }

    protected function createEntity(array $row)
    {
        $entityClass = $this->_mappingInstruction->class;
        return new $entityClass();
    }

    public function loadRow(Zend_Entity_Interface $entity, array $row, Zend_Entity_Manager_Interface $entityManager)
    {
        $state = array();
        foreach($this->_mappingInstruction->columnNameToProperty AS $columnName => $property) {
            if(!array_key_exists($columnName, $row)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "In rename column to property the column '".$columnName."' does not exist in resultset."
                );
            }
            $state[$property->propertyName] = $property->castColumnToPhpType($row[$columnName]);
        }
        unset($row);

        $state = $this->initializeRelatedObjects($state, $entityManager);
        $this->_mappingInstruction->stateTransformer->setState($entity, $state);
        
        $entityManager->getEventListener()->postLoad($entity);
    }
    
    protected function initializeRelatedObjects(array $entityState, Zend_Entity_Manager $entityManager)
    {
        foreach($this->_mappingInstruction->toOneRelations AS $relation) {
            /* @var $relation Zend_Entity_Definition_RelationAbstract */
            $propertyName = $relation->propertyName;
            if($relation->fetch == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $entityState[$propertyName] = $entityManager->getReference(
                    $relation->class, $entityState[$propertyName]
                );
            } else if($relation->fetch == Zend_Entity_Definition_Property::FETCH_SELECT) {
                $entityState[$propertyName] = $entityManager->load(
                    $relation->class, $entityState[$propertyName], $relation->notFound
                );
            }
        }
        foreach($this->_mappingInstruction->toManyRelations AS $collectionDef) {
            /* @var $collectionDef Zend_Entity_Definition_Collection */
            $relation = $collectionDef->relation;
            $foreignDefinition = $entityManager->getMetadataFactory()->getDefinitionByEntityName($relation->class);

            $keyValue = $entityState[$this->_mappingInstruction->primaryKey->propertyName];

            $db = $entityManager->getAdapter();
            $query = $entityManager->createNativeQuery($relation->class);

            $intersectTable = $collectionDef->table;
            if($foreignDefinition->getTable() !== $collectionDef->table) {
                
                $foreignPrimaryKey = $foreignDefinition->getPrimaryKey()->columnName;

                $intersectOnLhs = $db->quoteIdentifier($intersectTable.".".$relation->columnName);
                $intersectOnRhs = $db->quoteIdentifier($foreignDefinition->getTable().".".$foreignPrimaryKey);
                $intersectOn = $intersectOnLhs." = ".$intersectOnRhs;
                $query->join($intersectTable, $intersectOn, array());
            }
            $query->where( $db->quoteIdentifier($intersectTable.".".$collectionDef->key)." = ?", $keyValue);

            if($collectionDef->orderByRestriction !== null) {
                $query->order($collectionDef->orderByRestriction);
            }

            if($collectionDef->whereRestriction !== null) {
                $query->where($collectionDef->whereRestriction);
            }

            $propertyName = $collectionDef->propertyName;

            if($collectionDef->fetch == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $callback = array($query, "getResultList");
                $entityState[$propertyName] = new Zend_Entity_LazyLoad_Collection($callback, array());
            } else if($collectionDef->fetch == Zend_Entity_Definition_Property::FETCH_SELECT) {
                $entityState[$propertyName] = new Zend_Entity_Collection($query->getResultList());
            }
        }
        foreach($this->_mappingInstruction->elementCollections AS $elementDef) {
            $propertyName = $elementDef->propertyName;
            $pk = $this->_mappingInstruction->primaryKey->propertyName;

            /* @var $elementDef Zend_Entity_Definition_Collection */
            $db = $entityManager->getAdapter();
            $select = $db->select();
            $select->from($elementDef->table);
            $select->where($elementDef->key." = ?", $entityState[$pk]);

            if($elementDef->fetch == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $entityState[$propertyName] = new Zend_Entity_LazyLoad_ElementHashMap(
                    $select, $elementDef->mapKey, $elementDef->element
                );
            } else {
                $stmt = $select->query();

                $elements = array();
                foreach($stmt->fetchAll() AS $row) {
                    $elements[$row[$elementDef->mapKey]] = $row[$elementDef->element];
                }

                $entityState[$propertyName] = new Zend_Entity_Collection_ElementHashMap($elements);
            }
        }

        return $entityState;
    }
}