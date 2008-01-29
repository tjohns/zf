<?php

require_once 'Zend/Build/Task/Resource/File.php';
require_once 'Zend/Console/Getopt/Provider/Interface.php';

class ZfTool_Build_Resource_File extends Zend_Build_Task_Resource_File implements Zend_Console_Getopt_Provider_Interface 
{
    
    public function getGetoptOptions()
    {
        return array(
            'filename|f=s' => 'File Name'
            );
    }
    
    public function satisfyDependencies()
    {
        if (!isset($this->_parameters['projectDirectory']) && !is_string($this->_parameters['projectDirectory'])) {
            throw new Zend_Build_Task_Action_Exception('Must have a project directory.');
        }
        
        if (!isset($this->_parameters['fileName']) && !is_string($this->_parameters['fileName'])) {
            throw new Zend_Build_Task_Action_Exception('Must have a file name.');
        }
        
    }

}