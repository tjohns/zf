<?php

require_once 'Zend/Tool/Framework/Client/Abstract.php';

class Zend_Tool_Framework_EmptyClient 
    extends Zend_Tool_Framework_Client_Abstract
    implements Zend_Tool_Framework_Registry_EnabledInterface
{
    
    protected $_registry = null;
    
    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
    {
        $this->_registry = $registry;
    }
    
}
