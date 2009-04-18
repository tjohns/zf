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

abstract class Zend_Entity_Mapper_Abstract implements Zend_Entity_Mapper_Interface
{
    /**
     * Zend Database adapter
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Object holding the table definition information.
     *
     * @var Zend_Entity_Mapper_Definition_Entity
     */
    protected $_entityDefinition = null;

    /**
     * Entity Resource Map (of all the other entities)
     *
     * @var Zend_Entity_Resource_Interface
     */
    protected $_entityResourceMap = null;

    /**
     * Loader
     *
     * @var Zend_Entity_Mapper_Loader_Interface
     */
    protected $_loader = null;

    /**
     * Persister
     *
     * @var Zend_Entity_Mapper_Persister_Interface
     */
    protected $_persister = null;

    /**
     * Construct DataMapper
     *
     * @param  Zend_Db_Adapter_Abstract  $db
     * @param  Zend_Entity_Mapper_Definition_Entity $def
     * @param  Zend_Entity_Resource_Interface $map
     */
    public function __construct(Zend_Db_Adapter_Abstract $db, Zend_Entity_Mapper_Definition_Entity $def, Zend_Entity_Resource_Interface $map)
    {
        $this->_db = $db;
        $this->_entityDefinition = $def;
        $this->_entityResourceMap = $map;
    }

    /**
     * Access the object mapper and retrieve data.
     *
     * @param  Zend_Db_Select $criteria
     * @return Zend_Entity_Interface[]
     */
    public function find(Zend_Db_Select $select, Zend_Entity_Manager $entityManager)
    {
        return $this->query($select, $entityManager);
    }

    /**
     * Find one and only one instance matching the select.
     *
     * @throws Exception
     * @param  Zend_Db_Select $criteria
     * @return Zend_Entity_Interface
     */
    public function findOne(Zend_Db_Select $select, Zend_Entity_Manager $entityManager)
    {
        $collection = $this->find($select, $entityManager);
        if(count($collection) == 1) {
            return $collection[0];
        } else {
            throw new Exception(count($collection)." elements found, but exactly one was asked for in entity '".$this->getEntityInterfaceTypeResponsibleFor()."'");
        }
    }

    /**
     * Find By Primary Key
     *
     * @throws Exception
     * @param  array|string
     * @return Zend_Entity_Interface
     */
    public function findByKey($keyValue, Zend_Entity_Manager $entityManager)
    {
        $keyIdent = Zend_Entity_Mapper_Definition_Utility::hashKeyIdentifier($keyValue);
        if($entityManager->getIdentityMap()->hasObject($this->getEntityInterfaceTypeResponsibleFor(), $keyIdent)) {
            return $entityManager->getIdentityMap()->getObject($this->getEntityInterfaceTypeResponsibleFor(), $keyIdent);
        } else {
            $key    = $this->getPrimaryKey()->getColumnName();
            $select = $this->select();
            $cond = $this->getAdapter()->quoteIdentifier($this->getMapperTable().".".$key);
            $select->where($cond." = ?", $keyValue);
            return $this->findOne($select, $entityManager);
        }
    }

    /**
     * Save a entity into persistence.
     *
     * @throws Exception
     * @param  array $entity
     * @return void
     */
    public function save(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager)
    {
        if($entity instanceof Zend_Entity_Mapper_LazyLoad_Entity &&
            $entity->entityWasLoaded() == false) {
            return;
        }

        $unitOfWork = $entityManager->getUnitOfWork();
        if($unitOfWork->getState($entity) == Zend_Entity_Mapper_UnitOfWork::STATE_CLEAN) {
            return;
        }

        $persister = $this->getPersister();
        $persister->save($entity, $entityManager);
    }

    /**
     * Delete a entity from persistence
     *
     * @throws Exception
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager )
    {
        if($entity instanceof Zend_Entity_Mapper_LazyLoad_Entity &&
            $entity->entityWasLoaded() == false) {
            return;
        }

        $unitOfWork = $entityManager->getUnitOfWork();
        if($unitOfWork->getState($entity) == Zend_Entity_Mapper_UnitOfWork::STATE_CLEAN) {
            return;
        } elseif($unitOfWork->getState($entity) == Zend_Entity_Mapper_UnitOfWork::STATE_NEW) {
            return;
        }

        $this->getPersister()->delete($entity, $entityManager);
        return;
    }
    
    /**
     * Return DB Adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getAdapter()
    {
        return $this->_db;
    }

    /**
     * Return responsible Persister class
     *
     * @return Zend_Entity_Mapper_Persister_Interface
     */
    protected function getPersister()
    {
        if($this->_persister === null) {
            $persisterClassName = $this->getDefinition()->getPersisterClass();
            $this->_persister = new $persisterClassName();
            $this->_persister->initialize($this->_entityDefinition, $this->_entityResourceMap);
        }

        return $this->_persister;
    }

    /**
     * Return select statement object
     *
     * @return Zend_Db_Select
     */
    public function select()
    {
        $select = new Zend_Entity_Mapper_Select( $this->getAdapter() );
        $this->getLoader()->initSelect($select);
        return $select;
    }

    /**
     * Return Resultprocessor and Object Loader
     * 
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    protected function getLoader()
    {
        if($this->_loader === null) {
            $this->_loader = new Zend_Entity_Mapper_Loader_Basic($this->getDefinition());
        }
        return $this->_loader;
    }

    /**
     * Query With Select objects.
     *
     * @throws Exception
     * @param  string
     * @return Zend_Db_Statement
     */
    protected function query(Zend_Db_Select $select, Zend_Entity_Manager $entityManager)
    {
        $identityMap = $entityManager->getIdentityMap();
        if($identityMap->hasCollection($select)) {
            return $identityMap->getCollection($select);
        } else {
            #$select->reset(Zend_Db_Select::COLUMNS);
            $loader = $this->getLoader();
            $loader->initColumns($select);

            try {
                $stmt = $this->getAdapter()->query($select);
            } catch(Exception $e) {
                throw new Exception("In '".$this->getEntityInterfaceTypeResponsibleFor()."' Mapper: ".$e->getMessage()." - ".$select." ".$e->getTraceAsString());
            }
            $collection = $loader->processResultset($stmt, $entityManager);
            $identityMap->addCollection($select, $collection);

            return $collection;
        }
    }

    /**
     *
     * @return Zend_Entity_Mapper_Definition_Entity
     */
    public function getDefinition()
    {
        if($this->_entityDefinition == null) {
            throw new Exception(
                sprintf("No table name or primary key given for metadata retrieval in mapper '%s'.",
                get_class($this)
            ));
        }
        return $this->_entityDefinition;
    }
    
    protected function getMapperTable()
    {
        return $this->getDefinition()->getTable();
    }

    /**
     * Return Primary Key Definition
     * 
     * @return Zend_Entity_Mapper_Definition_PrimaryKey
     */
    protected function getPrimaryKey()
    {
        return $this->getDefinition()->getPrimaryKey();
    }

    public function getEntityInterfaceTypeResponsibleFor()
    {
        return $this->getDefinition()->getClass();
    }
}