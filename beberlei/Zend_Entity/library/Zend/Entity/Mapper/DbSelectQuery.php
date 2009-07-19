<?php

class Zend_Entity_Mapper_DbSelectQuery extends Zend_Entity_Query_QueryAbstract
{
    /**
     * @var Zend_Entity_Mapper_Abstract
     */
    protected $_loader = null;

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
     *
     * @param Zend_Entity_Mapper_Select $select
     * @param Zend_Entity_Mapper_Loader_Interface $loader
     * @param Zend_Entity_Manager_Interface $manager
     */
    public function __construct(Zend_Entity_Mapper_Select $select, Zend_Entity_Mapper_Loader_Interface $loader, Zend_Entity_Manager_Interface $manager)
    {
        $this->_select = $select;
        $this->_loader = $loader;
        $this->_entityManager = $manager;

        $loader->initSelect($select);
        $loader->initColumns($select);
    }

    public function getResultList()
    {
        $stmt = $this->_select->query(); // TODO: Fetch Mmode and Bind!
        $resultSet = $stmt->fetchAll();
        $stmt->closeCursor();

        return $this->_loader->processResultset($resultSet, $this->_entityManager);
    }

    public function setFirstResult($offset)
    {
        $this->_offset = $offset;
        $this->_select->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    public function setMaxResults($itemCountPerPage)
    {
        $this->_itemCountPerPage = $itemCountPerPage;
        $this->_select->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    public function count()
    {
        return $this->_itemCountPerPage;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_select, $method), $args);
    }

    public function __toString()
    {
        return $this->_select->assemble();
    }
}