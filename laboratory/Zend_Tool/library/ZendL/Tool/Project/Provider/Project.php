<?php

class ZendL_Tool_Project_Provider_Project extends ZendL_Tool_Project_Provider_Abstract
{
    
    public function create($path = null /*, $projectprofiledata = null*/)
    {

        if ($path == null) {
            $path = getcwd();
        } else {
            $path = str_replace('\\', '/', realpath($path));
        }
        
        /**
         * @todo make sure a project doesnt alredy exist here
         */
        $structureGraph = $this->_loadExistingStructureGraph($path);

        if ($structureGraph) {
            throw new Exception('A project already exists here');
        }

        try {

            $structureParser = new ZendL_Tool_Project_Structure_Parser_Xml();
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
                <apisDirectory enabled="false" />
                <configsDirectory />
                <controllersDirectory>
                    <controllerFile controllerName="index" />
                    <controllerFile controllerName="error" />
                </controllersDirectory>
                <layoutsDirectory enabled="false" />
                <modelsDirectory />
                <modulesDirectory enabled="false" />
                <viewsDirectory>
                    <viewScriptsDirectory>
                        <viewControllerScriptsDirectory forControllerName="index">
                            <viewScriptFile scriptName="index" />
                        </viewControllerScriptsDirectory>
                        <viewControllerScriptsDirectory forControllerName="error">
                            <viewScriptFile scriptName="error" />
                        </viewControllerScriptsDirectory>
                    </viewScriptsDirectory>
                    <viewHelpersDirectory />
                    <viewFiltersDirectory enabled="false" />
                </viewsDirectory>
                <bootstrapFile />
            </applicationDirectory>
            <dataDirectory enabled="false">
                <cacheDirectory enabled="false" />
                <searchIndexesDirectory enabled="false" />
                <localesDirectory enabled="false" />
                <logsDirectory enabled="false" />
                <sessionsDirectory enabled="false" />
                <uploadsDirectory enabled="false" />
            </dataDirectory>
            <libraryDirectory>
                <zfStandardLibraryDirectory />
            </libraryDirectory>
            <publicDirectory>
                <publicStylesheetsDirectory enabled="false" />
                <publicScriptsDirectory enabled="false" />
                <publicImagesDirectory enabled="false" />
                <publicIndexFile />
                <htaccessFile />
            </publicDirectory>
            <providersDirectory enabled="false" />
            <!--
            <temporaryDirectory enabled="false" />
            <testsDirectory enabled="false" />
            -->
        </projectDirectory>
    </projectProfile>
EOS;
        return $data;
        
    }
    
}