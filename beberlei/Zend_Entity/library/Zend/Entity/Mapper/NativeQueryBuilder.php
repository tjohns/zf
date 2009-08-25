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
 * @package    Db
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
 * @package    Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Mapper_NativeQueryBuilder extends Zend_Entity_Query_QueryAbstract
{
    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * @var string
     */ 
    protected $_entityName = null;

    /**
     * @var Zend_Entity_Mapper_ResultSetMapping
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

    protected $_mappings = array();

    /**
     *
     * @param Zend_Entity_Manager_Interface $manager
     * @param Zend_Entity_Mapper_QueryObject $queryObject
     */
    public function __construct(Zend_Entity_Manager_Interface $manager, Zend_Entity_Mapper_QueryObject $queryObject)
    {
        $this->_mappings = $manager->getMapper()->getStorageMappings();
        $this->_entityManager = $manager;
        $this->_queryObject = $queryObject;
        $this->_rsm = new Zend_Entity_Mapper_ResultSetMapping();
    }

    public function with($entityName)
    {
        if(!isset($this->_mappings[$entityName])) {
            throw new Zend_Entity_Exception("Invalid entity name given to ->with().");
        }

        $this->_rsm->addEntity($entityName);
        foreach($this->_mappings[$entityName]->columnNameToProperty AS $columnName => $property) {
            $this->_rsm->addProperty($entityName, $columnName, $property->getPropertyName());
        }

        $this->_entityName = $entityName;
        $this->_queryObject->from($this->_mappings[$entityName]->table);
        $this->_queryObject->columns($this->_mappings[$entityName]->sqlColumnAliasMap);
        return $this;
    }

    /**
     * @return Zend_Entity_Mapper_Select
     */
    protected function getQueryObject()
    {
        return $this->_queryObject;
    }

    public function getResultList()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_ENTITIES);
    }

    public function getResultArray()
    {
        return $this->_execute(Zend_Entity_Manager::FETCH_ARRAY);
    }

    protected function _execute($fetchMode)
    {
        $stmt = $this->getQueryObject()->query(null, $this->getParameters());
        $resultSet = $stmt->fetchAll();
        $stmt->closeCursor();

        return $this->_entityManager->getMapper()
            ->getLoader($fetchMode, $this->_entityManager)
            ->processResultset($resultSet, $this->_rsm);
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
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function getPaginatorAdapter()
    {
        return new Zend_Paginator_Adapter_DbSelect($this->getQueryObject());
    }

    /**
     *
     * @param  string $method
     * @param  array $args
     * @return Zend_Entity_Mapper_NativeQuery
     */
    public function __call($method, $args)
    {
        call_user_func_array(array($this->_queryObject, $method), $args);
        return $this;
    }

    public function __toString()
    {
        return $this->getQueryObject()->assemble();
    }
}