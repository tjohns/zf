<?php

class Zend_Soap_Wsdl_Strategy_ArrayOfType extends Zend_Soap_Wsdl_Strategy_Abstract
{
    public function addComplexType($type)
    {
        if (preg_match('/^(.*)\[\]$/i', $type, $matches)) {
            $dom = $this->getContext()->toDomDocument();
            $singulartype = $this->getContext()->getType($matches[1]);

            $wsdltype = 'ArrayOf'.substr($singulartype, 4); // strip prefix

            if (!in_array($type, $this->getContext()->getTypes())) {

                $complexType = $dom->createElement('xsd:complexType');
                $complexType->setAttribute('name', $wsdltype);

                $sequence = $dom->createElement('xsd:sequence');

                $element = $dom->createElement('xsd:element');
                $element->setAttribute('name', 'item');
                $element->setAttribute('type', $singulartype);
                $element->setAttribute('minOccurs', 0);
                $element->setAttribute('maxOccurs', 'unbounded');
                $sequence->appendChild($element);

                $complexType->appendChild($sequence);
                
                $this->getContext()->getSchema()->appendChild($complexType);
                $this->getContext()->addType($type);
            }

            return "tns:$wsdltype";
        }

        return "xsd:anyType";
    }
}