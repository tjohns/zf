<?php

require_once 'Zend/Build/Resource/Abstract.php';

class Zend_Build_Resource_Filesystem extends Zend_Build_Resource_Abstract 
{
    
    protected $_realpath = null;
    
    public function validate()
    {

        // if there is no directoryPath and basePath/name have been supplied, use it
        if (!isset($this->_parameters['path']) && (isset($this->_parameters['basePath']) && isset($this->_parameters['name']))) {
            $this->_parameters['path'] = rtrim($this->_parameters['basePath'], '/') . '/' . $this->_parameters['name'];
        }
        
        if (!isset($this->_parameters['path'])) {
            throw new Zend_Build_Exception('"path" was not supplied to ' . get_class($this));
        }

    }

    public function execute($actionName)
    {
        parent::execute($actionName);
        $this->_realpath = realpath($this->_parameters['path']);
    }

    public function getRealpath()
    {
        return $this->_realpath;
    }

}
