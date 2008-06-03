<?php

require_once 'Zend/Build/Resource/File.php';

class Zend_Build_Resource_HtaccessFile extends Zend_Build_Resource_File 
{
    
    public function init()
    {
        $this->_parameters['name'] = '.htaccess';
    }
    
}
