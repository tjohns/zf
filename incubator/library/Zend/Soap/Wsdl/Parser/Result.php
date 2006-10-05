<?php
class Zend_Soap_Wsdl_Parser_Result {
    
    public $wsdl_file = '';
    
    public $name;
    
    public $documentation;
    
    public $operations;
    
    public $portType;
    
    public $binding;
    
    public $service;
    
    public $targetNamespace;
    
    public function __construct($wsdl)
    {
        $this->wsdl_file = $wsdl;
    }
}

?>