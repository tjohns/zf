<?php

class Zend_Tool_Project_Provider_Project extends Zend_Tool_Project_Provider_Abstract
{
    
    public function create($path = null /*, $projectprofiledata = null*/)
    {
        
        
        if ($path == null) {
            $path = $_SERVER['PWD'];
        } else {
            $path = str_replace('\\', '/', realpath($path));
        }
        
        /**
         * @todo make sure a project doesnt alredy exist here
         */
        $structureGraph = $this->_getExistingStructureGraph($path);

        if ($structureGraph) {
            throw new Exception('A project already exists here');
        }

        try {

            $structureParser = new Zend_Tool_Project_Structure_Parser_Xml();
            $structureGraph = $structureParser->unserialize($this->_getStructureData());

            $structureGraph->recursivelySetBaseDirectory($path);
            $structureGraph->recursivelyCreate();

        } catch (Exception $e) {
            die('Exception: ' . $e->getMessage());
        }
        
        echo 'creating project at ' . $path;
    }
    
    protected function _getStructureData()
    {
        
        $data = <<<EOS
<?xml version="1.0" encoding="UTF-8"?>
    <projectProfile name="default">
        <projectDirectory>
            <projectProfileFile />
            <applicationDirectory>
                <controllersDirectory>
                    <controllerFile controllerName="index" /> 
                    <controllerFile controllerName="error" />
                </controllersDirectory>
                <modelsDirectory />
                <viewsDirectory>
                    <viewScriptsDirectory>
                        <viewControllerScriptsDirectory forControllerName="index">
                            <viewScriptFile scriptName="index" />
                        </viewControllerScriptsDirectory>
                    </viewScriptsDirectory>
                    <viewHelpersDirectory />
                    <viewFiltersDirectory enabled="false" />
                </viewsDirectory>
                <modulesDirectory enabled="false" />
                <bootstrapFile />
            </applicationDirectory>
            <libraryDirectory>
                <!--<zendFrameworkStandardLibrary />-->
            </libraryDirectory>
            <publicDirectory>
                <publicIndexFile />
                <htaccessFile />
            </publicDirectory>
            <providersDirectory enabled="false" />
        </projectDirectory>
    </projectProfile>
EOS;
        return $data;
        
    }
    
}