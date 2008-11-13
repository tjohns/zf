<?php

class ZendL_Tool_Framework_System_Provider_Providers implements ZendL_Tool_Framework_Provider_Interface
{
    
    public function listAction()
    {
        $providerRegistry = ZendL_Tool_Framework_Provider_Registry::getInstance();
        
        foreach ($providerRegistry->getProviderSignatures() as $provider) {
            $output .= $provider->getName() . PHP_EOL;
        }
        
        ZendL_Tool_Framework_Endpoint_Registry::getInstance()->response->appendContent($output);
    }
    
}