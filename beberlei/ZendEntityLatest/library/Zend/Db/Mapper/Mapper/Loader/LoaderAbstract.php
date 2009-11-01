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
     * @var Zend_Entity_StateTransformer_TypeConverter
     */
    protected $_typeConverter = null;

    /**
     * Construct new Loader based on an entity definition.
     * 
     * @param Zend_Entity_Manager_Interface $em
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $mappings
     */
    public function __construct(Zend_Entity_Manager_Interface $em, Zend_Entity_MetadataFactory_FactoryAbstract $mappings)
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
     * @param array $state
     * @param Zend_Db_Mapper_Mapping $mapping
     * @return object
     */
    public function createEntityFromState(array $state, $mapping)
    {
        $identityMap = $this->_em->getIdentityMap();
        $pkPropertyName = $mapping->primaryKey->propertyName;
        $key = $state[$pkPropertyName];
        
        if($identityMap->hasObject($mapping->class, $key) == true) {
            $entity = $identityMap->getObject($mapping->class, $key);
        } else {
            $versionId = null;
            if($mapping->versionProperty !== null) {
                $versionPropertyName = $mapping->versionProperty->propertyName;
                if(isset($state[$versionPropertyName])) {
                    $versionId = $state[$versionPropertyName];
                } else {
                    throw new Zend_Entity_Exception(
                        "Missing version property '".$versionPropertyName."' in entity resultset"
                    );
                }
            }

            $entity = $this->createEntity($state, $mapping);
            // Set this before loading relations to circumvent infinite loop on backreferences and stuff
            $identityMap->addObject($mapping->class, $key, $entity, $versionId);

            $this->loadState($entity, $state, $mapping);
        }
        return $entity;
    }

    protected function createEntity(array $row, $mapping = null)
    {
        $entityClass = $mapping->class;
        return new $entityClass();
    }

    public function loadState($entity, array $state, $mapping)
    {
        $state = $this->initializeRelatedObjects($state, $mapping, $entity);
        $mapping->stateTransformer->setState($entity, $state);
        
        $this->_em->getEventListener()->postLoad($entity);
    }
    
    protected function initializeRelatedObjects(array $entityState, $mapping, $entity)
    {
        $entityManager = $this->_em;
        $db = $entityManager->getMapper()->getAdapter();

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
                $query->join($intersectTable, $intersectOn);
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
            $entityManager->getIdentityMap()->storeRelatedObject($entity, $propertyName, $entityState[$propertyName]);
        }

        foreach($mapping->toOneRelations AS $relation) {
            /* @var $relation Zend_Entity_Definition_RelationAbstract */
            $propertyName = $relation->propertyName;

            // it seems the object was deeply created already
            if(is_object($entityState[$propertyName])) {
                continue;
            }

            $relatedId = $entityState[$propertyName];
            if($relatedId === null) {
                if($relation->nullable == true) {
                    $entityState[$propertyName] = null;
                } else {
                    throw new Zend_Entity_InvalidEntityException(
                        "Property '".$propertyName."' is not allowed to be null on entity '".$mapping->class."'."
                    );
                }
                continue;
            } elseif($relation->fetch == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $entityState[$propertyName] = $entityManager->getReference(
                    $relation->class, $relatedId
                );
            } else if($relation->fetch == Zend_Entity_Definition_Property::FETCH_SELECT) {
                // @todo "Nullable or not" does not seem to be tested yet
                $entityState[$propertyName] = $entityManager->load(
                    $relation->class, $relatedId, ($relation->nullable)?"null":"exception"
                );
            }
            $entityManager->getIdentityMap()->storeRelatedObject($entity, $propertyName, $entityState[$propertyName]);
        }

        foreach($mapping->elementCollections AS $elementDef) {
            $propertyName = $elementDef->propertyName;
            $pk = $mapping->primaryKey->propertyName;

            /* @var $elementDef Zend_Entity_Definition_Collection */
            $select = $db->select();
            $select->from($elementDef->table);
            $select->where($elementDef->key." = ?", $entityState[$pk]);

            if($elementDef->fetch == Zend_Entity_Definition_Property::FETCH_LAZY) {
                $entityState[$propertyName] = new Zend_Entity_LazyLoad_Array(
                    $select, $elementDef->mapKey, $elementDef->element
                );
            } else {
                $stmt = $select->query();

                $elements = array();
                foreach($stmt->fetchAll() AS $row) {
                    $elements[$row[$elementDef->mapKey]] = $row[$elementDef->element];
                }

                $entityState[$propertyName] = new Zend_Entity_Collection_Array($elements);
            }
            $entityManager->getIdentityMap()->storeRelatedObject($entity, $propertyName, $entityState[$propertyName]);
        }

        return $entityState;
    }

    /**
     * Verification of a ResultSetMapping against a row for Entity, Array or Scalar loading.
     *
     * Although this validation seems computationally intensive, its overhead
     * can be neglected even in in production as it only has to be executed
     * on the first row of a resultset, relational databases enforce all the
     * following rows to be valid also.
     *
     * @param array $row
     * @param Zend_Entity_Query_ResultSetMapping $rsm
     * @throws Zend_Entity_Query_InvalidResultSetMappingException
     */
    protected function _validateResultSet($row, $rsm)
    {
        foreach($rsm->storageFieldEntity AS $reqCol => $entityAlias) {
            $entityName = $rsm->aliasToEntity[$entityAlias];
            if(!array_key_exists($reqCol, $row)) {
                throw new Zend_Entity_Query_InvalidResultSetMappingException(
                    "Required column '".$reqCol."' for entity '".$entityName."' ".
                    "is not present in the database result."
                );
            }
            if(!isset($this->_mappings[$entityName])) {
                throw new Zend_Entity_Query_InvalidResultSetMappingException(
                    "Specified entity '".$entityName."' does not exist in ".
                    "the entity mappings."
                );
            }
            $propertyName = $rsm->entityResult[$entityAlias]['properties'][$reqCol];
            if(!isset($this->_mappings[$entityName]->newProperties[$propertyName])) {
                throw new Zend_Entity_Query_InvalidResultSetMappingException(
                    "Specified ResultSetMapping property '".$propertyName."' ".
                    "for result column '".$reqCol."' ".
                    "does not exist on mapping of entity '".$entityName."'."
                );
            }
        }
        foreach($rsm->scalarResult AS $scalarCol) {
            if(!array_key_exists($scalarCol, $row)) {
                throw new Zend_Entity_Query_InvalidResultSetMappingException(
                    "Required scalar result column '".$scalarCol."' is not ".
                    "present in the database result."
                );
            }
        }
    }

    /**
     * Prepares the row and splits it into entity data and scalar value returns.
     *
     * @param array $row
     * @param array $data
     * @param array $scalars
     * @param Zend_Entity_Query_ResultSetMapping $rsm
     */
    protected function _prepareData(&$row, &$data, &$scalars, $rsm)
    {
        $tc = $this->getTypeConverter();

        foreach($row AS $sqlResultKey => $sqlResultValue) {
            if(in_array($sqlResultKey, $rsm->scalarResult)) {
                $scalars[$sqlResultKey] = $sqlResultValue;
            } elseif(isset($rsm->storageFieldEntity[$sqlResultKey])) {
                $aliasName = $rsm->storageFieldEntity[$sqlResultKey];
                $entityName = $rsm->aliasToEntity[$aliasName];
                $propertyName = $rsm->entityResult[$aliasName]['properties'][$sqlResultKey];

                $type = $this->_mappings[$entityName]->newProperties[$propertyName]->propertyType;
                $nullable = $this->_mappings[$entityName]->newProperties[$propertyName]->nullable;
                $data[$aliasName][$propertyName] = $tc->convertToPhpType($type, $sqlResultValue, $nullable);
            }
        }
    }

    /**
     * @return Zend_Entity_StateTransformer_TypeConverter
     */
    public function getTypeConverter()
    {
        if($this->_typeConverter == null) {
            $this->_typeConverter = new Zend_Entity_StateTransformer_TypeConverter();
        }
        return $this->_typeConverter;
    }

    /**
     * @param Zend_Entity_StateTransformer_TypeConverter $typeConverter
     * @return Zend_Entity_MapperAbstract
     */
    public function setTypeConverter(Zend_Entity_StateTransformer_TypeConverter $typeConverter)
    {
        $this->_typeConverter = $typeConverter;
        return $this;
    }
}
