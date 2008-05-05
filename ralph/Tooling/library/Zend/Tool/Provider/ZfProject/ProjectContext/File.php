<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_File extends Zend_Tool_Provider_ZfProject_ProjectContext_FilesystemAbstract 
{
    
    protected $_contents = ' file contents ';
    
    public function getContextName()
    {
        return 'file';
    }
    
    public function exists() { return false; }

    public function create($recurseChildren = true)
    {
        if (!$this->_enabled) {
            return;
        }
        
        file_put_contents($this->getFullPath(), $this->_contents);
        if ($recurseChildren) {
            foreach ($this->_children as $child) {
                $child->create($recurseChildren);
            }
        }
    }
    
    public function delete($recurseChildren = true) { return false; }
    public function trash() { return false; }
    
    public function setContents($contents)
    {
        $this->_contents = $contents;
        return $this;
    }
    
    public function getContents()
    {
        return $this->_contents;
    }
    
}