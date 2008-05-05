<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Directory extends Zend_Tool_Provider_ZfProject_ProjectContext_FilesystemAbstract 
{
    
    public function getContextName()
    {
        return 'directory';
    }
    
    public function exists() { return false; }

    public function create($recurseChildren = true)
    {
        if (!$this->_enabled) {
            return;
        }

        // @todo do more to check the sanity here
        if (!file_exists($this->getFullPath())) {
            mkdir($this->getFullPath());
        }
        
        if ($recurseChildren) {
            foreach ($this->_children as $child) {
                $child->create($recurseChildren);
            }
        }
    }
    
    
    public function delete($recurseChildren = true) { return false; }
    public function trash() { return false; }
    
}