<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Directory.php';
require_once 'Zend/Tool/Project/Context/System/Interface.php';
require_once 'Zend/Tool/Project/Context/System/TopLevelRestrictable.php';
require_once 'Zend/Tool/Project/Context/System/NotOverwritable.php';

class Zend_Tool_Project_Context_System_ProjectDirectory 
    extends Zend_Tool_Project_Context_Filesystem_Directory
    implements Zend_Tool_Project_Context_System_Interface,
               Zend_Tool_Project_Context_System_NotOverwritable,
               Zend_Tool_Project_Context_System_TopLevelRestrictable 
{
    
    protected $_filesystemName = null;
    
    public function getName()
    {
        return 'ProjectDirectory';
    }
    
    public function init()
    {
        // get base path from attributes (would be in path attribute)
        $projectDirectory = $this->_resource->getAttribute('path');
        
        // if not, get from profile
        if ($projectDirectory == null) {
            $projectDirectory = $this->_resource->getProfile()->getAttribute('projectDirectory');
        }
        
        // if not, exception.
        if ($projectDirectory == null) {
            require_once 'Zend/Tool/Project/Exception.php';
            throw new Zend_Tool_Project_Exception('projectDirectory cannot find the directory for this project.');
        }
        
        $this->_baseDirectory = rtrim($projectDirectory, '\\/');
    }
    
    public function create()
    {
        if (file_exists($this->getPath())) {
            /*
            foreach (new DirectoryIterator($this->getPath()) as $item) {
                if (!$item->isDot()) {
                    if (Zend_Tool_Framework_Registry::getInstance()->getClient()->isInteractive()) {
                        // @todo prompt for override
                    } else {
                        require_once 'Zend/Tool/Project/Context/Exception.php';
                        throw new Zend_Tool_Project_Context_Exception('This directory is not empty, project creation aborted.');
                    }
                    break;
                }
            }
            */
        }
        
        parent::create();
    }
    
}