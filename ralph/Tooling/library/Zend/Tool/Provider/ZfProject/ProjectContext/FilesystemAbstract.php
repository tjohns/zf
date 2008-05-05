<?php

abstract class Zend_Tool_Provider_ZfProject_ProjectContext_FilesystemAbstract extends Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract
{
    
    protected $_baseDirectoryName = null;
    
    protected $_name;
    protected $_enabled = true;
    
    public function setParameters(Array $parameters)
    {
        foreach ($parameters as $parameterName => $parameterValue) {
            $methodName = 'set' . $parameterName;
            $this->{$methodName}($parameterValue);
        }
    }
    
    public function setBaseDirectoryName($baseDirectoryName)
    {
        
        $this->_baseDirectoryName = rtrim(str_replace('\\', '/', $baseDirectoryName), '/');
        return $this;
    }
    
    public function getBaseDirectoryName()
    {
        return $this->_baseDirectoryName;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function setEnabled($enabled = true)
    {
        $this->_enabled = (!in_array($enabled, array('false', 'disabled', 0, -1, false), true)) ? true : false;
        return $this;
    }

    public function isEnabled()
    {
        return $this->_enabled;
    }
    
    public function getFullPath()
    {
        $fullPath = $this->_baseDirectoryName . '/';
        if ($this->_name) {
            $fullPath .= $this->_name; 
        }
        return $fullPath;
    }
    
}
