<?php

class ZendL_Tool_Rpc_Provider_Action
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
    
    
    public function getParameterRequirements()
    {
        return array();
    }
    
}



