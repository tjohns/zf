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
class Zend_Entity_Mapper_NativeQuery extends Zend_Entity_Query_QueryAbstract
{
    /**
     * Name of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * @var Zend_Entity_Mapper_Mapper
     */
    protected $_mapper = null;

    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * @var Zend_Entity_Mapper_Select
     */
    protected $_select = null;

    /**
     * @var int
     */
    protected $_offset = null;

    /**
     * @var int
     */
    protected $_itemCountPerPage = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $_rowCount = null;

    protected $_entityName = null;

    /**
     *
     * @param Zend_Entity_Mapper_Mapper $mapper
     * @param Zend_Entity_Manager_Interface $manager
     */
    public function __construct(Zend_Entity_Mapper_Mapper $mapper, Zend_Entity_Manager_Interface $manager)
    {
        $this->_mapper = $mapper;
        $this->_entityManager = $manager;
    }

    public function with($entityName)
    {
        $this->_entityName = $entityName;
        $loader = $this->_mapper->getLoader(null, $entityName);
        $loader->initSelect($this->getSelect());
        $loader->initColumns($this->getSelect());
        return $this;
    }

    /**
     * @return Zend_Entity_Mapper_Select
     */
    protected function getSelect()
    {
        if($this->_select == null) {
            $this->_select = $this->_mapper->select();
        }
        return $this->_select;
    }

    public function getResultList()
    {
        $stmt = $this->getSelect()->query(null, $this->getParameters());
        $resultSet = $stmt->fetchAll();
        $stmt->closeCursor();

        return $this->_mapper->getLoader(null, $this->_entityName)
            ->processResultset($resultSet, $this->_entityManager);
    }

    public function setFirstResult($offset)
    {
        $this->_offset = $offset;
        $this->getSelect()->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    public function setMaxResults($itemCountPerPage)
    {
        $this->_itemCountPerPage = $itemCountPerPage;
        $this->getSelect()->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    /**
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function getPaginatorAdapter()
    {
        return new Zend_Paginator_Adapter_DbSelect($this->getSelect());
    }

    /**
     *
     * @param  string $method
     * @param  array $args
     * @return Zend_Entity_Mapper_NativeQuery
     */
    public function __call($method, $args)
    {
        call_user_func_array(array($this->getSelect(), $method), $args);
        return $this;
    }

    public function __toString()
    {
        return $this->getSelect()->assemble();
    }
}