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
     * @var array
     */
    protected $_toManyCascadeRelations = array();

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
     * @var Zend_Entity_StateTransformer_Abstract
     */
    protected $_stateTransformer = null;

    /**
     * Initialize is called once on each persister to gather information on how to perform the persist operation.
     *
     * @param  Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param  Zend_Entity_MetadataFactory_Interface     $defMap
     * @return void
     */
    public function initialize(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_MetadataFactory_Interface $defMap)
    {
        $properties = array();
        foreach($entityDef->getProperties() AS $property) {
            if(!($property instanceof Zend_Entity_Mapper_Definition_Formula)) {
                $properties[] = $property;
            }
        }
        $this->_properties = $properties;
        foreach($entityDef->getRelations() AS $relation) {
            if($relation->isOwning()) {
                $this->_toOneRelations[$relation->getPropertyName()] = $relation;
            }
        }
        foreach($entityDef->getExtensions() AS $collection) {
            if($this->isCascadingToManyCollection($collection)) {
                $this->_toManyCascadeRelations[] = $collection;
            }
        }
        $this->_class            = $entityDef->getClass();
        $this->_primaryKey       = $entityDef->getPrimaryKey();
        $this->_table            = $entityDef->getTable();
        $this->_stateTransformer = $entityDef->getStateTransformer();
    }

    /**
     * Is this is a cascading collection?
     * 
     * @param Zend_Entity_Mapper_Definition_Collection $collection
     * @return boolean
     */
    private function isCascadingToManyCollection(Zend_Entity_Mapper_Definition_Collection $collection)
    {
        return( ($collection->getCollectionType() == Zend_Entity_Mapper_Definition_Collection::COLLECTION_RELATION) &&
            ($collection->getRelation()->getCascade() != Zend_Entity_Mapper_Definition_Property::CASCADE_NONE) );
    }

    /**
     * @ignore
     * @param Zend_Entity_Interface $relatedObject
     * @param Zend_Entity_Mapper_Definition_AbstractRelation $relationDef
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return mixed
     */
    public function evaluateRelatedObject($relatedObject, $relationDef, $entityManager)
    {
        if($relatedObject instanceof Zend_Entity_LazyLoad_Entity && $relatedObject->entityWasLoaded() == false) {
            $value = $relatedObject->getLazyLoadEntityId();
        } else if($relatedObject instanceof Zend_Entity_Interface) {
            $foreignKeyProperty = $relationDef->getForeignKeyPropertyName();
            $relatedObjectState = $relatedObject->getState();
            $value = $relatedObjectState[$foreignKeyProperty];

            switch($relationDef->getCascade()) {
                case Zend_Entity_Mapper_Definition_Property::CASCADE_ALL:
                case Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE:
                    $entityManager->save($relatedObject);
                    break;
            }
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * @ignore
     * @param Zend_Entity_Collection_Interface $relatedCollection
     * @param Zend_Entity_Mapper_Definition_Collection $collectionDef
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    public function evaluateRelatedCollection($keyValue, $relatedCollection, $collectionDef, $entityManager)
    {
        if($relatedCollection instanceof Zend_Entity_Collection_Interface
            && $relatedCollection->wasLoadedFromDatabase() == true) {
            /* @var $relatedCollection Zend_Entity_Mapper_Definition_AbstractRelation */
            switch($collectionDef->getRelation()->getCascade()) {
                case Zend_Entity_Mapper_Definition_Property::CASCADE_ALL:
                case Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE:
                    foreach($relatedCollection AS $collectionEntity) {
                        $entityManager->save($collectionEntity);
                    }
                    break;
            }
        }

        if($collectionDef->getRelation() instanceof Zend_Entity_Mapper_Definition_ManyToManyRelation) {
            $db = $entityManager->getAdapter();
            $identityMap = $entityManager->getIdentityMap();

            $key = $collectionDef->getKey();
            $foreignKey = $collectionDef->getRelation()->getColumnName();
            foreach($relatedCollection->getAdded() AS $relatedEntity) {
                $db->insert($collectionDef->getTable(), array(
                    $key => $keyValue,
                    $foreignKey => $identityMap->getPrimaryKey($relatedEntity),
                ));
            }
            foreach($relatedCollection->getRemoved() AS $relatedEntity) {
                $db->delete($collectionDef->getTable(), 
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
        $entityState = $this->_stateTransformer->getState($entity);
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
        foreach($this->_toManyCascadeRelations AS $collectionDef) {
            $relatedCollection = $entityState[$collectionDef->getPropertyName()];

            $this->evaluateRelatedCollection($key, $relatedCollection, $collectionDef, $entityManager);
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
        foreach($this->_properties AS $property) {
            $propertyName = $property->getPropertyName();
            $columnName   = $property->getColumnName();
            $propertyValue = $property->castPropertyToSqlType($entityState[$propertyName]);
            $dbState[$columnName] = $propertyValue;
        }
        foreach($this->_toOneRelations AS $relation) {
            $propertyName = $relation->getPropertyName();
            $relatedObject      = $entityState[$propertyName];
            $dbState[$relation->getColumnName()] = $this->evaluateRelatedObject(
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
        $dbAdapter = $entityManager->getAdapter();
        $pk        = $this->_primaryKey;
        $tableName = $this->_table;
        $identityMap = $entityManager->getIdentityMap();
        if($identityMap->contains($entity) == false) {
            $dbState = array_merge(
                $dbState,
                $pk->applyNextSequenceId($dbAdapter, $dbState)
            );
            $dbAdapter->insert($tableName, $dbState);
            $key = $pk->lastSequenceId($dbAdapter, $dbState);
            $this->_stateTransformer->setId($entity, $pk->getPropertyName(), $key);

            $identityMap->addObject(
                $this->_class,
                $key,
                $entity
            );
        } else {
            $key = $identityMap->getPrimaryKey($entity);
            $where = $pk->buildWhereCondition(
                $dbAdapter,
                $tableName,
                $identityMap->getPrimaryKey($entity)
            );
            $dbState = $pk->removeSequenceFromState($dbState);
            $dbAdapter->update($tableName, $dbState, $where);
        }
        return $key;
    }

    /**
     * Remove entity from persistence based on the persisters scope
     *
     * @ignore
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity, Zend_Entity_Manager_Interface $entityManager)
    {
        $identityMap = $entityManager->getIdentityMap();
        $db          = $entityManager->getAdapter();
        
        $tableName   = $this->_table;
        $whereClause = $this->_primaryKey->buildWhereCondition($db, $tableName, $identityMap->getPrimaryKey($entity));
        
        $db->delete($tableName, $whereClause);
        
        $this->_stateTransformer->setId($entity, $this->_primaryKey->getPropertyName(), null);
        $identityMap->remove($this->_class, $entity);
    }
}