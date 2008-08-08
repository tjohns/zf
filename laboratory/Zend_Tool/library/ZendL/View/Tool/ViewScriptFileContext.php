<?php

class ZendL_View_Tool_ViewScriptFileContext extends ZendL_Tool_Project_Structure_Context_Filesystem_File
{
    
    protected $_filesystemName = 'view.phtml';
    
    protected $_scriptName = null;
    
    public function getPersistentParameters()
    {
        return array(
            'scriptName' => $this->_scriptName
            );
    }
    
    public function getName()
    {
        return 'ViewScriptFile';
    }
    
    public function setScriptName($scriptName)
    {
        $this->_scriptName = $scriptName;
        $this->_filesystemName = $scriptName . '.phtml';
    }
    
}