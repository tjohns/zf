<?php

abstract class Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract implements RecursiveIterator, Countable
{

    /**
     * @var Zend_Tool_Provider_ZfProject_ProfileSet_ProfileSetAbstract
     */
    protected static $_profileSet  = null;
    
    /**
     * @var string
     */
    protected $_contextName = null;
    
    /**
     * @var int
     */
    protected $_position     = 0;
    
    /**
     * @var array
     */
    protected $_subContexts       = array();
    
    /**
     * @var bool
     */
    protected $_recurseSubContexts = true;
    
    protected $_persistentParameters = array('_name');
    
    /**
     * @var bool
     */
    protected $_enabled = true;

    protected $_isDeleted = false;
    
    public function getProfileSet()
    {
        return Zend_Tool_Provider_ZfProject_ProjectProfile::getProfileSet();
    }
    
    abstract public function exists();
    
    abstract public function create();
    abstract public function delete();

    public function setDeleted($deleted)
    {
        $this->_isDeleted = ($deleted) ? true : false;
        return $this;
    }
    
    public function isDeleted()
    {
        return $this->_isDeleted;
    }
    
    public function setRecurseSubContexts($recurseSubContexts = true)
    {
        $this->_recurseSubContexts = ($recurseSubContexts) ? true : false;
        return $this;
    }
    
    public function getRecurseSubContexts()
    {
        return $this->_recurseSubContexts;
    }


    
    public function getContextName()
    {
        if ($this->_contextName == null) {
            $className = get_class($this);
            $this->_contextName = substr($className, strrpos($className, '_')+1);
        }
        
        $lowerFirst = strtolower($this->_contextName[0]);
        $this->_contextName[0] = $lowerFirst;
        
        return $this->_contextName;
    }

    public function setParameters(Array $parameters)
    {
        foreach ($parameters as $parameterName => $parameterValue) {
            $methodName = 'set' . $parameterName;
            $propertyName = '_' . $parameterName;
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($parameterValue);
            } elseif (property_exists($this, '_' . $propertyName)) {
                $this->{$propertyName} = $parameterValue;
            }
        }
        
        return $this;
    }
    
    public function getParameters()
    {
        $returnParams = array();
        foreach (get_object_vars($this) as $propertyName => $propertyValue) {
            if (is_scalar($propertyValue)) {
                if ($propertyName[0] == '_') {
                    $propertyName = substr($propertyName, 1);
                }
                $returnParams[$propertyName] = $propertyValue;
            }

        }

        return $returnParams;
    }
    
    public function append(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $contextNode)
    {
        $this->_subContexts[] = $contextNode;
        //$contextNode->setParent($this);
    }
    
    public function hasSubContexts()
    {
        return (count($this->_subContexts) > 0) ? true : false;
    }

    public function getPersistentParameters()
    {
        $params = array();
        
        foreach ($this->_persistentParameters as $persistentParameter)
        {
            // remove first underscore
            $parameterName = substr($persistentParameter, 1);
            if (property_exists($this, $persistentParameter) && is_scalar($this->{$persistentParameter})) {
                $params[$parameterName] = $this->{$persistentParameter};
            }
        }
        
        return $params;
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
    
    public function current()
    {
        return $this->_subContexts[$this->_position];
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
        return (isset($this->_subContexts[$this->_position]));
    }
    
    public function hasChildren()
    {
        return ($this->hasSubContexts()) ? true : false;
    }
    
    public function getChildren()
    {
        return $this->_subContexts[$this->_position];
    }
    
    public function count()
    {
        return count($this->_subContexts);
    }
    
    public function __isset($contextName)
    {
        foreach ($this->_subContexts as $item) {
            if ($item->getContextName() == $contextName) {
                return true;
            }
        }
        
        return false;
    }
    
    public function __get($contextName)
    {
        foreach ($this->_subContexts as $item) {
            if ($item->getContextName() == $contextName) {
                return $item;
            }
        }
        
        throw new Exception('Child context named ' . $contextName . ' was not found.');
    }
    
}