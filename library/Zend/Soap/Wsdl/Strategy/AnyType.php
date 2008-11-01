<?php

class Zend_Soap_Wsdl_Strategy_AnyType implements Zend_Soap_Wsdl_Strategy_Interface
{
    /**
     * Not needed in this strategy.
     *
     * @param Zend_Soap_Wsdl $context
     */
    public function setContext(Zend_Soap_Wsdl $context)
    {
        
    }

    /**
     * Returns xsd:anyType regardless of the input.
     *
     * @param string $type
     * @return string
     */
    public function addComplexType($type)
    {
        return 'xsd:anyType';
    }
}