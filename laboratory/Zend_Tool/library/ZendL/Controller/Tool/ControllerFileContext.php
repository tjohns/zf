<?php

require_once 'ZendL/Tool/Project/Structure/Context/Filesystem/File.php';

class ZendL_Controller_Tool_ControllerFileContext extends ZendL_Tool_Project_Structure_Context_Filesystem_File 
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
        
        $codeGenFile = new ZendL_Tool_CodeGenerator_Php_File(array(
            'classes' => array(
                new ZendL_Tool_CodeGenerator_Php_Class(array(
                    'name' => $className,
                    //'extendedClass' => 'Zend_Controller_Action',
                    'methods' => array(
                        new ZendL_Tool_CodeGenerator_Php_Method(array(
                            'name' => 'init',
                            'body' => '        /* Initialize action controller here */'
                            )),
                        new ZendL_Tool_CodeGenerator_Php_Method(array(
                            'name' => 'indexAction',
                            'body' => '        /* Default action for action controller */'
                            ))
                        )
                    ))
                )
            ));
            
        if ($className == 'ErrorController') {
            $classes = $codeGenFile->getClasses();
            $classes['ErrorController']->setMethod(new ZendL_Tool_CodeGenerator_Php_Method(array(
                'name' => 'errorAction',
                'body' => '        // some errorAction stuff here' 
                )));
        }
        
        return $codeGenFile->generate();
    }
    
    public function addAction($actionName)
    {
        require_once $this->getPath();
        $codeGenFile = ZendL_Tool_CodeGenerator_Php_File::fromReflection(new ZendL_Reflection_File($this->getPath()));
        $codeGenFileClasses = $codeGenFile->getClasses();
        $class = array_shift($codeGenFileClasses);
        $class->setMethod(array('name' => $actionName . 'Action', 'body' => '        // action body here'));
        
        file_put_contents($this->getPath(), $codeGenFile->generate());
    }
    
}