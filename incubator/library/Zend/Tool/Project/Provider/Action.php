<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Client/Registry.php';

class Zend_Controller_Tool_Provider_Action extends Zend_Tool_Project_Provider_Abstract
{

    /*
    public function getContextClasses()
    {
        return array(
            'Zend_Controller_Tool_ControllerFileContext', // Context: ControllerFile
            'Zend_Controller_Tool_ControllersDirectoryContext' // Context: ControllersDirectory
            );
    }
    */

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

        Zend_Tool_Framework_Client_Registry::getInstance()->response->appendContent(
            'Adding action \'' . $actionName . '\' to controller \'' . $controllername
        );

        $controllerContext->addAction($actionName);

        if ($viewincluded) {
            $viewProvider = Zend_Tool_Framework_Provider_Registry::getInstance()->getProvider('View');
            $viewProvider->create($controllername, $actionName);
        }

    }

}