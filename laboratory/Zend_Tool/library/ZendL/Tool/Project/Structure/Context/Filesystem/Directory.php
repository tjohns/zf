<?php

abstract class ZendL_Tool_Project_Structure_Context_Filesystem_Directory extends ZendL_Tool_Project_Structure_Context_Filesystem_Abstract 
{
    
    public function setDirectoryName($directoryName) {
        $this->_filesystemName = $directoryName;
    }

    public function create()
    {

        // @todo do more to check the sanity here
        if (!file_exists($this->getPath())) {
            mkdir($this->getPath());
        }

    }
    
    public function recursivelyCreate()
    {
        $this->create();
        foreach ($this->_node as $subNode) {
            $subNode->recursivelyCreate();
        }
    }
    
    public function delete()
    {
        $this->_node->setDeleted(true);
        rmdir($this->getPath());        
    }
    
    public function recursivelyDelete()
    {
        if ($this->_recurseSubContexts) {
            foreach ($this->_node as $node) {
                $node->delete();
            }
        }
        
    }
    
}