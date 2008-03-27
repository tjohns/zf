<?php

require_once 'Zend/Build/Resource/Abstract.php';

class Zend_Build_Resource_Filesystem extends Zend_Build_Resource_Abstract 
{
    
    protected $_useCwdWhenNoPath = false;
    
    protected $_realpath = null;
    
    public function validate()
    {

        // if there is no directoryPath and basePath/name have been supplied, use it
        if (!isset($this->_parameters['path']) && (isset($this->_parameters['basePath']) && isset($this->_parameters['name']))) {
            $this->_parameters['path'] = rtrim($this->_parameters['basePath'], '/') . '/' . $this->_parameters['name'];
        }
        
        if (!isset($this->_parameters['path']) && ($this->_useCwdWhenNoPath == true)) {
            $this->_parameters['path'] = getcwd();
        }
        
        if (!isset($this->_parameters['path'])) {
            throw new Zend_Build_Exception('"path" was not supplied to ' . get_class($this));
        }

    }

    public function execute($actionName)
    {
        parent::execute($actionName);
        $this->setRealpath();
    }

    public function setRealpath($path = null)
    {
        if ($path == null) {
            $path = $this->_realpath;
        }
        
        if ($path == null) {
            $path = $this->_parameters['path'];
        }
        
        $this->_realpath = str_replace('\\', '/', realpath($path));
    }
    
    /**
     * Enter description here...
     *
     * @return SplFileObject
     */
    public function getRealpath()
    {
        if (!$this->_realpath) {
            throw new Zend_Build_Resource_Exception('You attempted to get a file object on a resource that has not yet set the realpath to this object.'); 
        }
        
        return $this->_realpath;
    }


}
