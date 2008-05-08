<?php

abstract class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_FilesystemAbstract extends Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract
{
    
    protected $_baseDirectoryName = null;
    
    protected $_name;
    
    public function setBaseDirectoryName($baseDirectoryName)
    {
        
        $this->_baseDirectoryName = rtrim(str_replace('\\', '/', $baseDirectoryName), '/');
        
        if ($this->_subContexts) {
            foreach ($this->_subContexts as $item) {
                $item->setBaseDirectoryName($this->getFullPath());
            }
        }
        
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
    

    public function getFullPath()
    {
        $fullPath = $this->_baseDirectoryName . '/';
        if ($this->_name) {
            $fullPath .= $this->_name; 
        }
        return $fullPath;
    }
    
}
