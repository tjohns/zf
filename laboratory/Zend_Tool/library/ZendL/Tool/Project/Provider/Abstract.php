<?php

require_once 'ZendL/Tool/Rpc/Provider/Interface.php';

abstract class ZendL_Tool_Project_Provider_Abstract implements ZendL_Tool_Rpc_Provider_Interface 
{
    
    private static $_builtInClassesLoaded = false;
    
    protected $_loadedStructureGraph = null;
    
    final public function __construct()
    {
        
        if ($contextClasses = $this->getContextClasses()) {
            $this->_loadContextClassesIntoRegistry($contextClasses);
        }
        
        if (!self::$_builtInClassesLoaded) {
            $this->_loadContextClassesIntoRegistry(array(
                'ZendL_Tool_Project_Structure_Context_Zf_ProjectDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_ApisDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_ApplicationDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_BootstrapFile',
                'ZendL_Tool_Project_Structure_Context_Zf_CacheDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_ConfigsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_DataDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_HtaccessFile',
                'ZendL_Tool_Project_Structure_Context_Zf_LayoutsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_LibraryDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_LocalesDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_LogsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_ModelsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_ModulesDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_ProjectProfileFile',
                'ZendL_Tool_Project_Structure_Context_Zf_ProvidersDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_PublicDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_PublicIndexFile',
                'ZendL_Tool_Project_Structure_Context_Zf_PublicStylesheetsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_PublicScriptsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_PublicImagesDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_SearchIndexesDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_SessionsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_UploadsDirectory',
                'ZendL_Tool_Project_Structure_Context_Zf_ZfStandardLibraryDirectory'
                ));
        }
        
        
        
    }
    
    public function getContextClasses()
    {
        return array();
    }
    
    /**
     * _getProject is designed to find if there is project file in the context of where
     * the endpoint has been called from..   The search order is as follows..
     *    - traversing downwards from (PWD) - current working directory
     *    - if an enpoint variable has been registered in teh endpoint registry - key=workingDirectory
     *    - if an ENV variable with the key ZFPROJECT_PATH is found
     * 
     * @return ZendL_Tool_Project_Structure_Graph
     */
    protected function _loadExistingStructureGraph($path = null)
    {
        
        

        if ($path == null) {
            $path = getcwd();
        }
        
        $structureDataFile = $path . '/.zfproject.xml'; // @todo make this non-hard coded?
        
        if (file_exists($structureDataFile)) {
            $structureData = file_get_contents($structureDataFile);
        }
                
        $structureGraph = false;
        
        if (isset($structureData)) {
            
            $structureGraphParser = new ZendL_Tool_Project_Structure_Parser_Xml();
            $structureGraph = $structureGraphParser->unserialize($structureData);

            $structureGraph->recursivelySetBaseDirectory($path);
            
        }

        $this->_loadedStructureGraph = $structureGraph;
        return $structureGraph;
    }
    
    protected function _getExistingStructureGraph()
    {
        if (!$this->_loadedStructureGraph) {
            $this->_loadExistingStructureGraph();
        }
        
        return $this->_loadedStructureGraph;
    }
    
    protected function _storeLoadedStructureGraph()
    {
        $projectProfileFile = $this->_loadedStructureGraph->findNodeByContext('ProjectProfileFile');
        
        $name = $projectProfileFile->getContext()->getPath();
        
        echo 'Updating project profile \'' . $name . '\'' . PHP_EOL;
        $projectProfileFile->create();
    }
    
    private function _loadContextClassesIntoRegistry($contextClasses)
    {
        
        $registry = ZendL_Tool_Project_Structure_Context_Registry::getInstance();
        foreach ($contextClasses as $contextClass) {
            $registry->addContextClass($contextClass);
        }
    }
    
}