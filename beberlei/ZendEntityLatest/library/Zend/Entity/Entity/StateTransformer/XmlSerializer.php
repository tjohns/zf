<?php

class Zend_Entity_StateTransformer_XmlSerializer
{
    /**
     *
     * @param  array $values
     * @return string
     */
    static public function toXml($values)
    {
        if(!is_array($values)) {
            throw new Zend_Entity_StateTransformer_Exception(
                "Trying to transform '".gettype($values)."' ".
                "to xml but an array is expected."
            );
        }

        if(count($values) == 0) {
            return '<?xml version="1.0" ?>'."\n".'<array type="array"></array>';
        } else {
            $xml = '';
            $xml = self::_convertArrayToXml($xml, $values);
            return '<?xml version="1.0" ?>'."\n".'<array type="array">'.$xml.'</array>';
        }
    }

    /**
     *
     * @param  string $xml
     * @param  array $array
     * @return string
     */
    static protected function _convertArrayToXml($xml, array $array)
    {
        foreach($array AS $k => $v) {
            if(is_array($v)) {
                if(count($v)) {
                    $xml .= '<'.$k.' type="array">';
                    $xml = self::_convertArrayToXml($xml, $v);
                    $xml .= '</'.$k.'>';
                } else {
                    $xml .= '<'.$k.' type="array" />';
                }
            } elseif(is_string($v) || is_numeric($v)) {
                if(is_numeric($k)) {
                    $k = "array_numeric_elem".$k;
                }
                $xml .= '<'.$k.' type="'.gettype($v).'"><![CDATA['.$v.']]></'.$k.'>';
            } else {
                throw new Zend_Entity_StateTransformer_Exception(
                    "Can only serialize an array of primative integer, float and string datataypes."
                );
            }
        }
        return $xml;
    }

    /**
     *
     * @param  string $xml
     * @return array
     */
    static public function fromXml($xml)
    {
        if(!is_string($xml) || strpos($xml, '<?xml') !== 0) {
            throw new Zend_Entity_StateTransformer_Exception(
                "Can only deserialize xml strings to an array."
            );
        }

        $data = simplexml_load_string($xml);
        return self::_convertXmlToArray($data);
    }

    /**
     * @param  SimpleXmlElement $xml
     * @return array
     */
    static protected function _convertXmlToArray(SimpleXmlElement $xml)
    {
        if(count($xml->children()) > 0) {
            $a = array();
            foreach($xml AS $xmlKey => $xmlElement) {
                $xmlKey = (string)$xmlKey;
                if(strpos($xmlKey, "array_numeric_elem") === 0) {
                    $xmlKey = (int)substr($xmlKey, 18);
                }

                $a[$xmlKey] = self::_evaluateElement($xmlElement);
            }
        } else {
            $a = self::_evaluateElement($xml);
        }
        return $a;
    }

    static protected function _evaluateElement(SimpleXmlElement $xmlElement)
    {
        if(count($xmlElement->attributes()) == 1 && isset($xmlElement['type'])) {
            $type = (string)$xmlElement['type'];
            switch($type) {
                case 'array':
                    if(count($xmlElement->children()) > 0) {
                        $item = self::_convertXmlToArray($xmlElement);
                    } else {
                        $item = array();
                    }
                    break;
                case 'string':
                    $item = (string)$xmlElement;
                    break;
                case 'integer':
                    $item = intval((string)$xmlElement);
                    break;
                case 'float':
                case 'double':
                    $item = floatval((string)$xmlElement);
                    break;
                default:
                    throw new Zend_Entity_StateTransformer_Exception(
                        "Can only deserialize unknown attribute type back to an array."
                    );
            }
        } else {
            throw new Zend_Entity_StateTransformer_Exception(
                "Can only deserialize primative attributes back to an array."
            );
        }
        return $item;
    }
}