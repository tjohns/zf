<?php

class Zend_Tool_Provider_ZfProject_ProfileSet_Default extends Zend_Tool_Provider_ZfProject_ProfileSet_ProfileSetAbstract
{
    
    protected $_profileName = 'default';
    
    public function projectProfile()
    {

        return '<?xml version="1.0" encoding="UTF-8"?>
            <projectProfile name="default">
                <projectDirectory>
                    <projectProfileFile />
                    <applicationDirectory>
                        <controllersDirectory>
                            <controllerFile name="index" /> 
                            <controllerFile name="error" />
                        </controllersDirectory>
                        <modelsDirectory />
                        <viewsDirectory>
                            <viewScriptsDirectory>
                                <viewControllerScriptsDirectory name="index">
                                    <viewScriptFile name="index" />
                                </viewControllerScriptsDirectory>
                            </viewScriptsDirectory>
                            <viewHelpersDirectory />
                            <viewFiltersDirectory enabled="false" />
                        </viewsDirectory>
                        <modulesDirectory enabled="false" />
                        <bootstrapFile />
                    </applicationDirectory>
                    <libraryDirectory>
                        <zendFrameworkStandardLibrary />
                    </libraryDirectory>
                    <publicDirectory>
                        <publicIndexFile />
                        <htaccessFile />
                    </publicDirectory>
                    <providersDirectory enabled="false" />
                </projectDirectory>
            </projectProfile>';

    }
    
    public function controllerFile()
    {
        $className = $this->_parameters->className;
        

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
    
    public function publicIndexFile()
    {
        
        $codeGenFile = new Zend_Tool_CodeGenerator_Php_File(array(
            'requiredFiles' => array('../application/bootstrap.php')
            ));
        
        return $codeGenFile->toString();
    }
    
    public function bootstrapFile()
    {
        
        $codeGenFile = new Zend_Tool_CodeGenerator_Php_File(array(
            'requiredFiles' => array('Zend/Loader.php'),
            'body' => 'Zend_Loader::registerAutoloader();' . PHP_EOL . PHP_EOL . '// bootstrap application here'
            ));
        
        return $codeGenFile->toString();
    }
    
}