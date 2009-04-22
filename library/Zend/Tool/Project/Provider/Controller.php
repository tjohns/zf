<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Registry.php';
require_once 'Zend/Tool/Project/Provider/View.php';


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
            /**
             * @see Zend_Tool_Project_Provider_Exception
             */
            require_once 'Zend/Tool/Project/Provider/Exception.php';

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

    /**
     * Enter description here...
     *
     * @param string $name The name of the controller to create.
     * @param bool $indexActionIncluded Whether or not to create the index action.
     */
    public function create($name, $indexActionIncluded = true)
    {
        
        $profile = $this->_getProfile();

        // determine if testing is enabled in the project
        require_once 'Zend/Tool/Project/Provider/Test.php';
        $testingEnabled = Zend_Tool_Project_Provider_Test::isTestingEnabled($profile);
        
        try {
            $controllerResource = self::createResource($profile, $name);
            if ($indexActionIncluded) {
                $indexActionResource = Zend_Tool_Project_Provider_Action::createResource($profile, 'index', $name);
                $indexActionViewResource = Zend_Tool_Project_Provider_View::createResource($profile, $name, 'index');
            }
            if ($testingEnabled) {
                $testControllerResource = Zend_Tool_Project_Provider_Test::createApplicationResource($profile, $name, 'index');
            }
            
        } catch (Exception $e) {
            $response = $this->_registry->getResponse();
            $response->setException($e);
            return;
        }

        // do the creation
        if ($this->_registry->getRequest()->isPretend()) {
            
            $this->_registry->getResponse()->appendContent('Would create a controller at '  . $controllerResource->getContext()->getPath());
            
            if (isset($indexActionResource)) {
                $this->_registry->getResponse()->appendContent('Would create an index action method in controller ' . $name);
                $this->_registry->getResponse()->appendContent('Would create a view script for the index action method at ' . $indexActionViewResource->getContext()->getPath());
            }
            
            if ($testControllerResource) {
                $this->_registry->getResponse()->appendContent('Would create a controller test file at ' . $testControllerResource->getContext()->getPath());
            }
            
        } else {
            
            $this->_registry->getResponse()->appendContent('Creating a controller at ' . $controllerResource->getContext()->getPath());
            $controllerResource->create();
            
            if (isset($indexActionResource)) {
                $this->_registry->getResponse()->appendContent('Creating an index action method in controller ' . $name);
                $indexActionResource->create();
                $this->_registry->getResponse()->appendContent('Creating a view script for the index action method at ' . $indexActionViewResource->getContext()->getPath());
                $indexActionViewResource->create();
            }
            
            if ($testControllerResource) {
                $this->_registry->getResponse()->appendContent('Creating a controller test file at ' . $testControllerResource->getContext()->getPath());
                $testControllerResource->create();
            }
            
            $this->_storeProfile();
        }

    }



}
