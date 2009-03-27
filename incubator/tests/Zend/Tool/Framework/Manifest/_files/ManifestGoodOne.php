<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Tool/Framework/Manifest/Metadata.php';

require_once 'ProviderOne.php';
require_once 'ActionOne.php';

class Zend_Tool_Framework_Manifest_ManifestGoodOne implements Zend_Tool_Framework_Manifest_Interface
{
    
    public function getIndex()
    {
        return 5;
    }
    
    public function getProviders()
    {
        return new Zend_Tool_Framework_Manifest_ProviderOne();
    }
    
    public function getActions()
    {
        return new Zend_Tool_Framework_Manifest_ActionOne();
    }
    
    public function getMetadata()
    {
        return new Zend_Tool_Framework_Manifest_Metadata(array('name' => 'FooOne', 'value' => 'Bar'));
    }
    
}
