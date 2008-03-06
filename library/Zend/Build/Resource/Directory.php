<?php

require_once 'Zend/Build/Resource/Abstract.php';

class Zend_Build_Resource_Directory extends Zend_Build_Resource_Abstract
{

    protected $_basePath = null;
    
    public function validate()
    {
        if (isset($this->_parameters['basePath']) && isset($this->_parameters['name'])) {
            $this->_parameters['directoryPath'] = $this->_parameters['basePath'] . '/' . $this->_parameters['name'];
        }
        
        if (!isset($this->_parameters['directoryPath'])) {
            throw new Zend_Build_Exception('"directoryPath" was not supplied to ' . get_class($this));
        }
        
    }
    
    public function create()
    {
        //echo 'creating directory ' . $this->_parameters['directoryPath'];
        mkdir($this->_parameters['directoryPath']);
    }
    
    
}