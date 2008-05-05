<?php



class Zend_Tool_Provider_ZfProject_Project extends Zend_Tool_Provider_ZfProject_ProviderAbstract
{

    
    /**
     * Create Action
     * 
     * 
     *
     * @param string $path
     */
    public function create($path = null)
    {
        /**
         * @todo make sure a project doesnt alredy exist here
         */
        //$project = $this->_getProject();

        if ($path == null) {
            $path = $_SERVER['PWD'];
        }
        
        $structure = Zend_Tool_Provider_ZfProject_ProjectContext_ProjectProfileFile::fromXml($this->_getDefaultStructureXml(), $path);
        $structure->create(true);
        
        
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
    protected function _getProject()
    {
        if ($path == null) {
            $path = $_SERVER['PWD'];
        }        
        
        if (file_exists($path . '.zfproject.xml')) {
            
        }
        
        return false;
    }
    
    /**
     * @todo determine if this is the right place for this default sturcture/profile
     *
     * @return string
     */
    protected function _getDefaultStructureXml()
    {
        $default = <<<EOS
<projectDirectory>
    <projectProfileFile />
    <applicationDirectory>
        <controllersDirectory>
            <controllerFile name="IndexController.php" /> <!-- could also be controllerName="index" -->
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
</projectDirectory>
EOS;
        return $default;
    }
    
    
}

/*
<?php

class Zend_Tool_Provider_ZfProject_Project extends Zend_Tool_Provider_Abstract
{
    
    protected $_name = 'project';
    
    protected function _getDefaultStructureXml()
    {
        $default = <<<EOS
<projectProfile>
    <directory context="application">
        <directory context="controllers">
            <file context="controller" name="IndexController.php" /> <!-- could also be controllerName="index" -->
            <file context="controller" name="ErrorController.php" />
        </directory>
        <directory context="models">
        </directory>
        <directory context="views">
            <directory context="viewScripts" name="scripts">
                <directory name="index">
                    <file context="viewScript" name="index.phtml" />
                </directory>
            </directory>
            <directory context="viewHelpers" name="helpers">
            </directory>
        </directory>
        <file context="bootstrap" />
    </directory>
    <directory context="library">
        <directory context="ZendFrameworkStandardLibrary" />
    </directory>
    <directory context="public">
        <file context="publicIndex" />
        <file context="htaccess" />
    </directory>
    
</projectProfile>
EOS;
    }
    
    
    
}
*/