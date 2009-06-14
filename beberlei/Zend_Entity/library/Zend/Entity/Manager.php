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

require_once "Manager/Interface.php";

class Zend_Entity_Manager implements Zend_Entity_Manager_Interface
{
    const FETCH_ENTITIES        = 1;
    const FETCH_ARRAY           = 2;
    const FETCH_ENTITY_ITERATOR = 3;
    const FETCH_ARRAY_ITERATOR  = 4;

    /**
     * Db Handler
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Unit of Work
     *
     * @var Zend_Entity_Mapper_UnitOfWork_Interface
     */
    protected $_unitOfWork = null;

    /**
     * Interface to Mapper hashmap
     * 
     * @var array
     */
    protected $_entityToMapper = array();

    /**
     * Object identity map
     * 
     * @var Zend_Entity_Mapper_IdentityMap
     */
    protected $_identityMap = null;

    /**
     * Definition Map
     *
     * @var Zend_Entity_Mapper_DefinitionMap
     */
    protected $_resource = null;

    /**
     * Construct new model factory.
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @param Zend_Entity_EventDispatcher $dispatcher
     */
    public function __construct(Zend_Db_Adapter_Abstract $db, $options = array())
    {
        $this->setAdapter($db);
        
        if(!isset($options['identityMap'])) {
            $options['identityMap'] = new Zend_Entity_Mapper_IdentityMap();
        }

        if(!isset($options['unitOfWork'])) {
            $options['unitOfWork'] = new Zend_Entity_Mapper_UnitOfWork();
        }

        foreach($options AS $k => $v) {
            $method = 'set'.ucfirst($k);
            if(method_exists($this, $method)) {
                $this->$method($v);
            }
        }
    }

    /**
     * Set UnitOfWork
     * 
     * @param  Zend_Entity_Mapper_UnitOfWork $uow
     * @return Zend_Entity_Manager
     */
    public function setUnitOfWork(Zend_Entity_Mapper_UnitOfWork $uow)
    {
        $uow->setAdapter($this->getAdapter());
        $uow->setManager($this);
        
        $this->_unitOfWork = $uow;
        return $this;
    }

    /**
     * Return UnitOfWork
     * 
     * @return Zend_Entity_Mapper_UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->_unitOfWork;
    }

    /**
     * Set the Identity Map
     * 
     * @param Zend_Entity_Mapper_IdentityMap $map
     * @return Zend_Entity_Manager
     */
    public function setIdentityMap(Zend_Entity_Mapper_IdentityMap $map)
    {
        $this->_identityMap = $map;
        return $this;
    }

    /**
     * Return current identity map.
     * 
     * @return Zend_Entity_Mapper_IdentityMap
     */
    public function getIdentityMap()
    {
        return $this->_identityMap;
    }

    /**
     * Set the Entity Resource Map
     *
     * @param  Zend_Entity_Resource_Interface
     * @return Zend_Entity_Manager
     */
    public function setResource(Zend_Entity_Resource_Interface $map)
    {
        $this->_resource = $map;
        return $this;
    }

    /**
     * Get Resource Map
     *
     * @return Zend_Entity_Resource_Interface
     */
    public function getResource()
    {
        if($this->_resource === null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("No resource definition map was given to Entity Manager.");
        }
        return $this->_resource;
    }

    /**
     * Set Database Adapter
     *
     * @param  Zend_Db_Adapter_Abstract $db
     * @return Zend_Entity_Manager
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Return Adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_db;
    }

    /**
     * Return concrete mapper implementation of the given Entity Type
     * 
     * @param  string|Zend_Entity_Interface $entity
     * @return Zend_Entity_Mapper_Interface
     */
    public function getMapperByEntity($entity)
    {
        if($entity instanceof Zend_Entity_Interface) {
            $className = get_class($entity);
        } else {
            $className = $entity;
        }

        if(!isset($this->_entityToMapper[$className])) {
            $this->loadEntityMapper($className);
        }
        return $this->_entityToMapper[$className];
    }

    /**
     * Load Entity Mapper
     * 
     * @param string $entityClassName
     * @return void
     */
    protected function loadEntityMapper($entityClassName)
    {
        $resourceMap = $this->getResource();
        $entityDefinition = $resourceMap->getDefinitionByEntityName($entityClassName);
        $mapper = new Zend_Entity_Mapper($this->getAdapter(), $entityDefinition, $resourceMap);
        $this->_entityToMapper[$entityClassName] = $mapper;
    }

    /**
     * Return Select statement
     * 
     * @param  string $entityName
     * @return Zend_Db_Select
     */
    public function select($entityName)
    {
        $mapper = $this->getMapperByEntity($entityName);
        return $mapper->select();
    }

    /**
     * Find all entitys matching select statement
     *
     * @param  string $entityName
     * @param  Zend_Db_Select|string $select
     * @return Zend_Entity_Collection
     */
    public function performFindQuery($entityName, $select)
    {
        $mapper = $this->getMapperByEntity($entityName);
        return $mapper->performFindQuery($select, $this);
    }

    /**
     * Find one entity matching select statement
     *
     * @param string $entityName
     * @param Zend_Db_Select $select
     * @return Zend_Entity_Interface
     */
    public function findOne($entityName, $select)
    {
        $mapper = $this->getMapperByEntity($entityName);
        return $mapper->findOne($select, $this);
    }

    /**
     * Find all entities of a type
     *
     * @param string $entityName
     * @param string $subcondition
     * @param int    $limit
     * @param string $order
     * @return Zend_Entity_Collection
     */
    public function findAll($entityName, $subcondition=null, $limit=null, $order=null)
    {
        $mapper = $this->getMapperByEntity($entityName);
        $select = $mapper->select();
        if($subcondition !== null) {
            $select->where($subcondition);
        }
        if($limit !== null) {
            $select->limit($limit);
        }
        if($order !== null) {
            $select->order($order);
        }
        return $mapper->performFindQuery($select, $this);
    }

    /**
     * Find by primary key
     *
     * @param string $entityName
     * @param string $key
     * @return Zend_Entity_Interface
     */
    public function load($entityName, $key)
    {
        $mapper = $this->getMapperByEntity($entityName);
        return $mapper->load($key, $this);
    }

    /**
     * Save entity by registering it with UnitOfWork or hitting the database mapper.
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function save(Zend_Entity_Interface $entity)
    {
        $uow = $this->getUnitOfWork();
        if($uow->isManagingCurrentTransaction() == true) {
            $uow->registerDirty($entity);
        } else {
            $mapper = $this->getMapperByEntity($entity);
            $mapper->save($entity, $this);
        }
    }

    /**
     * Try to delete entity by checking with UnitOfWork or directly going to mapper.
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity)
    {
        $uow = $this->getUnitOfWork();
        if($uow->isManagingCurrentTransaction() == true) {
            $uow->registerDeleted($entity);
        } else {
            $mapper = $this->getMapperByEntity($entity);
            $mapper->delete($entity, $this);
        }
    }

    /**
     * Referesh the state of the given entity from the database.
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function refresh(Zend_Entity_Interface $entity)
    {
        
    }

    /**
     * Tell Unit Of work to begin transaction
     *
     * @retun void
     */
    public function beginTransaction()
    {
        $this->getUnitOfWork()->beginTransaction();
    }

    /**
     * Tell Unit of Work to commit transaction.
     *
     * @return void
     */
    public function commit()
    {
        $this->getUnitOfWork()->commit();
    }

    /**
     * Tell Unit of Work to roll back current transaction.
     *
     * @return void
     */
    public function rollBack()
    {
        $this->getUnitOfWork()->rollBack();
    }

    /**
     * If Unit of Work is managing the current transaction commit it and begin a new one.
     *
     * @throws Zend_Entity_Exception If UnitOfWork currently manages no transaction.
     * @return void
     */
    public function flush()
    {
        if($this->getUnitOfWork()->isManagingCurrentTransaction() == true) {
            $this->getUnitOfWork()->commit();
            $this->getUnitOfWork()->beginTransaction();
        } else {
            throw new Zend_Entity_Exception("UnitOfWork is not managing transaction and entity manager cannot be flushed.");
        }
    }

    /**
     * Clear identity map and Unit Of Work and Identity Map of entity references.
     *
     * @return void
     */
    public function clear()
    {
        $this->getUnitOfWork()->clear();
        $this->getIdentityMap()->clear();
    }

    /**
     * Close connection and beforehand commit any open transactions managed by UnitOfWork
     *
     * @return void
     */
    public function closeConnection()
    {
        if($this->getUnitOfWork()->isManagingCurrentTransaction() == true) {
            $this->getUnitOfWork()->commit();
        }
        $this->clear();
        $this->getAdapter()->closeConnection();
    }

    /**
     * Set this entity manager to read only, which might lead to faster object destruction and memory management.
     *
     * @return void
     */
    public function setReadOnly()
    {
        $this->getUnitOfWork()->setReadOnly();
    }
}