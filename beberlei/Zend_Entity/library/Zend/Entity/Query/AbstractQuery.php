<?php

abstract class Zend_Entity_Query_AbstractQuery
{
    /**
     * @var Zend_Entity_Mapper_Abstract
     */
    protected $_mapper = null;

    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * @var object
     */
    protected $_entityDefintion = null;

    final public function __construct(
        Zend_Entity_Mapper_Abstract $mapper,
        Zend_Entity_Manager_Interface $manager)
    {
        $this->_mapper = $mapper;
        $this->_entityManager = $manager;
        $this->_entityDefintion = $mapper->getDefinition();

        $this->_initQuery();
    }

    abstract protected function _initQuery();

    public function execute()
    {
        return $this->_mapper->find($this->_assembleQuery(), $this->_entityManager);
    }

    abstract protected function _assembleQuery();
}