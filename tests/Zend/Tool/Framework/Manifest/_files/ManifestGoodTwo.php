<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Tool/Framework/Manifest/Metadata.php';

require_once 'ProviderTwo.php';
require_once 'ActionTwo.php';

class Zend_Tool_Framework_Manifest_ManifestGoodTwo 
    implements Zend_Tool_Framework_Manifest_Interface, Zend_Tool_Framework_Registry_EnabledInterface
{
    
    protected $_registry = null;
    
    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
    {
        $this->_registry = $registry;
    }
    
    public function getIndex()
    {
        return 10;
    }
    
    public function getProviders()
    {
        return array(
            new Zend_Tool_Framework_Manifest_ProviderTwo()
            );
    }
    
    public function getActions()
    {
        return array(
            new Zend_Tool_Framework_Manifest_ActionTwo(),
            'Foo'
            );
    }
    
    public function getMetadata()
    {
        return array(
            new Zend_Tool_Framework_Manifest_Metadata(array('name' => 'FooTwo', 'value' => 'Baz1')),
            new Zend_Tool_Framework_Manifest_Metadata(array('name' => 'FooThree', 'value' => 'Baz2'))
            );
            
    }
    
}
