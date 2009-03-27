<?php

require_once 'Zend/Tool/Project/Context/Interface.php';

abstract class Zend_Tool_Project_Context_Filesystem_Abstract implements Zend_Tool_Project_Context_Interface 
{ 
    
    /**
     * @var Zend_Tool_Project_Profile_Resource
     */
    protected $_resource = null;
    protected $_baseDirectory = null;
    protected $_filesystemName = null;
    
    public function init()
    {
        $parentBaseDirectory = $this->_resource->getParentResource()->getContext()->getPath();
        $this->_baseDirectory = $parentBaseDirectory;
    }
    
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }
    
    public function setBaseDirectory($baseDirectory)
    {
        $this->_baseDirectory = rtrim(str_replace('\\', '/', $baseDirectory), '/');
        
        return $this;
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
    
    public function exists()
    {
        return file_exists($this->getPath());
    }
    
    abstract public function create();

    abstract public function delete();
    
}