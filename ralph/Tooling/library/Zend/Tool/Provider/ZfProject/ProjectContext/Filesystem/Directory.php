<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_FilesystemAbstract 
{
    
    public function exists() { return false; }

    public function create()
    {
        if (!$this->_enabled) {
            return;
        }

        // @todo do more to check the sanity here
        if (!file_exists($this->getFullPath())) {
            mkdir($this->getFullPath());
        }
        
        if ($this->_recurseSubContexts) {
            foreach ($this->_subContexts as $item) {
                $item->create($recurseSubContexts);
            }
        }
    }
    
    
    public function delete() { return false; }
    
}