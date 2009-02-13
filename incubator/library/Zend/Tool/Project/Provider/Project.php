<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Client/Registry.php';

class Zend_Tool_Project_Provider_Project extends Zend_Tool_Project_Provider_Abstract
{

    public function create($path = null)
    {
        if ($path == null) {
            $path = getcwd();
        } else {
            $path = str_replace('\\', '/', realpath($path));
        }

        /**
         * @todo make sure a project doesnt alredy exist here
         */
        $profile = $this->_loadProfile($path);

        if ($profile) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('A project already exists here');
        }

        $newProfile = new Zend_Tool_Project_Profile(array(
            'projectDirectory' => $path,
            'profileData' => $this->_getDefaultProfile()
        ));

        $newProfile->loadFromData();

        Zend_Tool_Framework_Client_Registry::getInstance()->response->appendContent(
            'Creating project at ' . $path
        );

        foreach ($newProfile->getIterator() as $resource) {
            $resource->getContext()->create();
        }
    }

    protected function _getDefaultProfile()
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