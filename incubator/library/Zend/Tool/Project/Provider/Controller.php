<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Client/Registry.php';

/*
require_once 'Zend/Tool/Project/Resource.php';
require_once 'Zend/Tool/Framework/Client/Registry.php';
require_once 'Zend/Tool/Framework/Provider/Registry.php';
*/

class Zend_Tool_Project_Provider_Controller extends Zend_Tool_Project_Provider_Abstract
{

    /**
     * createResource will create the controllerFile resource at the appropriate location in the 
     * profile.  NOTE: it is your job to execute the create() method on the resource, as well as
     * store the profile when done.
     *
     * @param Zend_Tool_Project_Profile $profile
     * @param string $controllerName
     * @param string $moduleName
     * @return unknown
     */
    public static function createResource(Zend_Tool_Project_Profile $profile, $controllerName, $moduleName = null)
    {
        if (!is_string($controllerName)) {
            throw new Zend_Tool_Project_Provider_Exception('Zend_Tool_Project_Provider_Controller::createResource() expects \"controllerName\" is the name of a controller resource to create.');
        }
        
        $profileSearchParams = array();
        
        if ($moduleName != null && is_string($moduleName)) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => $moduleName);
        }
        
        $profileSearchParams[] = 'controllersDirectory';
        
        // @todo determine if this one already exists
        
        $newController = $profile->createResourceAt($profileSearchParams, 'controllerFile', array('controllerName' => $controllerName));
        
        return $newController;
    }

    public function create($name, $viewincluded = true)
    {

        $profile = $this->_getProfile();
        
        try {
            $controllerResource = self::createResource($profile, $name);
            if ($viewincluded) {
                $viewResource = Zend_Tool_Project_Provider_View::createResource($profile, $name, 'index');
            }
        } catch (Exception $e) {
            $response = $this->_getResponse();
            $response->setException($e);
            return;
        }
                
        // do the creation
        if ($this->_getRequest()->isPretend()) {
            $this->_getResponse()->appendContent('Would create a controller at '  . $controllerResource->getContext()->getPath());
            $this->_getResponse()->appendContent('Would create a view script at ' . $viewResource->getContext()->getPath());
        } else {
            $this->_getResponse()->appendContent('Creating a controller at ' . $controllerResource->getContext()->getPath());
            $controllerResource->create();
            $this->_getResponse()->appendContent('Creating a view at ' . $viewResource->getContext()->getPath());
            $viewResource->create();
            $this->_storeProfile();
        }

    }
    
    

}