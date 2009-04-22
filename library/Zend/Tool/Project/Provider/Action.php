<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';

class Zend_Tool_Project_Provider_Action extends Zend_Tool_Project_Provider_Abstract
{

    public static function createResource(Zend_Tool_Project_Profile $profile, $actionName, $controllerName, $moduleName = null)
    {
        
        if (!is_string($actionName)) {
            throw new Zend_Tool_Project_Provider_Exception('Zend_Tool_Project_Provider_Action::createResource() expects \"actionName\" is the name of a action resource to create.');
        }

        if (!is_string($controllerName)) {
            throw new Zend_Tool_Project_Provider_Exception('Zend_Tool_Project_Provider_Action::createResource() expects \"controllerName\" is the name of a controller resource to create.');
        }
        
        $searchParams = array();
        
        if ($moduleName) {
            $searchParams[] = 'modulesDirectory';
            $searchParams['moduleDirectory'] = array('name' => $moduleName);
        }
        
        $searchParams[] = 'controllersDirectory';
        $searchParams['controllerFile'] = array('controllerName' => $controllerName);

        $actionMethod = $profile->createResourceAt($searchParams, 'ActionMethod', array('actionName' => $actionName));
        
        return $actionMethod;
    }

    public function create($name, $controllerName = 'index', $viewIncluded = true)
    {

        $profile = $this->_loadProfile();
        
        $actionMethod = self::createResource($profile, $name, $controllerName);
        
        if ($this->_registry->getRequest()->isPretend()) {
            $this->_registry->getResponse()->appendContent(
                'Would create an action named ' . $name . 
                ' inside controller at ' . $actionMethod->getParentResource()->getContext()->getPath()
                );
        } else {
            $this->_registry->getResponse()->appendContent(
                'Creating an action named ' . $name . 
                ' inside controller at ' . $actionMethod->getParentResource()->getContext()->getPath()
                );
            $actionMethod->create();
            $this->_storeProfile();
        }
        
        if ($viewIncluded) {
            $viewResource = Zend_Tool_Project_Provider_View::createResource($profile, $controllerName, $name);
            
            if ($this->_registry->getRequest()->isPretend()) {
                $this->_registry->getResponse()->appendContent(
                    'Would create a view script for the ' . $name . ' action method at ' . $viewResource->getContext()->getPath()
                    );
            } else {
                $this->_registry->getResponse()->appendContent(
                    'Creating a view script for the ' . $name . ' action method at ' . $viewResource->getContext()->getPath()
                    );
                $viewResource->create();
            }
            
        }
        
    }

}
