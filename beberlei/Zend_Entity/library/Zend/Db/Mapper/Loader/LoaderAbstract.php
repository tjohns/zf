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
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract Loader
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Mapper_Loader_LoaderAbstract
{
    /**
     * @var Zend_Db_Mapper_Mapping
     */
    protected $_mappings = null;
    
    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_em;

    /**
     * Construct new Loader based on an entity definition.
     * 
     * @param Zend_Entity_Manager_Interface $em
     * @param Zend_Db_Mapper_Mapping[] $mappings
     */
    public function __construct(Zend_Entity_Manager_Interface $em, array $mappings)
    {
        $this->_em = $em;
        $this->_mappings = $mappings;
    }

    /**
     * @param  array $resultSet
     * @param  string $fetchMode
     * @return Zend_Entity_Collection_Interface
     */
    abstract public function processResultset($resultSet, Zend_Entity_Query_ResultSetMapping $rsm);

    /**
     * @todo Gah Code duplication
     * @param  array $row
     * @return array
     */
    protected function renameAndCastColumnToPropertyKeys($row, $mapping)
    {
        $state = array();
        foreach($mapping->columnNameToProperty AS $columnName => $property) {
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

    public function createEntityFromRow(array $row, $mapping)
    {
        $identityMap = $this->_em->getIdentityMap();
        $key = $mapping->primaryKey->retrieveKeyValuesFromProperties($row);
        if($identityMap->hasObject($mapping->class, $key) == true) {
            $entity = $identityMap->getObject($mapping->class, $key);
        } else {
            $versionId = null;
            if($mapping->versionProperty !== null) {
                $versionColumnName = $mapping->versionProperty->columnName;
                if(isset($row[$versionColumnName])) {
                    $versionId = $row[$versionColumnName];
                } else {
                    throw new Zend_Entity_Exception(
                        "Missing version column '".$versionColumnName."' in entity resultset"
                    );
                }
            }

            $entity = $this->createEntity($row, $mapping);
            // Set this before loading relations to circumvent infinite loop on backreferences and stuff
            $identityMap->addObject($mapping->class, $key, $entity, $versionId);

            $this->loadRow($entity, $row, $mapping);
        }
        return $entity;
    }

    protected function createEntity(array $row, $mapping = null)
    {
        $entityClass = $mapping->class;
        return new $entityClass();
    }

    public function loadRow(Zend_Entity_Interface $entity, array $row, $mapping)
    {
        $state = array();
        foreach($mapping->columnNameToProperty AS $columnName => $property) {
            if(!array_key_exists($columnName, $row)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "In rename column to property the column '".$columnName."' does not exist in resultset."
                );
            }
            $state[$property->propertyName] = $property->castColumnToPhpType($row[$columnName]);
        }
        unset($row);

        $state = $this->initializeRelatedObjects($state, $mapping);
        $mapping->stateTransformer->setState($entity, $state);
        
        $this->_em->getEventListener()->postLoad($entity);
    }
    
    protected function initializeRelatedObjects(array $entityState, $mapping)
    {
        $entityManager = $this->_em;
        $db = $entityManager->getMapper()->getAdapter();
        foreach($mapping->toOneRelations AS $relation) {
            /* @var $relation Zend_Entity_Definition_RelationAbstract */
            $propertyName = $relation->propertyName;

            // it seems the object was deeply created already
            if(is_object($entityState[$propertyName])) {
                continue;
            }

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
        foreach($mapping->toManyRelations AS $collectionDef) {
            /* @var $collectionDef Zend_Entity_Definition_Collection */
            $relation = $collectionDef->relation;
            $foreignDefinition = $entityManager->getMetadataFactory()->getDefinitionByEntityName($relation->class);

            $keyValue = $entityState[$mapping->primaryKey->propertyName];

            $query = new Zend_Db_Mapper_SqlQueryBuilder($entityManager);
            $query->from($this->_mappings[$relation->class]->table)
                  ->with($relation->class);

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
        foreach($mapping->elementCollections AS $elementDef) {
            $propertyName = $elementDef->propertyName;
            $pk = $mapping->primaryKey->propertyName;

            /* @var $elementDef Zend_Entity_Definition_Collection */
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