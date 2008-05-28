<?php

class Zend_Controller_Tool_ControllerFileContext extends Zend_Tool_Project_Structure_Context_Filesystem_File 
{
    
    protected $_controllerName = 'index';
    protected $_filesystemName = 'controllerName';
    
    public function getPersistentParameters()
    {
        return array(
            'controllerName' => $this->_controllerName
            );
    }
    
    public function getName()
    {
        return 'ControllerFile';
    }
    
    public function setControllerName($controllerName) 
    {
        $this->_controllerName = $controllerName;
        $this->_filesystemName = ucfirst($controllerName) . 'Controller.php';
    }
    
    public function getControllerName()
    {
        return $this->_controllerName;
    }
    
    public function getContents()
    {
        
        $filter = new Zend_Filter_Word_DashToCamelCase();
        
        $className = $filter->filter($this->_controllerName) . 'Controller';
        
        $codeGenFile = new Zend_Tool_CodeGenerator_Php_File(array(
            'classes' => array(
                new Zend_Tool_CodeGenerator_Php_Class(array(
                    'className' => $className,
                    'extendedClassName' => 'Zend_Controller_Action',
                    'methods' => array(
                        new Zend_Tool_CodeGenerator_Php_Method(array(
                            'name' => 'init',
                            'body' => '        /* Initialize action controller here */'
                            )),
                        new Zend_Tool_CodeGenerator_Php_Method(array(
                            'name' => 'indexAction',
                            'body' => '        /* Default action for action controller */'
                            ))
                        )
                    ))
                )
            ));
            
        if ($className == 'ErrorController') {
            $classes = $codeGenFile->getClasses();
            $classes[0]->addMethod(new Zend_Tool_CodeGenerator_Php_Method(array(
                'name' => 'errorAction',
                'body' => '        // some errorAction stuff here' 
                )));
        }
        
        return $codeGenFile->toString();
    }
    
}