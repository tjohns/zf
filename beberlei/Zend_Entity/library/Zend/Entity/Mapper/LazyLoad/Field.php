<?php

class Zend_Entity_Mapper_LazyLoad_Field
{
    protected $_db;
    protected $_select;
    protected $_fieldValue = null;

    public function __construct(Zend_Db_Adapter_Abstract $db, $select)
    {
        $this->_db = $db;
        $this->_select = $select;
    }

    protected function getField()
    {
        if($this->_fieldValue === null) {
            $this->_fieldValue = $this->_db->fetchOne($this->_select);
            unset($this->_db);
            unset($this->_select);
        }
        return $this->_fieldValue;
    }

    public function __toString()
    {
        return $this->getField();
    }

    public function wasLoadedFromDatabase()
    {
        if($this->_fieldValue === null) {
            return false;
        }
        return true;
    }
}