<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';

class Zend_Tool_Framework_Manifest_ManifestBadProvider implements Zend_Tool_Framework_Manifest_Interface
{
    
    public function getIndex()
    {
        return 20;
    }
    
    public function getProviders()
    {
        return new ArrayObject();
    }
    
}
