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

class Zend_Db_Mapper_Persister_Simple implements Zend_Db_Mapper_Persister_Interface
{
    /**
     * @var Zend_Db_Mapper_Mapping
     */
    protected $_mappingInstruction = null;

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
     * @ignore
     * @param Zend_Entity_Interface $relatedObject
     * @param Zend_Entity_Definition_RelationAbstract $relationDef
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return mixed
     */
    public function evaluateRelatedObject($relatedObject, $relationDef, $entityManager)
    {
        if($relatedObject instanceof Zend_Entity_LazyLoad_Entity && $relatedObject->entityWasLoaded() == false) {
            $value = $relatedObject->getLazyLoadEntityId();
        } else if($relatedObject instanceof Zend_Entity_Interface) {
            $foreignKeyPropertyName = $relationDef->mappedBy;

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
            switch($collectionDef->relation->cascade) {
                case Zend_Entity_Definition_Property::CASCADE_ALL:
                case Zend_Entity_Definition_Property::CASCADE_SAVE:
                    foreach($relatedCollection AS $collectionEntity) {
                        $entityManager->save($collectionEntity);
                    }
                    break;
            }
        }

        if($collectionDef->relation instanceof Zend_Entity_Definition_ManyToManyRelation) {
            $db = $entityManager->getMapper()->getAdapter();
            $identityMap = $entityManager->getIdentityMap();

            $key = $collectionDef->key;
            $foreignKey = $collectionDef->relation->columnName;
            foreach($relatedCollection->__ze_getAdded() AS $relatedEntity) {
                $db->insert($collectionDef->table, array(
                    $key => $keyValue,
                    $foreignKey => $identityMap->getPrimaryKey($relatedEntity),
                ));
            }
            foreach($relatedCollection->__ze_getRemoved() AS $relatedEntity) {
                $db->delete($collectionDef->table,
                    $db->quoteIdentifier($key)." = ".$db->quote($keyValue)." AND ".
                    $db->quoteIdentifier($foreignKey)." = ".$db->quote($identityMap->getPrimaryKey($relatedEntity))
                );
            }
        }
    }

    /**
     * Save entity into persistence based on the persisters scope
     *
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function save(Zend_Entity_Interface $entity, Zend_Entity_Manager_Interface $entityManager)
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
            switch($relation->cascade) {
                case Zend_Entity_Definition_Property::CASCADE_ALL:
                case Zend_Entity_Definition_Property::CASCADE_SAVE:
                    $propertyName = $relation->propertyName;
                    $relatedObject = $entityState[$propertyName];

                    $entityManager->save($relatedObject);
                    break;
            }
        }

        foreach($this->_mappingInstruction->toManyRelations AS $collectionDef) {
            $relatedCollection = $entityState[$collectionDef->propertyName];

            $this->evaluateRelatedCollection($key, $relatedCollection, $collectionDef, $entityManager);
        }
        foreach($this->_mappingInstruction->elementCollections AS $elementDef) {
            $elementHashMap = $entityState[$elementDef->propertyName];
            $db = $entityManager->getMapper()->getAdapter();
            if($elementHashMap instanceof Zend_Entity_Collection_ElementHashMap) {
                /* @var $elementHashMap Zend_Entity_Collection_ElementHashMap */
                foreach($elementHashMap->__ze_getRemoved() AS $k => $v) {
                    $db->delete(
                        $elementDef->table,
                        implode(" AND ", array(
                            $db->quoteInto($elementDef->key." = ?", $key),
                            $db->quoteInto($elementDef->mapKey." = ?", $k)
                        ))
                    );
                }

                foreach($elementHashMap->__ze_getAdded() AS $k => $v) {
                    $db->insert(
                        $elementDef->table,
                        array(
                            $elementDef->key => $key,
                            $elementDef->mapKey=> $k,
                            $elementDef->element => $v,
                        )
                    );
                }
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
        $dbState = array();
        foreach($this->_mappingInstruction->properties AS $property) {
            $propertyName = $property->propertyName;
            $propertyValue = $property->castPropertyToStorageType($entityState[$propertyName]);
            $dbState[$property->columnName] = $propertyValue;
        }
        foreach($this->_mappingInstruction->toOneRelations AS $relation) {
            $propertyName = $relation->propertyName;
            $relatedObject = $entityState[$propertyName];
            $dbState[$relation->columnName] = $this->evaluateRelatedObject(
                $relatedObject,
                $relation,
                $entityManager
            );
        }
        return $dbState;
    }

    /**
     * @ignore
     * @param Zend_Entity_Interface $entity
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

            $dbState = array_merge(
                $dbState,
                $pk->applyNextSequenceId($db, $dbState)
            );
            $db->insert($tableName, $dbState);
            $key = $pk->lastSequenceId($db, $dbState);
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
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity, Zend_Entity_Manager_Interface $entityManager)
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
        $identityMap->remove($this->_mappingInstruction->class, $entity);

        $entityManager->getEventListener()->postDelete($entity);
    }
}