<?php

class Zend_Tool_Project_Resource implements RecursiveIterator, Countable
{
    
    /**#@+
     * @var bool
     */
    protected $_appendable = true;
    protected $_deleted = false;
    protected $_enabled = true;

    /**
     * @var Zend_Tool_Project_Resource|string
     */
    protected $_context = null;
    
    /**
     * @var array
     */
    protected $_subResources = array();
    
    /**
     * @var int
     */
    protected $_position = 0;
    
    
    public function __construct($context)
    {
        $this->setContext($context);
    }
    
    public function setContext($context)
    {
        $this->_context = $context;
        /*
        if (method_exists($this->_resourceContext, 'setResource')) {
            $this->_resourceContext->setResource($this);
        }
        */
        return $this;
    }
    
    public function getContext()
    {
        if (is_string($this->_context)) {
            $this->_context = Zend_Tool_Project_Context_Registry::getContext($this->_context); 
        }
        
        return $this->_context;
    }
    
    /*
    public function getName()
    {
        return $this->_context->getName();
    }
    */

    public function getPersistentParameters()
    {
        if (method_exists($this->_context, 'getPersistentParameters')) {
            return $this->_context->getPersistentParameters();
        }
        
        return array();
    }
    
    public function setEnabled($enabled = true)
    {
        // convert fuzzy types to bool
        $this->_enabled = (!in_array($enabled, array('false', 'disabled', 0, -1, false), true)) ? true : false;
        return $this;
    }

    public function isEnabled()
    {
        return $this->_enabled;
    }
    

    
    public function setDeleted($deleted = true)
    {
        $this->_deleted = (bool) $deleted;
        return $this;
    }
    
    public function isDeleted()
    {
        return $this->_deleted;
    }
    
    public function setAppendable($appendable)
    {
        $this->_appendable = (bool) $appendable;
        return $this;
    }
    
    public function isAppendable()
    {
        return $this->_appendable;
    }
    
    public function append(Zend_Tool_Project_Resource $resource)
    {
        if (!$this->isAppendable()) {
            throw new Exception('Resource is not appendable');
        }
        
        $this->_subResources[] = $resource;
        return $this;
    }
    
    public function hasSubResources()
    {
        return (count($this->_subResources > 0) ? true : false);
    }
    
    public function current()
    {
        return $this->_subResources[$this->_position];
    }
    
    public function key()
    {
        return $this->_position;
    }
    
    public function next()
    {
        $this->_position++;
        return $this;
    }
    
    public function rewind()
    {
        $this->_position = 0;
        return $this;
    }
    
    public function valid()
    {
        if (isset($this->_subResources[$this->_position]) && Zend_Tool_Project_Profile::isTraverseEnabled() == false) {
            while (!$this->_subResources[$this->_position]->isEnabled()) {
                $this->next();
                if (!isset($this->_subResources[$this->_position])) {
                    break;
                }
            }
        }
        
        return (isset($this->_subResources[$this->_position]));
    }
    
    public function hasChildren()
    {
        return ($this->hasSubGraphResources()) ? true : false;
    }
    
    public function getChildren()
    {
        return $this->_subResources[$this->_position];
    }
    
    public function count()
    {
        return count($this->_subResources);
    }
    
    public function __call($method, $arguments)
    {
        if (method_exists($this->_context, $method)) {
            return call_user_func_array(array($this->_context, $method), $arguments);
        }
    }
    
}