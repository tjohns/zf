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
 * @package    Zend_Entity
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Entity Manager
 *
 * @uses       Zend_Entity_Manager_Interface
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Manager implements Zend_Entity_Manager_Interface
{
    const FETCH_ENTITIES        = 1;
    const FETCH_ARRAY           = 2;


    const NOTFOUND_EXCEPTION = "exception";
    const NOTFOUND_NULL = "null";

    /**
     * Db Handler
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
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
     * @var Zend_Entity_MetadataFactory_Interface
     */
    protected $_metadataFactory = null;

    /**
     * @var Zend_Entity_Event_EventAbstract
     */
    protected $_eventListener = null;

    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_namedQueryLoader = null;

    /**
     * @var array
     */
    protected $_namedQueries = array();

    /**
     * @var Zend_Entity_Transaction
     */
    protected $_transaction = null;

    /**
     * Construct new Entity Manager instance
     *
     * @param array $options
     */
    public function __construct($options = array())
    {        
        if(!isset($options['identityMap'])) {
            $options['identityMap'] = new Zend_Entity_IdentityMap();
        }

        if(isset($options['db'])) {
            $this->setAdapter($options['db']);
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
     * @return Zend_Entity_MapperAbstract
     */
    public function getMapper()
    {
        if($this->_mapper == null) {
            $options = array(
                'db' => $this->_db,
                'metadataFactory' => $this->_metadataFactory,
            );
            $this->_mapper = Zend_Db_Mapper_Mapper::create($options);
            $this->_transaction = $this->_mapper->getTransaction();
        }
        return $this->_mapper;
    }

    /**
     * @param  Zend_Db_Mapper_Abstract $mapper
     * @return Zend_Entity_Manager
     */
    public function setMapper(Zend_Entity_MapperAbstract $mapper)
    {
        $this->_mapper = $mapper;
        $this->_transaction = $this->_mapper->getTransaction();
        return $this;
    }

    /**
     * @param string|object $sqlQuery
     * @param string|Zend_Entity_Query_ResultSetMapping $classOrResultSetMapping
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function createNativeQuery($sqlQuery, $classOrResultSetMapping=null)
    {
        return $this->getMapper()->createNativeQuery($sqlQuery, $classOrResultSetMapping, $this);
    }

    /**
     * @param string $queryName
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function createNamedQuery($queryName)
    {
        if(preg_match('/([^A-Za-z0-9]+)/', $queryName)) {
            throw new Zend_Entity_Exception("Invalid named query identifier. Only chars and numbers are allowed.");
        }

        if($this->_namedQueryLoader == null) {
            throw new Zend_Entity_Exception("No named query loader was initialized with this entity manager.");
        }
        
        if(!isset($this->_namedQueries[$queryName])) {
            $queryClassName = $this->_namedQueryLoader->getClassName($queryName);
            if(!class_exists($queryClassName)) {
                throw new Zend_Entity_Exception(
                    "A named query class with name '".$queryClassName."' does not exist!"
                );
            }

            $this->_namedQueries[$queryName] = new $queryClassName;

            if(!($this->_namedQueries[$queryName] instanceof Zend_Entity_Query_NamedQueryAbstract)) {
                throw new Zend_Entity_Exception(
                    "Named query plugin has to be of type 'Zend_Entity_Query_NamedQueryAbstract', but ".
                    "'".get_class($this->_namedQueries[$queryName])."' was given."
                );
            }
            $this->_namedQueries[$queryName]->setEntityManager($this);
        }
        return $this->_namedQueries[$queryName]->create();
    }

    /**
     * Find by primary key
     *
     * @param string $entityName
     * @param string $key
     * @return Zend_Entity_Interface
     */
    public function load($entityName, $key, $notFound="null")
    {
        if($this->_identityMap->hasObject($entityName, $key)) {
            $object = $this->_identityMap->getObject($entityName, $key);
        } else {
            $mapper = $this->getMapper();
            $object = $mapper->load($this, $entityName, $key, $notFound);
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
            $callbackArguments = array($class, $id, self::NOTFOUND_EXCEPTION);
            $reference = new Zend_Entity_LazyLoad_Entity($callback, $callbackArguments, $class);
            $identityMap->addObject($class, $id, $reference);
        }
        return $reference;
    }

    /**
     * Begin a transaction
     *
     * @retun Zend_Entity_Transaction
     */
    public function beginTransaction()
    {
        $this->_transaction->begin();
        return $this->_transaction;
    }

    /**
     * @return Zend_Entity_Transaction
     */
    public function getTransaction()
    {
        return $this->_transaction;
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
        if($this->_transaction->isActive()) {
            $this->_transaction->commit();
        }
        $this->_mapper->closeConnection();
    }

    /**
     * @param Zend_Loader_PluginLoader $loader
     * @return Zend_Entity_Manager
     */
    public function setNamedQueryLoader(Zend_Loader_PluginLoader $loader)
    {
        $this->_namedQueryLoader = $loader;
        return $this;
    }

    /**
     * @return Zend_Loader_PluginLoader
     */
    public function getNamedQueryLoader()
    {
        return $this->_namedQueryLoader;
    }
}