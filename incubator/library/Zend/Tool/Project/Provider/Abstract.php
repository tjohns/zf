<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Tool/Project/Context/Registry.php';
require_once 'Zend/Tool/Project/ProfileFileParser/Xml.php';

abstract class Zend_Tool_Project_Provider_Abstract implements Zend_Tool_Framework_Provider_Interface 
{
    
    private static $_builtInClassesLoaded = false;
    
    /**
     * @var Zend_Tool_Project_Profile
     */
    protected $_loadedProfile = null;
    
    final public function __construct()
    {
        

        
        if (!self::$_builtInClassesLoaded) {
            $this->_loadContextClassesIntoRegistry(array(

            
                
                'Zend_Tool_Project_Context_Zf_ApisDirectory',
                'Zend_Tool_Project_Context_Zf_ApplicationDirectory',
                'Zend_Tool_Project_Context_Zf_BootstrapFile',
                'Zend_Tool_Project_Context_Zf_CacheDirectory',
                'Zend_Tool_Project_Context_Zf_ConfigsDirectory',
                'Zend_Tool_Project_Context_Zf_ControllerFile',
                'Zend_Tool_Project_Context_Zf_ControllersDirectory',
                'Zend_Tool_Project_Context_Zf_DataDirectory',
                'Zend_Tool_Project_Context_Zf_HtaccessFile',
                'Zend_Tool_Project_Context_Zf_LayoutsDirectory',
                'Zend_Tool_Project_Context_Zf_LibraryDirectory',
                'Zend_Tool_Project_Context_Zf_LocalesDirectory',
                'Zend_Tool_Project_Context_Zf_LogsDirectory',
                'Zend_Tool_Project_Context_Zf_ModelsDirectory',
                'Zend_Tool_Project_Context_Zf_ModulesDirectory',
                

                'Zend_Tool_Project_Context_Zf_PublicDirectory',
                'Zend_Tool_Project_Context_Zf_PublicIndexFile',
                'Zend_Tool_Project_Context_Zf_PublicStylesheetsDirectory',
                'Zend_Tool_Project_Context_Zf_PublicScriptsDirectory',
                'Zend_Tool_Project_Context_Zf_PublicImagesDirectory',
                'Zend_Tool_Project_Context_Zf_SearchIndexesDirectory',
                'Zend_Tool_Project_Context_Zf_SessionsDirectory',
                'Zend_Tool_Project_Context_Zf_UploadsDirectory',
                'Zend_Tool_Project_Context_Zf_ViewControllerScriptsDirectory',
                'Zend_Tool_Project_Context_Zf_ViewFiltersDirectory',
                'Zend_Tool_Project_Context_Zf_ViewHelpersDirectory',
                'Zend_Tool_Project_Context_Zf_ViewScriptFile',
                'Zend_Tool_Project_Context_Zf_ViewScriptsDirectory',
                'Zend_Tool_Project_Context_Zf_ViewsDirectory',
                'Zend_Tool_Project_Context_Zf_ZfStandardLibraryDirectory'
                ));
        }
        
        if ($contextClasses = $this->getContextClasses()) {
            $this->_loadContextClassesIntoRegistry($contextClasses);
        }
        
    }
    
    public function getContextClasses()
    {
        return array();
    }
    
    /**
     * _getProject is designed to find if there is project file in the context of where
     * the client has been called from..   The search order is as follows..
     *    - traversing downwards from (PWD) - current working directory
     *    - if an enpoint variable has been registered in teh client registry - key=workingDirectory
     *    - if an ENV variable with the key ZFPROJECT_PATH is found
     * 
     * @return Zend_Tool_Project_Profile
     */
    protected function _loadExistingProfile($path = null)
    {
        
        if ($path == null) {
            $path = getcwd();
        }
        
        $profileFile = $path . '/.zfproject.xml'; // @todo make this non-hard coded?
        
        if (file_exists($profileFile)) {
            $profileFileContents = file_get_contents($profileFile);
        }
                
        $profile = false;
        
        if (isset($profileFileContents)) {
            
            $profileFileParser = new Zend_Tool_Project_ProfileFileParser_Xml();
            $profile = $profileFileParser->unserialize($profileFileContents);
            
            //$profile->
            
            $profile->recursivelySetBaseDirectory($path);
            
        }

        $this->_loadedProfile = $profile;
        return $profile;
    }
    
    protected function _createNode($searchNodeToAppendTo, Zend_Tool_Project_Context_Interface $context)
    {
        if ($this->_loadedProfile == null) {
            throw new Zend_Tool_Project_Provider_Exception('You must call _loadExistingProfile first.');
        }

        $searchNode = $this->_loadedProfile->findNodeByContext($searchNodeToAppendTo);
        
        $newNode = new ZendL_Tool_Project_Resource($context);
        $searchNode->append($newNode);
    }
    
    protected function _getExistingProfile()
    {
        if (!$this->_loadedProfile) {
            $this->_loadExistingProfile();
        }
        
        return $this->_loadedProfile;
    }
    
    protected function _storeLoadedProfile()
    {
        $projectProfileFile = $this->_profile->findNodeByContext('ProjectProfileFile');
        
        $name = $projectProfileFile->getContext()->getPath();
        
        echo 'Updating project profile \'' . $name . '\'' . PHP_EOL;
        $projectProfileFile->create();
    }
    
    private function _loadContextClassesIntoRegistry($contextClasses)
    {
        
        $registry = Zend_Tool_Project_Context_Registry::getInstance();
        foreach ($contextClasses as $contextClass) {
            $registry->addContextClass($contextClass);
        }
    }
    
}