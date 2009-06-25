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

    /**
     * Db Handler
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

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

        foreach($options AS $k => $v) {
            $method = 'set'.ucfirst($k);
            if(method_exists($this, $method)) {
                $this->$method($v);
            }
        }
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
    public function find($entityName, $select)
    {
        $mapper = $this->getMapperByEntity($entityName);
        return $mapper->find($select, $this);
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
        return $mapper->find($select, $this);
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
        $mapper = $this->getMapperByEntity($entity);
        $mapper->save($entity, $this);
    }

    /**
     * Try to delete entity by checking with UnitOfWork or directly going to mapper.
     *
     * @param  Zend_Entity_Interface $entity
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity)
    {
        $mapper = $this->getMapperByEntity($entity);
        $mapper->delete($entity, $this);
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
     * Check if entity instance belongs to the persistence context.
     *
     * @param  Zend_Entity_Interface $entity
     * @return boolean
     */
    public function contains(Zend_Entity_Interface $entity)
    {
        return $this->getIdentityMap()->contains($entity);
    }

    /**
     * Get a reference of an object.
     *
     * A reference is either a LazyLoad entity of the type {@see Zend_Entity_Mapper_LazyLoad_Entity}
     * or if the entity was loaded before and is found in the identity map the original is used.
     *
     * @param string $class
     * @param int|string $id
     */
    public function getReference($class, $id)
    {
        $identityMap = $this->getIdentityMap();
        if($identityMap->hasObject($class, $id) || $identityMap->hasLazyObject($class, $id)) {
            $lazyEntity = $identityMap->getObject($class, $id);
        } else {
            $callback          = array($this, "load");
            $callbackArguments = array($class, $id);
            $lazyEntity = new Zend_Entity_Mapper_LazyLoad_Entity($callback, $callbackArguments);
            $identityMap->addObject($class, $id, $lazyEntity);
        }
        return $lazyEntity;
    }

    /**
     * Tell Unit Of work to begin transaction
     *
     * @retun void
     */
    public function beginTransaction()
    {
        $this->_db->beginTransaction();
    }

    /**
     * Tell Unit of Work to commit transaction.
     *
     * @return void
     */
    public function commit()
    {
        $this->_db->commit();
    }

    /**
     * Tell Unit of Work to roll back current transaction.
     *
     * @return void
     */
    public function rollBack()
    {
        $this->_db->rollBack();
    }

    /**
     * Clear identity map and Unit Of Work and Identity Map of entity references.
     *
     * @return void
     */
    public function clear()
    {
        $this->getIdentityMap()->clear();
    }

    /**
     * Close connection and beforehand commit any open transactions managed by UnitOfWork
     *
     * @return void
     */
    public function closeConnection()
    {
        $this->clear();
        $this->getAdapter()->closeConnection();
    }
}