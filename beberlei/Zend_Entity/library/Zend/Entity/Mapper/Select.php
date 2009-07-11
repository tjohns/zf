<?php

class Zend_Entity_Mapper_Select extends Zend_Db_Select
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
     *
     * @var Zend_Entity_Mapper_Definition_Entity
     */
    protected $_entityDefinition = null;

    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter, Zend_Entity_Mapper_Abstract $mapper)
    {
        parent::__construct($adapter);
        $this->_mapper = $mapper;
    }

    /**
     * @param  Zend_Entity_Manager $entityManager
     * @return Zend_Entity_Mapper_Select
     */
    public function setEntityManager($entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * Prevents Wildcards to cluster the loaded columns, because Mapper enforces required columns anyways.
     *
     * @param string $type
     * @param string $name
     * @param string $cond
     * @param string|array $cols
     * @param string $schema
     */
    protected function _join($type, $name, $cond, $cols, $schema = null)
    {
        if($cols == Zend_Db_Select::SQL_WILDCARD) {
            $cols = array();
        }
        return parent::_join($type, $name, $cond, $cols, $schema);
    }

    /**
     * @return Zend_Entity_Collection_Interface
     */
    public function execute()
    {
        if($this->_entityManager == null) {
            throw new Exception("Select Statement is not connected to an Entity Manager, use Zend_Entity_Manager_Interface::find().");
        }

        return $this->_mapper->find($this->assemble(), $this->_entityManager);
    }
}
