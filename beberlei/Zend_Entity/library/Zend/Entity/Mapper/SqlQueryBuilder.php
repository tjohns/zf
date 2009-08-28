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
 * Native Query implementation for the Database Mapper
 *
 * @uses       Zend_Entity_Query_QueryAbstract
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Mapper_SqlQueryBuilder extends Zend_Entity_Mapper_SqlQueryAbstract
{
    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * @var Zend_Entity_Query_ResultSetMapping
     */
    protected $_rsm = null;

    /**
     * @var int
     */
    protected $_offset = null;

    /**
     * @var int
     */
    protected $_itemCountPerPage = null;

    /**
     * @var Zend_Entity_Mapper_QueryObject
     */
    protected $_queryObject = null;

    /**
     * @var array
     */
    protected $_mappings = array();

    /**
     *
     * @param Zend_Entity_Manager_Interface $manager
     * @param Zend_Entity_Mapper_QueryObject $queryObject
     */
    public function __construct(Zend_Entity_Manager_Interface $manager, Zend_Entity_Mapper_QueryObject $queryObject=null)
    {
        $mapper = $manager->getMapper();
        if(!($mapper instanceof Zend_Entity_Mapper_Mapper)) {
            throw new Zend_Entity_StorageMissmatchException("SqlQueryBuilder only works with Zend_Db_Mapper storage engine");
        }

        if($queryObject == null) {
            $queryObject = $mapper->createSqlQueryObject();
        }

        $this->_mappings = $mapper->getStorageMappings();
        $this->_entityManager = $manager;
        $this->_queryObject = $queryObject;
        $this->_rsm = new Zend_Entity_Query_ResultSetMapping();
    }

    /**
     *
     * @param  string $entityName
     * @param  string $correlationName
     * @return Zend_Entity_Mapper_SqlQueryBuilder
     */
    public function with($entityName, $correlationName = null, $joined = false)
    {
        if(!isset($this->_mappings[$entityName])) {
            throw new Zend_Entity_InvalidEntityException("Invalid entity name '".$entityName."' given to ->with().");
        }

        if($joined) {
            $this->_rsm->addJoinedEntity($entityName);
        } else {
            $this->_rsm->addEntity($entityName);
        }
        foreach($this->_mappings[$entityName]->columnNameToProperty AS $columnName => $property) {
            $this->_rsm->addProperty($entityName, $columnName, $property->getPropertyName());
        }

        $this->_queryObject->columns($this->_mappings[$entityName]->sqlColumnAliasMap, $correlationName);
        return $this;
    }

    /**
     * @return Zend_Entity_Mapper_Select
     */
    protected function getQueryObject()
    {
        return $this->_queryObject;
    }

    protected function _doExecute()
    {
        return $this->getQueryObject()->query(null, $this->getParams()); // null = $fetchMode
    }

    public function setFirstResult($offset)
    {
        $this->_offset = $offset;
        $this->getQueryObject()->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    public function setMaxResults($itemCountPerPage)
    {
        $this->_itemCountPerPage = $itemCountPerPage;
        $this->getQueryObject()->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    /**
     *
     * @param  string $table
     * @param  string $correlationName
     * @return Zend_Entity_Mapper_SqlQueryBuilder
     */
    public function from($table, $correlationName=null)
    {
        $this->_queryObject->from($table, $correlationName);
        return $this;
    }

    /**
     *
     * @param string|array $table
     * @param string $onCondition
     * @param string $joinedEntityName
     * @return Zend_Entity_Mapper_SqlQueryBuilder
     */
    public function joinWith($table, $onCondition, $joinedEntityName)
    {
        if(is_string($table)) {
            $correlationName = $table;
            $tableName = $table;
        } else {
            $correlationName = key($table);
            if(is_numeric($correlationName)) {
                $correlationName = current($table);
            }
            $tableName = current($table);
        }
        $this->_queryObject->join($table, $onCondition);
        return $this->with($joinedEntityName, $correlationName, true);
    }

    /**
     *
     * @param  string $method
     * @param  array $args
     * @return Zend_Entity_Mapper_SqlQueryBuilder
     */
    public function __call($method, $args)
    {
        call_user_func_array(array($this->_queryObject, $method), $args);
        return $this;
    }

    public function toSql()
    {
        return $this->getQueryObject()->assemble();
    }
}