<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ControllerFile extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ClassFile
{
    
    protected $_name = 'IndexController.php';
    
    protected $_className = null;


    public function setClassName($className)
    {
        $this->_className = $className;
        return $this;
    }
    
    public function getClassName()
    {
        return $this->_className;
    }

    public function getContents()
    {
        if (($className = $this->getClassName()) == null) {
            $className = substr($this->_name, 0, strpos($this->_name, '.php'));
        }
        
        $profileSet = $this->getProfileSet();
        $profileSet->className = $className;
        
        $output = $profileSet->controllerFile();
        return $output;
    }
    
}