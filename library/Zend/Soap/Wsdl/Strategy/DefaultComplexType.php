<?php

class Zend_Soap_Wsdl_Strategy_DefaultComplexType extends Zend_Soap_Wsdl_Strategy_Abstract
{
    /**
     * Add a complex type by recursivly using all the class properties fetched via Reflection.
     *
     * @param  string $type Name of the class to be specified
     * @return string XSD Type for the given PHP type
     */
    public function addComplexType($type)
    {
        if(!class_exists($type)) {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception(sprintf(
                "Cannot add a complex type %s that is not an object in 'DefaultComplexType' strategy.", $type
            ));
        }

        $dom = $this->getContext()->toDomDocument();
        $class = new ReflectionClass($type);

        $complexType = $dom->createElement('xsd:complexType');
        $complexType->setAttribute('name', $type);

        $all = $dom->createElement('xsd:all');

        foreach ($class->getProperties() as $property) {
            if (preg_match_all('/@var\s+([^\s]+)/m', $property->getDocComment(), $matches)) {

            	/**
            	 * @todo check if 'xsd:element' must be used here (it may not be compatible with using 'complexType'
            	 * node for describing other classes used as attribute types for current class
            	 */
                $element = $dom->createElement('xsd:element');
                $element->setAttribute('name', $property->getName());
                $element->setAttribute('type', $this->getContext()->getType(trim($matches[1][0])));
                $all->appendChild($element);
            }
        }

        $complexType->appendChild($all);
        $this->getContext()->getSchema()->appendChild($complexType);
        $this->getContext()->addType($type);

        return "tns:$type";
    }
}