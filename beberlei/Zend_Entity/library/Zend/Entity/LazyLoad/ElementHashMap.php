<?php

class Zend_Entity_LazyLoad_ElementHashMap extends Zend_Entity_Collection_ElementHashMap
{
    /**
     * @var array
     */
    protected $_elements = null;

    /**
     * @var callable
     */
    protected $_callback = null;

    /**
     * @var array
     */
    protected $_callbackArgs = array();

    /**
     *
     * @param string $callback
     * @param array  $callbackArgs
     */
    public function __construct($callback, array $callbackArgs=array())
    {
        if(!is_callable($callback)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Invalid callback given to Element Hash Map Lazy Load container."
            );
        }
        $this->_callback = $callback;
        $this->_callbackArgs = $callbackArgs;
    }

    /**
     * Lazy loads the data
     *
     * @return void
     */
    protected function _loadData()
    {
        if($this->_elements == null) {
            $this->_elements = call_user_func_array(
                $this->_callback, $this->_callbackArgs
            );
            $this->_callback = null;
            $this->_callbackArgs = null;
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