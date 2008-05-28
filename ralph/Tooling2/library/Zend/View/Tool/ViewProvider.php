<?php

class Zend_View_Tool_ViewProvider extends Zend_Tool_Project_Provider_Abstract 
{
    
    public function getContextClasses()
    {
        return array(
            'Zend_View_Tool_ViewScriptFileContext',
            'Zend_View_Tool_ViewsDirectoryContext',
            'Zend_View_Tool_ViewScriptsDirectoryContext',
            'Zend_View_Tool_ViewControllerScriptsDirectoryContext',
            'Zend_View_Tool_ViewHelpersDirectoryContext',
            'Zend_View_Tool_ViewFiltersDirectoryContext'
            );
        
    }
    
    public function create()
    {
        Zend_Debug::dump(Zend_Tool_Project_Structure_Context_Registry::getInstance());
        echo 'I am creating a controller.'; die();
    }
    
}