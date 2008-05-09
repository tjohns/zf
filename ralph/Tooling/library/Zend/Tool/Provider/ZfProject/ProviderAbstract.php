<?php

abstract class Zend_Tool_Provider_ZfProject_ProviderAbstract extends Zend_Tool_Provider_Abstract
{
    
    /**
     * _getProject is designed to find if there is project file in the context of where
     * the endpoint has been called from..   The search order is as follows..
     *    - traversing downwards from (PWD) - current working directory
     *    - if an enpoint variable has been registered in teh endpoint registry - key=workingDirectory
     *    - if an ENV variable with the key ZFPROJECT_PATH is found
     * 
     * @return Zend_Tool_Provider_ZfProject_ProjectProfile
     */
    protected function _getProjectProfile($path = null)
    {
        
        if ($path == null) {
            $path = $_SERVER['PWD'];
        }
        
        $projectProfileFilename = $path . '/.zfproject.xml'; 
        
        if (file_exists($projectProfileFilename)) {
            $projectProfileXml = file_get_contents($projectProfileFilename);
        }
        
        $projectProfile = false;
        
        if (isset($projectProfileXml)) {
            
            $projectProfile = new Zend_Tool_Provider_ZfProject_ProjectProfile($projectProfileXml);

            if ($projectProfile->projectDirectory) {
                $projectProfile->projectDirectory->setBaseDirectoryName($path);
            }
        }
        
        return $projectProfile;
    }
    
}