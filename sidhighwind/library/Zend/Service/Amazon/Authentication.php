<?php

abstract class Zend_Service_Amazon_Authentication
{
    protected $_accessKey;
    
    protected $_secretKey;
    
    protected $_apiVersion;
    
    public function __construct($accessKey, $secretKey, $apiVersion)
    {
        $this->setAccessKey($accessKey);
        $this->setSecretKey($secretKey);
        $this->setApiVersion($apiVersion);
    }
    
    public function setAccessKey($accessKey) {
        $this->_accessKey = $accessKey;
    }
    
    public function setSecretKey($secretKey) {
        $this->_secretKey = $secretKey;
    }
    
    public function setApiVersion($apiVersion) {
        $this->_apiVersion = $apiVersion;
    }
}

