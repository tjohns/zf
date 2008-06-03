<?php

require_once 'Zend/Build/Resource/File.php';

class Zend_Build_Resource_ProjectFile extends Zend_Build_Resource_File
{
    
    public function init()
    {
        $this->_parameters['name'] = '.zfproject.xml';
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     * @return RecursiveIterator
     */
    public function getProfile($name = 'default')
    {
        return new SimpleXMLIterator($this->_getDefaultProfile());
    }
    
    public function _getDefaultProfile()
    {
        return <<<EOS
<?xml version="1.0" encoding="UTF-8"?>
<projectProfile name="default">
    <projectFile />
    <applicationDirectory>
        <apisDirectory enabled="false" />
        <configsDirectory />
        <controllersDirectory>
            <controllerFile name="IndexController.php" />
            <controllerFile name="ErrorController.php" />
        </controllersDirectory>
        <layoutsDirectory />
        <modelsDirectory />
        <modulesDirectory enabled="false" />
        <viewsDirectory>
            <viewScriptsDirectory>
                <directory name="index">
                    <viewScriptFile name="index.phtml" />
                </directory>
            </viewScriptsDirectory>
            <viewHelpersDirectory />
            <viewFiltersDirectory />
        </viewsDirectory>
        <bootstrapFile />        
    </applicationDirectory>
    <dataDirectory>
        <cachesDirectory />
        <searchIndexesDirectory />
        <localesDirectory />
        <logsDirectory />
        <sessionsDirectory />
        <uploadsDirectory />
    </dataDirectory>
    <docsDirectory>
    </docsDirectory>
    <libraryDirectory>
        <zendFrameworkLibrary />
    </libraryDirectory>
    <publicDirectory>
        <cssDirectory />
        <jsDirectory />
        <imagesDirectory />
        <htaccessFile />
        <indexFile />
    </publicDirectory>
    <scriptsDirectory enabled="false">
        <jobsDirectory />
        <buildsDirectory />
    </scriptsDirectory>
    <tempDirectory enable="false" />
    <testsDirectory enabled="false" />
</projectProfile>    
EOS;
        
    }
    
}

