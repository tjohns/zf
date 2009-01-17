<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Directory.php';
require_once 'Zend/Tool/Project/Context/System/ISystem.php';
require_once 'Zend/Tool/Project/Context/System/ITopLevel.php';
require_once 'Zend/Tool/Project/Context/System/INotOverwritable.php';

class Zend_Tool_Project_Context_System_ProjectDirectory 
    extends Zend_Tool_Project_Context_Filesystem_Directory
    implements Zend_Tool_Project_Context_System_ISystem,
               Zend_Tool_Project_Context_System_INotOverwritable,
               Zend_Tool_Project_Context_System_ITopLevel 
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
    
}