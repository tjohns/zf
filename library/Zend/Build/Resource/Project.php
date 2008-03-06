<?php

require_once 'Zend/Build/Resource/Abstract.php';

class Zend_Build_Resource_Project extends Zend_Build_Resource_Abstract
{
    
    protected $_projectStructure = null;
    
    public function validate()
    {
        if (isset($this->_parameters['projectProfile'])) {
            // check to see if the projectProfile that exists is sane
        } else {
            $this->_projectStructure = new SimpleXMLElement($this->_getDefaultProjectStructure());
        }
        
        if (!isset($this->_parameter['directory'])) {
            $this->_parameters['directory'] = realpath('./');
        }
        
    }
    
    public function create()
    {
        $actionName = 'create';
        $basePath = $this->_parameters['directory'];
        $pathArray = array();
        
        $x = new SimpleXMLIterator($this->_getDefaultProjectStructure());
        
        $ri = new RecursiveIteratorIterator($x, RecursiveIteratorIterator::SELF_FIRST);
        $lastDepth = 0;
        foreach ($ri as $name => $item) {

            $currentDepth = $ri->getDepth();
            
            if ($currentDepth <= $lastDepth) {
                array_pop($pathArray);
            }
            
            if ($currentDepth < $lastDepth) {
                for ($x = 0; $x < ($lastDepth - $currentDepth); $x++) {
                    array_pop($pathArray);
                }
            }
            
            $fullPath = $basePath . '/';
            if ($pathArray) {
                $fullPath .= implode('/', $pathArray);
            }
            
            $fullPath = rtrim($fullPath, '/') . '/' . $name;
            
            echo $fullPath . PHP_EOL;

            array_push($pathArray, $name);
            
            $lastDepth = $ri->getDepth();
        }
    }
    
    protected function _getDefaultProjectStructure()
    {
        return <<<EOS
<?xml version="1.0" encoding="UTF-8"?>
<projectProfile name="default">
    <applicationDirectory>
        <apiDirectory enabled="false" />
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
    <tempDirectory enabled="false">
    </tempDirectory>
    <testsDirectory>
    </testsDirectory>
</projectProfile>    
EOS;

    }
    
}

