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
* Database Mapper
*
* @uses       Zend_Entity_MapperAbstract
* @category   Zend
* @package    Zend_Db
* @subpackage Mapper
* @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
* @license    http://framework.zend.com/license/new-bsd     New BSD License
*/
class Zend_Db_Mapper_Mapper extends Zend_Entity_MapperAbstract
{
    /**
     * Zend Database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Persister
     *
     * @var Zend_Db_Mapper_Persister_Interface[]
     */
    protected $_persister = array();

    /**
     * Loader
     *
     * @var array
     */
    protected $_loaders = null;

    /**
     * Loading a single instance of a specific entity happens in larger batches so that caching the required RSM helps alot.
     *
     * @var array
     */
    protected $_loadResultSetMappings = array();

    /**
     * Cache for Select queries loading a specific single instance.
     *
     * @var array
     */
    protected $_loadSqlQueries = array();

    /**
     * Construct DataMapper
     *
     * @param array $options
     */
    public function __construct($options, $metadataFactory=null, array $mappingInstructions=array())
    {
        foreach($options AS $option => $value) {
            $method = "set".ucfirst(strtolower($option));
            if(method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        if($this->_db === null) {
            throw new Zend_Entity_Exception("Required Zend_Db_Adapter_Abstract instance was not given to the database mapper.");
        }
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return Zend_Db_Mapper_Mapper
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function initializeMappings(Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {
        $options = array();

        $parts = explode("_", get_class($this->_db));
        $adapterName = array_pop($parts);
        $options['adapterName'] = $adapterName;

        if($metadataFactory->getDefaultIdGeneratorClass() == null) {
            $idGeneratorClass = $this->_guessIdGeneratorClass();
            $metadataFactory->setDefaultIdGeneratorClass($idGeneratorClass);
        }

        $this->_mappings = $metadataFactory->transform('Zend_Db_Mapper_Mapping', $options);
    }

    /**
     * Guess the correct Default Id Generator class from the configured Database Adapter.
     * 
     * @return string
     */
    protected function _guessIdGeneratorClass()
    {
        $adapters = array(
            'Zend_Db_Adapter_Mysqli' => 'Zend_Db_Mapper_Id_AutoIncrement',
            'Zend_Db_Adapter_Pdo_Mysql' => 'Zend_Db_Mapper_Id_AutoIncrement',
            'Zend_Db_Adapter_Pdo_Mssql' => 'Zend_Db_Mapper_Id_AutoIncrement',
            'Zend_Db_Adapter_Sqlsrv' => 'Zend_Db_Mapper_Id_AutoIncrement',
            'Zend_Db_Adapter_Pdo_Sqlite' => 'Zend_Db_Mapper_Id_AutoIncrement',
            'Zend_Db_Adapter_Pdo_Oci' => 'Zend_Db_Mapper_Id_Sequence',
            'Zend_Db_Adapter_Pdo_Pgsql' => 'Zend_Db_Mapper_Id_Sequence',
            'Zend_Db_Adapter_Oracle' => 'Zend_Db_Mapper_Id_Sequence',
            'Zend_Db_Adapter_Db2' => 'Zend_Db_Mapper_Id_Sequence',
            'Zend_Db_Adapter_Pdo_Ibm' => 'Zend_Db_Mapper_Id_Sequence',
        );

        foreach($adapters AS $adapterClassName => $idGeneratorClass) {
            if($this->_db instanceof $adapterClassName) {
                return $idGeneratorClass;
            }
        }
        return "Zend_Db_Mapper_Id_AutoIncrement";
    }

    /**
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @param  string $entityName
     * @param  mixed $keyValue
     * @return object
     */
    protected function _doLoad($entityManager, $entityName, $keyValue)
    {
        $sql = $this->_createLoadSqlQuery($entityName);
        $rsm = $this->_createLoadResultSetMapping($entityName);

        $sqlQuery = new Zend_Db_Mapper_SqlQuery($entityManager, $sql, $rsm);
        $sqlQuery->bindParam(1, $keyValue);
        return $sqlQuery;
    }

    /**
     * Create the SQL Query that is required to load a single entity.
     *
     * @param  string $entityName
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return string
     */
    protected function _createLoadSqlQuery($entityName)
    {
        if(!isset($this->_loadSqlQueries[$entityName])) {
            $this->_cacheLoadSqlQueryAndRsm($entityName);
        }
        return $this->_loadSqlQueries[$entityName];
    }

    /**
     * Create the ResultSetMapping that is required to load a single entity.
     *
     * @param  string $entityName
     * @return Zend_Entity_Query_ResultSetMapping
     */
    protected function _createLoadResultSetMapping($entityName)
    {
        if(!isset($this->_loadResultSetMappings[$entityName])) {
            $this->_cacheLoadSqlQueryAndRsm($entityName);
        }
        return $this->_loadResultSetMappings[$entityName];
    }

    /**
     * @schemaRequired
     * @param string $entityName
     * @return void
     */
    protected function _cacheLoadSqlQueryAndRsm($entityName)
    {
        $mapping = $this->_mappings[$entityName];

        $select = $this->createSqlQueryObject();
        $select->from($mapping->table);

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity($entityName);
        foreach($mapping->columnNameToProperty AS $columnName => $propertyName) {
            $rsm->addProperty($entityName, $columnName, $propertyName);
        }
        $select->columns($mapping->sqlColumnAliasMap);

        $tableName = $mapping->table;
        $key = $mapping->primaryKey->columnName;
        $cond = $this->_db->quoteIdentifier($tableName.".".$key);

        $select->where($cond." = ?");
        $this->_loadSqlQueries[$entityName] = $select->assemble();
        $this->_loadResultSetMappings[$entityName] = $rsm;
    }

    /**
     * Refresh the internal state of the given entity.
     *
     * @param object $entity
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    public function refresh($entity, $entityManager)
    {
        $entityName = $this->_getEntityName($entity);

        $id = $entityManager->getIdentityMap()->getPrimaryKey($entity);

        $sql = $this->_createLoadSqlQuery($entityName);
        $rsm = $this->_createLoadResultSetMapping($entityName);

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $resultSet = $stmt->fetchAll();
        
        $entityLoader = $this->getLoader(Zend_Entity_Manager::FETCH_REFRESH, $entityManager);
        $entityLoader->processResultset($resultSet, $rsm);
    }

    /**
     * @param string $entity
     * @param string $entityName
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    protected function _doSave($entity, $entityName, $entityManager)
    {
        $persister = $this->getPersister($entityName);
        $persister->save($entity, $entityManager);
    }

    /**
     * @param string $entity
     * @param string $entityName
     * @param Zend_Entity_Manager_Interface $entityManager
     * @return void
     */
    protected function _doDelete($entity, $entityName, $entityManager)
    {
        $this->getPersister($entityName)->delete($entity, $entityManager);
    }

    /**
     * @param  string $sqlQuery
     * @param  string|Zend_Entity_Query_ResultSetMapping $entityNameOrResultSetMapping
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return Zend_Db_Mapper_SqlQuery
     */
    public function createNativeQuery($sqlQuery, $entityNameOrResultSetMapping, $entityManager)
    {
        if(is_string($entityNameOrResultSetMapping)) {
            $entityName = $entityNameOrResultSetMapping;
            if(!isset($this->_mappings[$entityName])) {
                throw new Zend_Entity_InvalidEntityException($entityName);
            }

            $rsm = new Zend_Entity_Query_ResultSetMapping();
            $rsm->addEntity($entityName);
            foreach($this->_mappings[$entityName]->columnNameToProperty AS $columnName => $propertyName) {
                $rsm->addProperty($entityName, $columnName, $propertyName);
            }
        } else if($entityNameOrResultSetMapping instanceof Zend_Entity_Query_ResultSetMapping) {
            $rsm = $entityNameOrResultSetMapping;
        } else {
            throw new Zend_Entity_Exception();
        }

        return new Zend_Db_Mapper_SqlQuery($entityManager, $sqlQuery, $rsm);
    }

    /**
     * @return Zend_Entity_Transaction
     */
    public function getTransaction()
    {
        return new Zend_Db_Mapper_Transaction($this->_db);
    }

    /**
     * Close the current connection context.
     *
     * @return void
     */
    public function closeConnection()
    {
        $this->_db->closeConnection();
    }

    /**
     * Return DB Adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_db;
    }

    /**
     * Return responsible Persister class
     *
     * @param string $className
     * @return Zend_Db_Mapper_Persister_Interface
     */
    protected function getPersister($className)
    {
        if(!isset($this->_persister[$className])) {
            $this->_persister[$className] = new Zend_Db_Mapper_Persister_Simple();
            $this->_persister[$className]->initialize($this->_mappings[$className]);
            $this->_persister[$className]->setTypeConverter($this->getTypeConverter());
        }

        return $this->_persister[$className];
    }

    public function createSqlQueryObject()
    {
        return new Zend_Db_Mapper_QueryObject($this->_db);
    }

    /**
     * Return Resultprocessor and Object Loader
     *
     * @return Zend_Db_Mapper_Loader_LoaderAbstract
     */
    public function getLoader($fetchMode, $em)
    {
        if(!isset($this->_loaders[$fetchMode])) {
            switch($fetchMode) {
                case Zend_Entity_Manager::FETCH_ENTITIES:
                    $loader = new Zend_Db_Mapper_Loader_Entity($em, $this->_mappings);
                    break;
                case Zend_Entity_Manager::FETCH_ARRAY:
                    $loader = new Zend_Db_Mapper_Loader_Array($em, $this->_mappings);
                    break;
                case Zend_Entity_Manager::FETCH_SCALAR:
                    $loader = new Zend_Db_Mapper_Loader_Scalar($em, $this->_mappings);
                    break;
                case Zend_Entity_Manager::FETCH_SINGLESCALAR:
                    $loader = new Zend_Db_Mapper_Loader_SingleScalar($em, $this->_mappings);
                    break;
                case Zend_Entity_Manager::FETCH_REFRESH:
                    $loader = new Zend_Db_Mapper_Loader_Refresh($em, $this->_mappings);
                    break;
            }
            $loader->setTypeConverter($this->getTypeConverter());
            $this->_loaders[$fetchMode] = $loader;
        }
        return $this->_loaders[$fetchMode];
    }
}