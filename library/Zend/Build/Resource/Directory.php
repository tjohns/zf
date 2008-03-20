mee<?php

require_once 'Zend/Build/Resource/Abstract.php';

class Zend_Build_Resource_Directory extends Zend_Build_Resource_Filesystem 
{
    
    protected function create()
    {
        $directory = $this->_parameters['path']; 
        
        if (file_exists($directory)) {
            return;
        }
        
        if (!mkdir($directory)) {
            throw new Zend_Build_Resource_Exception('could not create directory ' . $directory);
        }
        
    }
    
    public function getDirname()
    {
        return parent::getRealpath() . '/';
    }
    
}
