<?php

class Zend_Tool_Rpc_System_Provider_Providers implements Zend_Tool_Rpc_Provider_Interface
{
    
    public function listAction()
    {
        $providerRegistry = Zend_Tool_Rpc_Provider_Registry::getInstance();
        
        foreach ($providerRegistry->getProviderSignatures() as $provider) {
            echo $provider->getName() . PHP_EOL;
        }
    }
    
}