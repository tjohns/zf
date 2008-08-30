<?php

require_once 'ZendL/Tool/Project/Structure/Context/Interface.php';

abstract class ZendL_Tool_Project_Structure_Context_Filesystem_Abstract implements ZendL_Tool_Project_Structure_Context_Interface 
{ 
    
    /**
     * @var ZendL_Tool_Project_Structure_Node
     */
    protected $_node = null;
    protected $_baseDirectory = null;
    protected $_filesystemName = null;
    
    abstract public function create();

    abstract public function delete();
    
    public function setNode($node)
    {
        $this->_node = $node;
    }
    
    public function setBaseDirectory($baseDirectory)
    {
        $this->_baseDirectory = rtrim(str_replace('\\', '/', $baseDirectory), '/');
        
        return $this;
    }
    
    public function recursivelySetBaseDirectory($baseDirectory)
    {

        $this->setBaseDirectory($baseDirectory);

        foreach ($this->_node as $subNode) {
            $subNode->recursivelySetBaseDirectory($this->getPath());
        }

    }
    
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }
    
    public function setFilesystemName($filesystemName)
    {
        $this->_filesystemName = $filesystemName;
        return $this;
    }
    
    public function getFilesystemName()
    {
        return $this->_filesystemName;
    }
    
    public function getPath()
    {
        $path = $this->_baseDirectory;
        if ($this->_filesystemName) {
            $path .= '/' . $this->_filesystemName;
        }
        return $path;
    }
    
    
}