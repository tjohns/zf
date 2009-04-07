<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Tool/Framework/Manifest/Metadata.php';

class Zend_Tool_Framework_Manifest_ManifestBadMetadata implements Zend_Tool_Framework_Manifest_Interface
{
    
    public function getMetadata()
    {
        return array(
            new Zend_Tool_Framework_Manifest_Metadata(array('name' => 'FooTwo', 'value' => 'Baz1')),
            new ArrayObject()
            );
            
    }
    
}