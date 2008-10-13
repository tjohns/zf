<?php

class ZendL_Tool_Project_Structure_Node implements RecursiveIterator, Countable
{
    
    protected $_appendable = true;
    protected $_deleted = false;
    protected $_enabled = true;
    protected $_nodeContext = null;
    protected $_subNodes = array();
    protected $_position = 0;
    
    public function __construct(ZendL_Tool_Project_Structure_Context_Interface $context)
    {
        $this->setContext($context);
    }
    
    public function setContext(ZendL_Tool_Project_Structure_Context_Interface $context)
    {
        $this->_nodeContext = $context;
        if (method_exists($this->_nodeContext, 'setNode')) {
            $this->_nodeContext->setNode($this);
        }
        return $this;
    }
    
    public function getContext()
    {
        return $this->_nodeContext;
    }
    
    public function getName()
    {
        return $this->_nodeContext->getName();
    }

    public function getPersistentParameters()
    {
        if (method_exists($this->_nodeContext, 'getPersistentParameters')) {
            return $this->_nodeContext->getPersistentParameters();
        }
        
        return array();
    }
    
    public function setEnabled($enabled = true)
    {
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
    
    public function append(ZendL_Tool_Project_Structure_Node $node)
    {
        if (!$this->isAppendable()) {
            throw new Exception('Node is not appendable');
        }
        
        $this->_subNodes[] = $node;
    }
    
    public function hasSubGraphNodes()
    {
        return (count($this->_subNodes > 0) ? true : false);
    }
    
    public function current()
    {
        return $this->_subNodes[$this->_position];
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
        if (isset($this->_subNodes[$this->_position]) && ZendL_Tool_Project_Structure_Graph::isTraverseEnabled() == false) {
            while (!$this->_subNodes[$this->_position]->isEnabled()) {
                $this->next();
                if (!isset($this->_subNodes[$this->_position])) {
                    break;
                }
            }
        }
        
        return (isset($this->_subNodes[$this->_position]));
    }
    
    public function hasChildren()
    {
        return ($this->hasSubGraphNodes()) ? true : false;
    }
    
    public function getChildren()
    {
        return $this->_subNodes[$this->_position];
    }
    
    public function count()
    {
        return count($this->_subNodes);
    }
    
    public function __call($method, $arguments)
    {
        if (method_exists($this->_nodeContext, $method)) {
            return call_user_func_array(array($this->_nodeContext, $method), $arguments);
        }
    }
    
}