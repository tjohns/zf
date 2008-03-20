<?php

require_once 'Zend/Build/Resource/Abstract.php';

class Zend_Build_Resource_File extends Zend_Build_Resource_Filesystem 
{
    
    public function create()
    {
        $file = $this->_parameters['path'];
        
        $contents = 'stuff';
        
        if (file_put_contents($file, $contents) === false) {
            throw new Zend_Build_Exception('File ' . $file . ' was not written.');
        }
        
    }
    
    public function getDirname()
    {
        return dirname(parent::getRealpath()) . '/';
    }
    
}