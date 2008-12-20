<?php

class Zend_Tool_Framework_System_Provider_Providers implements Zend_Tool_Framework_Provider_Interface
{
    
    public function listAction()
    {
        $providerRegistry = Zend_Tool_Framework_Provider_Registry::getInstance();
        
        foreach ($providerRegistry->getProviderSignatures() as $provider) {
            $output .= $provider->getName() . PHP_EOL;
        }
        
        Zend_Tool_Framework_Client_Registry::getInstance()->response->appendContent($output);
    }
    
}