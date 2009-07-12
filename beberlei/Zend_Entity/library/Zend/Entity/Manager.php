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
    protected $_entityMappers = array();

    /**
     * Object identity map
     * 
     * @var Zend_Entity_IdentityMap
     */
    protected $_identityMap = null;

    /**
     * Definition Map
     *
     * @var Zend_Entity_Mapper_DefinitionMap
     */
    protected $_metadataFactory = null;

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
            $options['identityMap'] = new Zend_Entity_IdentityMap();
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
     * @param Zend_Entity_IdentityMap $map
     * @return Zend_Entity_Manager
     */
    public function setIdentityMap(Zend_Entity_IdentityMap $map)
    {
        $this->_identityMap = $map;
        return $this;
    }

    /**
     * Return current identity map.
     * 
     * @return Zend_Entity_IdentityMap
     */
    public function getIdentityMap()
    {
        return $this->_identityMap;
    }

    /**
     * Set the Entity Metadata Factory
     *
     * @param  Zend_Entity_MetadataFactory_Interface
     * @return Zend_Entity_Manager
     */
    public function setMetadataFactory(Zend_Entity_MetadataFactory_Interface $metadataFactory)
    {
        $this->_metadataFactory = $metadataFactory;
        return $this;
    }

    /**
     * Get Entity Metadata Factory
     *
     * @return Zend_Entity_MetadataFactory_Interface
     */
    public function getMetadataFactory()
    {
        if($this->_metadataFactory === null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("No metadata factory was given to Entity Manager.");
        }
        return $this->_metadataFactory;
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
     * @return Zend_Entity_Mapper_Abstract
     */
    public function getMapperByEntity($entity)
    {
        if($entity instanceof Zend_Entity_Interface) {
            $className = get_class($entity);
        } else {
            $className = $entity;
        }

        if(!isset($this->_entityMappers[$className])) {
            $this->loadEntityMapper($className);
        }
        return $this->_entityMappers[$className];
    }

    /**
     * Load Entity Mapper
     * 
     * @param string $entityClassName
     * @return void
     */
    protected function loadEntityMapper($entityClassName)
    {
        $resourceMap = $this->getMetadataFactory();
        $entityDefinition = $resourceMap->getDefinitionByEntityName($entityClassName);
        $mapper = new Zend_Entity_Mapper($this->getAdapter(), $entityDefinition, $resourceMap);
        $this->_entityMappers[$entityClassName] = $mapper;
    }

    /**
     * @param string|object $input
     * @return Zend_Entity_Mapper_Select
     */
    public function createNativeQuery($input)
    {
        if(in_array($input, $this->getMetadataFactory()->getDefinitionEntityNames())) {
            $mapper = $this->getMapperByEntity($input);
            $loader = $mapper->getLoader();
            $select = $mapper->select();

            return new Zend_Entity_Mapper_DbSelectQuery($select, $loader, $this);
        } else {
            throw new Exception("Missing Native Query Parser/Builder/Whatever!");
        }
    }

    /**
     * @param string $entityName
     * @return Zend_Entity_Mapper_Query
     */
    public function createQuery($entityName)
    {
        throw new Exception("not implemented yet");
    }

    /**
     * Find by primary key
     *
     * @param string $entityName
     * @param string $key
     * @return Zend_Entity_Interface
     */
    public function load($entityName, $keyValue)
    {
        if($this->getIdentityMap()->hasObject($entityName, $keyValue)) {
            $object = $this->getIdentityMap()->getObject($entityName, $keyValue);
        } else {
            $mapper = $this->getMapperByEntity($entityName);
            $def = $mapper->getDefinition();

            $tableName = $def->getTable();
            $key = $def->getPrimaryKey()->getColumnName();
            $select =  $this->createNativeQuery($def->getClass());
            $cond = $this->getAdapter()->quoteIdentifier($tableName.".".$key);
            $select->where($cond." = ?", $keyValue);

            $object = $select->getSingleResult();
        }
        return $object;
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
        return $this->_identityMap->contains($entity);
    }

    /**
     * Get a reference of an object.
     *
     * A reference is either a LazyLoad entity of the type {@see Zend_Entity_LazyLoad_Entity}
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
            $lazyEntity = new Zend_Entity_LazyLoad_Entity($callback, $callbackArguments);
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
        $this->_identityMap->clear();
    }

    /**
     * Close connection and beforehand commit any open transactions managed by UnitOfWork
     *
     * @return void
     */
    public function close()
    {
        $this->clear();
        $this->_db->closeConnection();
    }
}