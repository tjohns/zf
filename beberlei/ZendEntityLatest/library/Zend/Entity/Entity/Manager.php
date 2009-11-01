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
    /**
     * @var int
     */
    const FETCH_ENTITIES        = 1;

    /**
     * @var int
     */
    const FETCH_ARRAY           = 2;

    /**
     * @var int
     */
    const FETCH_SINGLESCALAR    = 3;

    /**
     * @var int
     */
    const FETCH_SCALAR          = 4;

    /**
     * @var int
     */
    const FETCH_REFRESH         = 5;

    /**
     * Immediately flush persist, save and delete operations.
     *
     * @var int
     */
    const FLUSHMODE_IMMEDIATE   = 1;

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
     * MetadataFactory
     *
     * @var Zend_Entity_MetadataFactory_FactoryAbstract
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
     * @var Zend_Entity_LazyLoad_GeneratorAbstract
     */
    protected $_proxyGenerator = null;

    /**
     * @var array
     */
    protected $_namedQueries = array();

    /**
     * @var Zend_Entity_Transaction
     */
    protected $_transaction = null;

    /**
     * @var int
     */
    protected $_flushMode = self::FLUSHMODE_IMMEDIATE;

    /**
     * @var bool
     */
    protected $_initializedProxies = false;

    /**
     * @var array
     */
    protected $_storageOptions = array(
        'backend' => 'Db',
        'adapter' => null,
    );

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

        if(isset($options['metadataFactory'])) {
            $this->setMetadataFactory($options['metadataFactory']);
            unset($options['metadataFactory']);
        }

        if(isset($options['mapper'])) {
            $this->setMapper($options['mapper']);
            unset($options['mapper']);
        }

        if(isset($options['adapter'])) {
            $this->_storageOptions['adapter'] = $options['adapter'];
            unset($options['adapter']);
        }

        if(isset($options['storage'])) {
            $this->_storageOptions = $options['storage'];
            unset($options['storage']);
        }

        foreach($options AS $k => $v) {
            $method = 'set'.ucfirst($k);
            if(method_exists($this, $method)) {
                $this->$method($v);
            }
        }
    }

    /**
     *
     * @param int $mode
     * @throws Zend_Entity_Exception
     * @return Zend_Entity_Manager
     */
    public function setFlushMode($mode)
    {
        if(in_array($mode, array(self::FLUSHMODE_IMMEDIATE))) {
            $this->_flushMode = $mode;
        } else {
            throw new Zend_Entity_Exception("Invalid flush-mode specified.");
        }
    }

    /**
     * @return int
     */
    public function getFlushMode()
    {
        return $this->_flushMode;
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
     * @param  Zend_Entity_MetadataFactory_FactoryAbstract
     * @return Zend_Entity_Manager
     */
    public function setMetadataFactory(Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {
        $this->_metadataFactory = $metadataFactory;
        return $this;
    }

    /**
     * Get Entity Metadata Factory
     *
     * @return Zend_Entity_MetadataFactory_FactoryAbstract
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
            $this->setMapper(Zend_Entity_ManagerFactory::createMapper($this->_storageOptions));
        }
        return $this->_mapper;
    }

    /**
     * @param  Zend_Db_Mapper_Abstract $mapper
     * @return Zend_Entity_Manager
     */
    public function setMapper(Zend_Entity_MapperAbstract $mapper)
    {
        $mapper->initializeMappings($this->getMetadataFactory());
        $this->_mapper = $mapper;
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
     * @return object
     */
    public function load($entityName, $key, $notFound="exception")
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
     * @param  object $entity
     * @return void
     */
    public function persist($entity)
    {
        if(!is_object($entity)) {
            throw new Zend_Entity_InvalidEntityException();
        }

        $mapper = $this->getMapper();
        $mapper->save($entity, $this);
    }

    /**
     * @param object $entity
     * @deprecated
     */
    public function save($entity)
    {
        trigger_error("Zend_Entity_Manager::save() is deprecated, use persist().", E_USER_NOTICE);
        $this->persist($entity);
    }

    /**
     * Try to delete entity by checking with UnitOfWork or directly going to mapper.
     *
     * @param  object $entity
     * @return void
     */
    public function remove($entity)
    {
        if(!is_object($entity)) {
            throw new Zend_Entity_InvalidEntityException();
        } else if($this->_identityMap->contains($entity) == false) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException(
                "Cannot delete entity with unknown primary id from database."
            );
        }

        $mapper = $this->getMapper();
        $mapper->delete($entity, $this);
    }

    /**
     * @param object $entity
     * @deprecated
     */
    public function delete($entity)
    {
        trigger_error("Zend_Entity_Manager::delete() is deprecated, use remove().", E_USER_NOTICE);
        $this->remove($entity);
    }

    /**
     * Merge the state of a detached entity which has an identity back into the persistence context.
     *
     * @param object $entity
     * @return void
     */
    public function merge($entity)
    {
        throw new Zend_Entity_Exception("not implemented yet");
    }

    /**
     * Refresh the state of an entity.
     *
     * @uow
     * @param object $entity
     * @return void
     */
    public function refresh($entity)
    {
        $mapper = $this->getMapper();
        $mapper->refresh($entity, $this);
    }

    /**
     * Detach entity from persistence context, so that it will become unmanaged. Any unflushed changes will be lost.
     * 
     * @param object $entity
     */
    public function detach($entity)
    {
        if(!is_object($entity)) {
            throw new Zend_Entity_InvalidEntityException();
        }

        $this->_identityMap->remove($entity);
    }

    /**
     * Check if entity instance belongs to the persistence context.
     *
     * @param  object $entity
     * @return boolean
     */
    public function contains($entity)
    {
        return $this->_identityMap->contains($entity);
    }

    /**
     * Get a reference of an object.
     *
     * A reference is either a LazyLoad entity of the type {@see Zend_Entity_LazyLoad_Entity}
     * or if the entity was loaded before and is found in the identity map the original is used.
     *
     * @param string $entityName
     * @param int|string $id
     */
    public function getReference($entityName, $id)
    {
        if($this->_initializedProxies === false) {
            $this->_initializeProxies();
        }

        $identityMap = $this->getIdentityMap();
        if($identityMap->hasObject($entityName, $id) || $identityMap->hasLazyObject($entityName, $id)) {
            $reference = $identityMap->getObject($entityName, $id);
        } else {
            $reference = $this->_proxyGenerator->instantiate($entityName, $this, $id);
        }
        return $reference;
    }

    protected function _initializeProxies()
    {
        $metadata = $this->getMetadataFactory();
        $proxyGenerator = $this->getProxyGenerator();
        $metadata->visit($proxyGenerator);

        $proxyGenerator->generate();
        $this->_initializedProxies = true;
    }

    /**
     * Begin a transaction
     *
     * @retun Zend_Entity_Transaction
     */
    public function beginTransaction()
    {
        $tx = $this->getTransaction();
        $tx->begin();
        return $tx;
    }

    /**
     * @return Zend_Entity_Transaction
     */
    public function getTransaction()
    {
        if($this->_transaction == null) {
            $this->_transaction = $this->getMapper()->getTransaction();
        }

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
        $tx = $this->getTransaction();
        if($tx->isActive()) {
            $tx->commit();
        }
        $this->getMapper()->closeConnection();
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

    /**
     * @param Zend_Entity_LazyLoad_GeneratorAbstract $proxyGenerator
     * @return Zend_Entity_Manager
     */
    public function setProxyGenerator(Zend_Entity_LazyLoad_GeneratorAbstract $proxyGenerator)
    {
        $this->_proxyGenerator = $proxyGenerator;
        return $this;
    }

    /**
     * @return Zend_Entity_LazyLoad_GeneratorAbstract
     */
    public function getProxyGenerator()
    {
        if($this->_proxyGenerator == null) {
            $this->_proxyGenerator = new Zend_Entity_LazyLoad_DynamicGenerator();
        }
        return $this->_proxyGenerator;
    }
}