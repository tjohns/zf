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
     * @var Zend_Entity_MapperAbstract
     */
    protected $_mapper = null;

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
     * @var Zend_Entity_Event_EventAbstract
     */
    protected $_eventListener = null;

    /**
     * Construct new Entity Manager instance
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
     * @return Zend_Entity_Event_EventAbstract
     */
    public function getEventListener()
    {
        if($this->_eventListener == null) {
            $this->_eventListener = new Zend_Entity_Event_Listener();
        }
        return $this->_eventListener;
    }

    /**
     * @param Zend_Entity_Event_EventAbstract $eventListener
     */
    public function setEventListener(Zend_Entity_Event_EventAbstract $eventListener)
    {
        $this->_eventListener = $eventListener;
        return $this;
    }

    /**
     * @return Zend_Entity_Mapper_Mapper
     */
    protected function getMapper()
    {
        if($this->_mapper == null) {
            $this->_mapper = new Zend_Entity_Mapper_Mapper($this->_db, $this->_metadataFactory);
        }
        return $this->_mapper;
    }

    /**
     * @param string|object $input
     * @return Zend_Entity_Mapper_Select
     */
    public function createNativeQuery($input)
    {
        if(in_array($input, $this->getMetadataFactory()->getDefinitionEntityNames())) {
            $mapper = $this->getMapper();
            $loader = $mapper->getLoader($input);
            $select = $mapper->select();

            return new Zend_Entity_Mapper_NativeQuery($select, $loader, $this);
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
            $mapper = $this->getMapper();
            $object = $mapper->load($entityName, $this, $keyValue);
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
        $mapper = $this->getMapper();
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
        if($this->getIdentityMap()->contains($entity) == false) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException(
                "Cannot delete entity with unknown primary id from database."
            );
        }

        $mapper = $this->getMapper();
        $mapper->delete($entity, $this);
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
            $reference = $identityMap->getObject($class, $id);
        } else {
            $callback = array($this, "load");
            $callbackArguments = array($class, $id);
            $reference = new Zend_Entity_LazyLoad_Entity($callback, $callbackArguments, $class);
            $identityMap->addObject($class, $id, $reference);
        }
        return $reference;
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