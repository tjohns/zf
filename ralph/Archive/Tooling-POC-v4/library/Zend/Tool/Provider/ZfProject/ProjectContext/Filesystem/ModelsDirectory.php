<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ModelsDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'models';
    
    public function getContextName()
    {
        return 'modelsDirectory';
    }
    
}