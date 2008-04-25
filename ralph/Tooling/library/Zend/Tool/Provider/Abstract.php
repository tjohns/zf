<?php

abstract class Zend_Tool_Provider_Abstract
{
    
    protected $_name = null;
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    public function getName()
    {
        if ($this->_name == null) {
            $className = get_class($this);
            $this->_name = substr($className, strrpos($className, '_')+1);
        }
        
        return $this->_name;
    }
    
    public function execute($action)
    {
        $this->{$action}();
    }
    
    public function getActions()
    {
        $reflector = new ReflectionClass($this);
        $methods = $reflector->getMethods();
        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() == get_class($this)) {
                echo $method->getName() . ' is an action' . PHP_EOL;
            }
        }
    }
    
    public function getRequirements()
    {
        
    }
    
    public function getActionRequirements($actionName)
    {
        
    }
    
}
