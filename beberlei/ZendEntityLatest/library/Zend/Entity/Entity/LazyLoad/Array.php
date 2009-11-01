<?php

class Zend_Entity_LazyLoad_Array extends Zend_Entity_Collection_Array
{
    /**
     * @var array
     */
    protected $_elements = null;

    /**
     * @var callable
     */
    protected $_select = null;

    /**
     * @var string
     */
    protected $_mapKey = null;

    /**
     * @var string
     */
    protected $_element = null;

    /**
     *
     * @param string $callback
     * @param array  $callbackArgs
     */
    public function __construct(Zend_Db_Select $select, $mapKey, $element)
    {
        $this->_select = $select;
        $this->_mapKey = $mapKey;
        $this->_element = $element;
    }

    /**
     * Lazy loads the data
     *
     * @return void
     */
    protected function _loadData()
    {
        if($this->_elements === null) {
            $stmt = $this->_select->query();

            $this->_elements = array();
            foreach($stmt->fetchAll() AS $row) {
                $this->_elements[$row[$this->_mapKey]] = $row[$this->_element];
            }
            $this->_select = null;
        }
    }

    public function key()
    {
        $this->_loadData();
        return parent::key();
    }

    public function next()
    {
        $this->_loadData();
        return parent::next();
    }

    public function current()
    {
        $this->_loadData();
        return parent::current();
    }

    public function valid()
    {
        $this->_loadData();
        return parent::valid();
    }

    public function rewind()
    {
        $this->_loadData();
        return parent::rewind();
    }

    public function offsetGet($index)
    {
        $this->_loadData();
        return parent::offsetGet($index);
    }

    public function offsetSet($index, $value)
    {
        $this->_loadData();
        return parent::offsetSet($index, $value);
    }

    public function offsetExists($index)
    {
        $this->_loadData();
        return parent::offsetExists($index);
    }

    public function offsetUnset($index)
    {
        $this->_loadData();
        return parent::offsetUnset($index);
    }

    public function count()
    {
        $this->_loadData();
        return parent::count();
    }

    public function __ze_getRemoved()
    {
        return $this->_removed;
    }

    public function __ze_getAdded()
    {
        return $this->_added;
    }

    public function __ze_wasLoadedFromDatabase()
    {
        if($this->_elements == null) {
            return false;
        } else {
            return true;
        }
    }
}