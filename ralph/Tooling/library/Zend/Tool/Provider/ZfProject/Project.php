<?php



class Zend_Tool_Provider_ZfProject_Project extends Zend_Tool_Provider_ZfProject_ProviderAbstract
{

    
    /**
     * Create Action
     * 
     * 
     *
     * @param string $path shortName=c
     */
    public function create($path = null, $profileSetClass = 'Zend_Tool_Provider_ZfProject_ProfileSet_Default')
    {
        /**
         * @todo make sure a project doesnt alredy exist here
         */
        $projectProfile = $this->_getProjectProfile();

        if ($projectProfile) {
            throw new Exception('A project already exists here');
        }
        
        if ($path == null) {
            $path = $_SERVER['PWD'];
        }
        
        try {

            $projectProfile = new Zend_Tool_Provider_ZfProject_ProjectProfile();

            if ($projectProfile->projectDirectory) {
                $projectProfile->projectDirectory->setBaseDirectoryName($path);
            }
            
            $projectProfile->create();

        } catch (Exception $e) {
            die($e->getMessage());
        }
        
        $this->_response->setContent('creating project at ' . $path);
    }

    /**
     * _getProject is designed to find if there is project file in the context of where
     * the endpoint has been called from..   The search order is as follows..
     *    - traversing downwards from (PWD) - current working directory
     *    - if an enpoint variable has been registered in teh endpoint registry - key=workingDirectory
     *    - if an ENV variable with the key ZFPROJECT_PATH is found
     * 
     * @return unknown
     */
    protected function _getProjectProfile()
    {
        if ($path == null) {
            $path = $_SERVER['PWD'];
        }
        
        if (file_exists($path . '.zfproject.xml')) {
            
        }
        
        return null;
    }
    
    /**
     * @todo determine if this is the right place for this default sturcture/profile
     *
     * @return string
     */
    protected function _getDefaultStructureXml()
    {
        $default = <<<EOS
<projectProfile name="default">
    <projectDirectory>
        <projectProfileFile />
        <applicationDirectory>
            <controllersDirectory>
                <controllerFile name="IndexController.php" /> 
                <controllerFile name="ErrorController.php" />
            </controllersDirectory>
            <modelsDirectory />
            <viewsDirectory>
                <viewScriptsDirectory>
                    <directory name="index">
                        <viewScriptFile name="index.phtml" />
                    </directory>
                </viewScriptsDirectory>
                <viewHelpersDirectory />
            </viewsDirectory>
            <modulesDirectory enabled="false" />
            <bootstrapFile />
        </applicationDirectory>
        <libraryDirectory>
            <zendFrameworkStandardLibrary />
        </libraryDirectory>
        <publicDirectory>
            <publicIndexFile />
            <htaccessFile />
        </publicDirectory>
        <providersDirectory enabled="false" />
    </projectDirectory>
</projectProfile>
EOS;
        return $default;
    }
    
    
}
