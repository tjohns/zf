<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';
require_once 'Zend/CodeGenerator/Php/File.php';



class Zend_Tool_Project_Context_Zf_ProjectProviderFile extends Zend_Tool_Project_Context_Filesystem_File 
{
    
    protected $_projectProviderName = null;
    protected $_actionNames = array();
    
    public function init()
    {
        $this->_projectProviderName = $this->_resource->getAttribute('projectProviderName');
        $this->_actionNames = $this->_resource->getAttribute('actionNames');
        $this->_filesystemName = ucfirst($this->_projectProviderName) . 'Provider.php';
        
        if (strpos($this->_actionNames, ',')) {
            $this->_actionNames = explode(',', $this->_actionNames);
        } else {
            $this->_actionNames = ($this->_actionNames) ? array($this->_actionNames) : array();
        }

        parent::init();
    }
    
    public function getPersistentAttributes()
    {
        return array(
            'projectProviderName' => $this->getProjectProviderName(),
            'actionNames' => implode(',', $this->_actionNames)
            );
    }
    
    public function getName()
    {
        return 'ProjectProviderFile';
    }
    
    
    public function getProjectProviderName()
    {
        return $this->_projectProviderName;
    }


    public function getContents()
    {

        $filter = new Zend_Filter_Word_DashToCamelCase();
        
        $className = $filter->filter($this->_projectProviderName) . 'Provider';
        
        $class = new Zend_CodeGenerator_Php_Class(array(
            'name' => $className,
            'extendedClass' => 'Zend_Tool_Project_Provider_Abstract'
            ));
        
        $methods = array();
        foreach ($this->_actionNames as $actionName) {
            $methods[] = new Zend_CodeGenerator_Php_Method(array(
                'name' => $actionName,
                'body' => '        /** @todo Implementation */'
                ));
        }
        
        if ($methods) {
            $class->setMethods($methods);
        }
        
        $codeGenFile = new Zend_CodeGenerator_Php_File(array(
            'requiredFiles' => array(
                'Zend/Tool/Project/Provider/Abstract.php',
                'Zend/Tool/Project/Provider/Exception.php'
                ),
            'classes' => array($class)
            ));
        
        return $codeGenFile->generate();
    }
   
}
