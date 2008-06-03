<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ControllerFile extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ClassFile
{
    
    protected $_name = 'index';
    
    public function getClassName()
    {
        $className = preg_replace('/_-/', ' ', $this->_name);
        $filter = new Zend_Filter_Word_SeparatorToCamelCase();
        $className = $filter->filter($className) . 'Controller';
        return $className;
    }

    public function getContents()
    {
        $profileSet = $this->getProfileSet();
        $profileSet->className = $this->getClassName();
        
        $output = $profileSet->controllerFile();
        return $output;
    }
    
    public function getFileName()
    {
        $fileName = preg_replace('/_-/', ' ', $this->_name);
        $filter = new Zend_Filter_Word_SeparatorToCamelCase();
        $fileName = $filter->filter($fileName) . 'Controller.php';
        
        return $this->getBaseDirectoryName() . '/' . $fileName;
    }
    
    
}