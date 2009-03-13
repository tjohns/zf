<?php

require_once 'Zend/Tool/Framework/Action/Interface.php';

class Zend_Tool_Framework_Action_Base implements Zend_Tool_Framework_Action_Interface 
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



