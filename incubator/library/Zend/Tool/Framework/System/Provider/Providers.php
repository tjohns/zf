<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Tool/Framework/Client/Registry.php';
require_once 'Zend/Tool/Framework/Provider/Registry.php';

class Zend_Tool_Framework_System_Provider_Providers implements Zend_Tool_Framework_Provider_Interface
{

    public function listAction()
    {
        $providerRegistry = Zend_Tool_Framework_Provider_Registry::getInstance();

        $response = Zend_Tool_Framework_Client_Registry::getInstance()->response;

        foreach ($providerRegistry->getProviderSignatures() as $provider) {
            $response->appendContent($provider->getName());
        }
    }

}