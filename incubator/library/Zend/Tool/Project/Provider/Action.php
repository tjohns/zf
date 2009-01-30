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
        
        
        
    }

    public function create($name, $controllername = 'index', $viewincluded = true)
    {
        $actionName = $name;

        $profile = $this->_getExistingProfile();

        $controllerFileNode = $profile->findNodeByContext(array(
            'controllerFile' => array(
                'controllerName' => $controllername
            )
        ));

        $controllerContext = $controllerFileNode->getContext();

        $this->_getResponse()->appendContent('Adding action \'' . $actionName . '\' to controller \'' . $controllername);

        $controllerContext->addAction($actionName);

        if ($viewincluded) {
            $viewProvider = Zend_Tool_Framework_Provider_Registry::getInstance()->getProvider('View');
            $viewProvider->create($controllername, $actionName);
        }

    }

}