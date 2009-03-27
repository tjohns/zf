<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Tool/Framework/Registry.php';

class Zend_Tool_Framework_System_Provider_Providers implements Zend_Tool_Framework_Provider_Interface
{

    public function listAction()
    {
        
        $clientRegistry = Zend_Tool_Framework_Registry::getInstance();
        $providerRepository = $clientRegistry->getProviderRepository();

        $response = Zend_Tool_Framework_Registry::getInstance()->response;

        foreach ($providerRepository->getProviderSignatures() as $provider) {
            $response->appendContent($provider->getName());
        }
    }

}