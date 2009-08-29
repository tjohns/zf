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
     * Factory method to create the database mapper.
     * 
     * @param array $options
     * @return Zend_Db_Mapper_Mapper
     */
    static public function create(array $options)
    {
        if(!isset($options['db']) || (!($options['db'] instanceof Zend_Db_Adapter_Abstract))) {
            throw new Zend_Entity_Exception("Missing Database Adapter while creating Mapper.");
        }

        if(!isset($options['metadataFactory']) ||
            (!($options['metadataFactory'] instanceof Zend_Entity_MetadataFactory_Interface))) {
            throw new Zend_Entity_Exception("Missing Metadata Factory while creating Mapper.");
        }

        $db = $options['db'];
        $metadataFactory = $options['metadataFactory'];
        $mappings = $metadataFactory->transform('Zend_Db_Mapper_Mapping');

        return new self($db, $metadataFactory, $mappings);
    }

    /**
     * Construct DataMapper
     *
     * @param  Zend_Db_Adapter_Abstract  $db
     * @param  Zend_Entity_MetadataFactory_Interface $metadataFactory
     * @param  Zend_Db_Mapper_Mapping[] $mappingInstructions
     */
    public function __construct(Zend_Db_Adapter_Abstract $db, $metadataFactory=null, array $mappingInstructions=array())
    {
        $this->_db = $db;
        $this->_mappings = $mappingInstructions;
    }

    /**
     *
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @param  string $entityName
     * @param  mixed $keyValue
     * @return object
     */
    protected function _doLoad($entityManager, $entityName, $keyValue)
    {
        $mapping = $this->_mappings[$entityName];

        $tableName = $mapping->table;
        $key = $mapping->primaryKey->getColumnName();
        $query = new Zend_Db_Mapper_SqlQueryBuilder($entityManager, $this->createSqlQueryObject());
        $query->from($mapping->table);
        $query->with($entityName);

        $cond = $this->_db->quoteIdentifier($tableName.".".$key);
        return $query->where($cond." = ?", $keyValue);
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
     * @param  string $classOrNull
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return Zend_Db_Mapper_QueryObject
     */
    public function createNativeQueryBuilder($classOrNull, $entityManager)
    {
        $q = new Zend_Db_Mapper_SqlQueryBuilder($entityManager, $this->createSqlQueryObject());
        if($classOrNull !== null) {
            if(isset($this->_mappings[$classOrNull])) {
                $q->from($this->_mappings[$classOrNull]->table);
                $q->with($classOrNull);
            } else {
                throw new Zend_Entity_Exception(
                    "Cannot prepare the QueryBuilder with instructions to load unknown entity '".$classOrNull."'."
                );
            }
        }
        return $q;
    }

    /**
     * @param  string $sqlQuery
     * @param  Zend_Entity_Query_ResultSetMapping $resultSetMapping
     * @param  Zend_Entity_Manager_Interface $entityManager
     * @return Zend_Db_Mapper_SqlQuery
     */
    public function createNativeQuery($sqlQuery, $resultSetMapping, $entityManager)
    {
        return new Zend_Db_Mapper_SqlQuery($entityManager, $sqlQuery, $resultSetMapping);
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
            }

            $this->_loaders[$fetchMode] = $loader;
        }
        return $this->_loaders[$fetchMode];
    }
}