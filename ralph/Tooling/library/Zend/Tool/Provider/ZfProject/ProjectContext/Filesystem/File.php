<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_File extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_FilesystemAbstract 
{
    
    protected $_contents = ' file contents ';

    public function setContents($contents)
    {
        $this->_contents = $contents;
        return $this;
    }
    
    public function getContents()
    {
        return $this->_contents;
    }
    
    public function exists() { return false; }

    public function create($recurseChildren = true)
    {
        if (!$this->_enabled) {
            return;
        }
        
        file_put_contents($this->getFullPath(), $this->getContents());
    }
    
    public function delete($recurseChildren = true)
    {
        
        
        return false; 
    }

    
    public function append(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $childContextNode)
    {
        throw new Exception('Appending to a file based context node is not allowed.');
    }
    
}