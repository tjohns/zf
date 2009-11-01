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
 * Simple Persister
 *
 * @uses       Zend_Db_Mapper_Persister_Interface
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Persister_Simple implements Zend_Db_Mapper_Persister_Interface
{
    /**
     * @var Zend_Db_Mapper_Mapping
     */
    protected $_mappingInstruction = null;

    /**
     * @var array
     */
    protected $_subPersisters = array();

    /**
     * @var Zend_Entity_StateTransformer_TypeConverter
     */
    protected $_typeConverter = null;

    /**
     * Initialize is called once on each persister to gather information on how to perform the persist operation.
     *
     * @param  Zend_Db_Mapper_Mapping $mappingInstruction
     * @return void
     */
    public function initialize(Zend_Db_Mapper_Mapping $mappingInstruction)
    {
        $this->_mappingInstruction = $mappingInstruction;
    }

    /**
     * @param string $propertyName
     */
    protected function _getSubPersister($propertyName)
    {
        if(!isset($this->_subPersisters[$propertyName])) {
            $property = $this->_mappingInstruction->newProperties[$propertyName];
            if($property instanceof Zend_Entity_Definition_Collection) {
                $this->_subPersisters[$propertyName] = new Zend_Db_Mapper_Persister_Collection($property);
            } elseif($property instanceof Zend_Entity_Definition_Array) {
                $this->_subPersisters[$propertyName] = new Zend_Db_Mapper_Persister_Array($property);
            }
        }
        return $this->_subPersisters[$propertyName];
    }

    /**
     * @ignore
     * @param object $relatedObject
     * @param Zend_Entity_Definition_RelationAbstract $relationDef
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return mixed
     */
    public function evaluateRelatedObject($relatedObject, $relationDef, $entityManager)
    {
        if(is_object($relatedObject)) {
            /*switch($relationDef->cascade) {
                case Zend_Entity_Definition_Property::CASCADE_ALL:
                case Zend_Entity_Definition_Property::CASCADE_SAVE:
                    $entityManager->save($relatedObject);
                    break;
            }*/
            $value = $entityManager->getIdentityMap()->getPrimaryKey($relatedObject);
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * @ignore
     * @param Zend_Entity_Collection_Interface $relatedCollection
     * @param Zend_Entity_Definition_Collection $collectionDef
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    public function evaluateRelatedCollection($keyValue, $relatedCollection, $collectionDef, $entityManager)
    {
        if(is_array($relatedCollection)) {
            $relatedCollection = new Zend_Entity_Collection($relatedCollection);
        }

        if($relatedCollection instanceof Zend_Entity_Collection_Interface
            && $relatedCollection->__ze_wasLoadedFromDatabase() == true) {
            /* @var $relatedCollection Zend_Entity_Definition_RelationAbstract */
            if(in_array(Zend_Entity_Definition_Property::CASCADE_PERSIST, $collectionDef->relation->cascade) ||
               in_array(Zend_Entity_Definition_Property::CASCADE_ALL, $collectionDef->relation->cascade)) {
                foreach($relatedCollection AS $collectionEntity) {
                    $entityManager->persist($collectionEntity);
                }
            }
        }

        if($collectionDef->relation instanceof Zend_Entity_Definition_ManyToManyRelation) {
            $colPersister = $this->_getSubPersister($collectionDef->propertyName);
            $colPersister->persist($keyValue, $relatedCollection, $entityManager);
        }
    }

    /**
     * Save entity into persistence based on the persisters scope
     *
     * @param  object $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function save($entity, Zend_Entity_Manager_Interface $entityManager)
    {
        $entityState = $this->_mappingInstruction->stateTransformer->getState($entity);
        $dbState = $this->transformEntityToDbState($entityState, $entityManager);
        $key = $this->doPerformSave($entity, $dbState, $entityManager);
        $this->updateCollections($key, $entityState, $entityManager);
    }

    /**
     * @ignore
     * @param array $entityState
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    public function updateCollections($key, $entityState, $entityManager)
    {
        foreach($this->_mappingInstruction->toOneRelations AS $relation) {
            if(in_array(Zend_Entity_Definition_Property::CASCADE_PERSIST, $relation->cascade) ||
               in_array(Zend_Entity_Definition_Property::CASCADE_ALL, $relation->cascade)) {
                $propertyName = $relation->propertyName;
                $relatedObject = $entityState[$propertyName];

                $entityManager->persist($relatedObject);
            }
        }

        foreach($this->_mappingInstruction->toManyRelations AS $collectionDef) {
            $relatedCollection = $entityState[$collectionDef->propertyName];

            $this->evaluateRelatedCollection($key, $relatedCollection, $collectionDef, $entityManager);
        }
        foreach($this->_mappingInstruction->elementCollections AS $elementDef) {
            $arrayObject = $entityState[$elementDef->propertyName];
            $db = $entityManager->getMapper()->getAdapter();
            if($arrayObject instanceof Zend_Entity_Collection_Array) {
                $persister = $this->_getSubPersister($elementDef->propertyName);
                $persister->persist($key, $arrayObject, $entityManager);
            } else {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception();
            }
        }
    }

    /**
     * @ignore
     * @param  array $entityState
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return array
     */
    public function transformEntityToDbState($entityState, $entityManager)
    {
        $tc = $this->getTypeConverter();

        foreach($this->_mappingInstruction->columnNameToProperty AS $columnName => $propertyName) {
            $property = $this->_mappingInstruction->newProperties[$propertyName];
            if($property instanceof Zend_Entity_Definition_RelationAbstract) {
                $relatedObject = $entityState[$propertyName];
                $dbState[$columnName] = $this->evaluateRelatedObject(
                    $relatedObject,
                    $property,
                    $entityManager
                );
            } else if(!($property instanceof Zend_Entity_Definition_ArrayAbstract)) {
                $dbState[$columnName] = $tc->convertToStorageType(
                    $property->propertyType, $entityState[$propertyName], $property->nullable
                );
            }
        }
        return $dbState;
    }

    /**
     * @ignore
     * @param object $entity
     * @param array $dbState
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return int|string
     */
    public function doPerformSave($entity, $dbState, $entityManager)
    {
        $db = $entityManager->getMapper()->getAdapter();
        $pk = $this->_mappingInstruction->primaryKey;
        $tableName = $this->_mappingInstruction->table;
        $identityMap = $entityManager->getIdentityMap();
        if($identityMap->contains($entity) == false) {
            if($entityManager->getEventListener()->preInsert($entity) == false) {
                return;
            }

            if($this->_mappingInstruction->versionProperty !== null) {
                $versionColumnName = $this->_mappingInstruction->versionProperty->columnName;
                $dbState[$versionColumnName] = 1;
            }

            // wtf is this, fix this mess!
            $key = null;
            if($pk->getGenerator()->isPrePersistGenerator()) {
                $keyState = array($pk->columnName => $pk->getGenerator()->generate($entityManager, $entity));
                $dbState  = array_merge($dbState, $keyState);
                $key = array_shift($keyState);
            }

            $db->insert($tableName, $dbState);

            if($pk->getGenerator()->isPrePersistGenerator() == false) {
                $key = $pk->getGenerator()->generate($entityManager, $entity);
            }
            $this->_mappingInstruction->stateTransformer->setId($entity, $pk->propertyName, $key);

            $identityMap->addObject(
                $this->_mappingInstruction->class, $key, $entity, 1
            );

            $entityManager->getEventListener()->postInsert($entity);
        } else {
            if($entityManager->getEventListener()->preUpdate($entity) == false) {
                return;
            }

            $key = $identityMap->getPrimaryKey($entity);
            $where = $pk->buildWhereCondition(
                $db,
                $tableName,
                $identityMap->getPrimaryKey($entity)
            );

            if($this->_mappingInstruction->versionProperty !== null) {
                $versionColumnName = $this->_mappingInstruction->versionProperty->columnName;
                $versionId = $identityMap->getVersion($entity);
                $dbState[$versionColumnName] = $versionId+1;
                $where .= " AND ".$db->quoteInto(
                    $tableName.".".$versionColumnName." = ?", $versionId
                );
            }

            $dbState = $pk->removeSequenceFromState($dbState);
            $rows = $db->update($tableName, $dbState, $where);

            if($this->_mappingInstruction->versionProperty !== null) {
                if($rows == 0) {
                    throw new Zend_Entity_OptimisticLockException($entity);
                } else {
                    $identityMap->setVersion($entity, $versionId+1);
                }
            }

            $entityManager->getEventListener()->postUpdate($entity);
        }
        return $key;
    }

    /**
     * Remove entity from persistence based on the persisters scope
     *
     * @param  object $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function delete($entity, Zend_Entity_Manager_Interface $entityManager)
    {
        if($entityManager->getEventListener()->preDelete($entity) == false) {
            return;
        }

        $identityMap = $entityManager->getIdentityMap();
        $db = $entityManager->getMapper()->getAdapter();
        
        $tableName   = $this->_mappingInstruction->table;
        $whereClause = $this->_mappingInstruction->primaryKey
            ->buildWhereCondition($db, $tableName, $identityMap->getPrimaryKey($entity));

        if($this->_mappingInstruction->versionProperty !== null) {
            $versionColumnName = $this->_mappingInstruction->versionProperty->columnName;
            $versionId = $identityMap->getVersion($entity);
            $whereClause .= " AND ".$db->quoteInto($tableName.".".$versionColumnName." = ?", $versionId);
        }

        $numDeleted = $db->delete($tableName, $whereClause);

        if($numDeleted == 0 && $this->_mappingInstruction->versionProperty !== null) {
            throw new Zend_Entity_OptimisticLockException($entity);
        }
        
        $this->_mappingInstruction->stateTransformer
            ->setId($entity, $this->_mappingInstruction->primaryKey->propertyName, null);
        $identityMap->remove($entity);

        $entityManager->getEventListener()->postDelete($entity);
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