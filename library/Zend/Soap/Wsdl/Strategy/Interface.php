<?php

interface Zend_Soap_Wsdl_Strategy_Interface
{
    public function setContext(Zend_Soap_Wsdl $context);

    /**
     * Create a complex type based on a strategy
     *
     * @param  string $type
     * @return string XSD type
     */
    public function addComplexType($type);
}