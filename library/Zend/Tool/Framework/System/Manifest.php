<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Tool/Framework/System/Provider/Version.php';
require_once 'Zend/Tool/Framework/System/Provider/Providers.php';
require_once 'Zend/Tool/Framework/System/Provider/Phpinfo.php';
require_once 'Zend/Tool/Framework/System/Provider/Manifest.php';
require_once 'Zend/Tool/Framework/System/Action/Create.php';
require_once 'Zend/Tool/Framework/System/Action/Delete.php';

class Zend_Tool_Framework_System_Manifest implements Zend_Tool_Framework_Manifest_Interface
{

    public function getProviders()
    {
        $providers = array(
            new Zend_Tool_Framework_System_Provider_Version(),
            new Zend_Tool_Framework_System_Provider_Providers(),
            new Zend_Tool_Framework_System_Provider_Phpinfo(),
            new Zend_Tool_Framework_System_Provider_Manifest()
            );

        return $providers;
    }

    public function getActions()
    {
        $actions = array(
            new Zend_Tool_Framework_System_Action_Create(),
            new Zend_Tool_Framework_System_Action_Delete()
            );

        return $actions;
    }
}
