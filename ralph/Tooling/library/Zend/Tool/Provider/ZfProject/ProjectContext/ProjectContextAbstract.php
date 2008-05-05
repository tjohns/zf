<?php

abstract class Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract implements RecursiveIterator
{
    
    protected $_children    = array();
    
    abstract public function getContextName();
    
    abstract public function exists();
    abstract public function create($recurseChildren = true);
    abstract public function delete($recurseChildren = true);
    abstract public function trash();
    
    public function append(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $childContextNode)
    {
        $this->_children[] = $childContextNode;
    }
    
    public function current()
    {}
    
    public function key()
    {}
    
    public function next()
    {}
    
    public function rewind()
    {}
    
    public function valid()
    {}
    
    public function hasChildren()
    {}
    
    public function getChildren()
    {}
    
    
}