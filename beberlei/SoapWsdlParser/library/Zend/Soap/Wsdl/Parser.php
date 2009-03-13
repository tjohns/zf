<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Wsdl
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Parser.php 13673 2009-01-17 17:47:07Z beberlei $
 */

require_once 'Zend/Soap/Wsdl/Document.php';
require_once "Zend/Soap/Wsdl/Element/Collection.php";
require_once 'Zend/Soap/Wsdl/Element/Binding.php';
require_once 'Zend/Soap/Wsdl/Element/Message.php';
require_once 'Zend/Soap/Wsdl/Element/Part.php';
require_once 'Zend/Soap/Wsdl/Element/Operation.php';
require_once 'Zend/Soap/Wsdl/Element/Port.php';
require_once 'Zend/Soap/Wsdl/Element/Service.php';
require_once 'Zend/Soap/Wsdl/Element/Type.php';

/**
 * Zend_Soap_Wsdl_Parser
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Wsdl
 */
class Zend_Soap_Wsdl_Parser
{
    /**
     * WSDL Subelement Types based on Occurence Properties
     */
    const ELEMENT_TYPE_UNIQUE = 1;
    const ELEMENT_TYPE_ARRAY  = 2;

    const NAMESPACE_WSDL_URI = "http://schemas.xmlsoap.org/wsdl";
    const NAMESPACE_XSD_URI  = "http://www.w3.org/2001/XMLSchema";
    const NAMESPACE_SOAP_URI = "http://schemas.xmlsoap.org/wsdl/soap/";

    /**
     * DOMDocument instance that represents WSDL File
     *
     * @var XMLReader
     */
    protected $_wsdlXmlReader;

    /**
     * Which URI points to SOAP, WSDL and XSD namespace definitions?
     *
     * @var array
     */
    protected $_uri2namespace = array(
        "http://schemas.xmlsoap.org/wsdl/"      => "wsdl",
        "http://schemas.xmlsoap.org/wsdl/soap/" => "soap",
        "http://www.w3.org/2001/XMLSchema/"     => "xsd",
        "http://www.w3.org/2000/xmlns/"         => "xmlns",
    );

    /**
     * Which prefix should be normalized to which standard prefix?
     *
     * Certain WSDL services contain non-standard namespaces of SOAP, WSDL or XSD definitions.
     *
     * @var array
     */
    protected $_normalizedNamespaces = array(
        "wsdl"      => "wsdl",
        "default"   => "wsdl",
        "soap"      => "soap",
        "xsd"       => "xsd",
        "xmlns"     => "xmlns",
    );

    /**
     * Names of the required node sub elements that will be set by default by the parser.
     *
     * @var array
     */
    protected static $_requiredNodeSubElements = array(
        "wsdl:definitions"  => array(
            "wsdl:types", "wsdl:portType", "wsdl:operation", "wsdl:binding",
            "wsdl:message", "wsdl:service", "wsdl:documentation"
        ),
        "wsdl:types"        => array("xsd:schema"),
        "wsdl:message"      => array("wsdl:part"),
        "wsdl:portType"     => array("wsdl:operation"),
        "wsdl:operation"    => array("wsdl:input", "wsdl:output", "soap:operation"),
        "wsdl:binding"      => array("soap:binding", "wsdl:operation"),
        "wsdl:service"      => array("wsdl:port"),
        "wsdl:port"         => array("soap:address")
    );

    protected static $_subElementTypes = array(
        "wsdl:definitions"      => self::ELEMENT_TYPE_UNIQUE,
        "wsdl:types"            => self::ELEMENT_TYPE_UNIQUE,
        "wsdl:portType"         => self::ELEMENT_TYPE_ARRAY,
        "wsdl:operation"        => self::ELEMENT_TYPE_ARRAY,
        "wsdl:message"          => self::ELEMENT_TYPE_ARRAY,
        "wsdl:service"          => self::ELEMENT_TYPE_ARRAY,
        "wsdl:documentation"    => self::ELEMENT_TYPE_UNIQUE,
        "xsd:schema"            => self::ELEMENT_TYPE_UNIQUE,
        "xsd:all"               => self::ELEMENT_TYPE_UNIQUE,
        "xsd:sequence"          => self::ELEMENT_TYPE_UNIQUE,
        "xsd:complexContent"    => self::ELEMENT_TYPE_UNIQUE,
        "wsdl:part"             => self::ELEMENT_TYPE_ARRAY,
        "wsdl:input"            => self::ELEMENT_TYPE_UNIQUE,
        "wsdl:output"           => self::ELEMENT_TYPE_UNIQUE,
        "soap:operation"        => self::ELEMENT_TYPE_UNIQUE,
        "wsdl:binding"          => self::ELEMENT_TYPE_ARRAY,
        "soap:binding"          => self::ELEMENT_TYPE_UNIQUE,
        "wsdl:port"             => self::ELEMENT_TYPE_UNIQUE,
        "soap:address"          => self::ELEMENT_TYPE_UNIQUE,
    );
    
    /**
     * Construct Wsdl Parser from a WSDL definition.
     *
     * @param  Zend_Soap_Wsdl|DOMDocument|SimpleXMLElement|string $wsdl
     * @return Zend_Soap_Wsdl_Parser
     */
    public static function import($wsdl)
    {
        if($wsdl instanceof DOMDocument) {
            $wsdl = $wsdl->saveXML();
        } else if($wsdl instanceof SimpleXMLElement) {
            $wsdl = $wsdl->asXml();
        } else if($wsdl instanceof Zend_Soap_Wsdl) {
            $wsdl = $wsdl->toXml();
        }

        if(!is_string($wsdl)) {
            /**
             * @see Zend_Soap_Parser_Exception
             */
            require_once "Zend/Soap/Wsdl/Parser/Exception.php";
            throw new Zend_Soap_Wsdl_Parser_Exception(
                "Cannot read WSDL input file. Has to be of type ".
                "String, DomDocument, SimpleXML or Zend_Soap_Wsdl."
            );
        } else {
            return new Zend_Soap_Wsdl_Parser($wsdl);
        }
    }

    /**
     * Construct a WSDL Parser
     *
     * @param string|Zend_Uri $wsdl
     */
    public function __construct($wsdl)
    {
        $this->setWsdl($wsdl);
    }

    /**
     * Set a WSDL file for this parser
     *
     * @param  string|Zend_Uri $wsdl
     * @return Zend_Soap_Wsdl_Parser
     */
    public function setWsdl($wsdl)
    {
        $xmlReader = new XMLReader();
        if($wsdl instanceof Zend_Uri) {
            $xmlReader->open($wsdl->getUri());
        } else if(strpos($wsdl, "<") === false) {
            if(file_exists($wsdl)) {
                $xmlReader->open($wsdl);
            } else {
                /**
                 * @see Zend_Uri
                 */
                require_once "Zend/Uri.php";
                if(Zend_Uri::check($wsdl) == false) {
                    /**
                     * @see Zend_Soap_Wsdl_Parser_Exception
                     */
                    require_once "Zend/Soap/Wsdl/Parser/Exception.php";
                    throw new Zend_Soap_Wsdl_Parser_Exception(
                        "Invalid WSDL uri or file given."
                    );
                }
                $xmlReader->open($wsdl);
            }
        } else {
            $xmlReader->XML($wsdl);
        }
        $this->_normalizedNamespaces = array(
            "wsdl"      => "wsdl",
            "default"   => "wsdl",
            "soap"      => "soap",
            "xsd"       => "xsd",
            "xmlns"     => "xmlns",
        );
        $this->_wsdlXmlReader = $xmlReader;
        return $this;
    }

    /**
     * Normalize uri so detection of namespaces can be done correctly.
     *
     * @param  string $uri
     * @return string
     */
    protected function addTrailingSlashIfNotExists($uri)
    {
        if(substr($uri, strlen($uri)-1) !== "/") {
            return $uri."/";
        }
        return $uri;
    }

    /**
     * Get a namespace prefix by uri.
     *
     * @param  string $uri
     * @return string
     */
    protected function getNamespaceByUri($uri)
    {
        $uri = $this->addTrailingSlashIfNotExists($uri);
        if(isset($this->_uri2namespace[$uri])) {
            return $this->_uri2namespace[$uri];
        } else {
            /**
             * @see Zend_Soap_Wsdl_Parser_Exception
             */
            require_once "Zend/Soap/Wsdl/Parser/Exception.php";
            throw new Zend_Soap_Wsdl_Parser_Exception(
                "Could not find namespace name by uri: ".$uri
            );
        }
    }

    /**
     * Normalize a namespace, by detecting its uri and checking in dictionary.
     * 
     * @param  string $namespace
     * @return string
     */
    protected function getNormalizedNamespace($namespace)
    {
        if(!isset($this->_normalizedNamespaces[$namespace])) {
            $uri = $this->_wsdlXmlReader->namespaceURI;
            $normalizedNamespace = $this->getNamespaceByUri($uri);
            $this->_normalizedNamespaces[$namespace] = $normalizedNamespace;
        }
        return $this->_normalizedNamespaces[$namespace];
    }

    /**
     * Parse WSDL file into a generic WSDL document object graph.
     *
     * @return Zend_Soap_Wsdl_Document
     */
    public function parse()
    {
        $wsdlArray = $this->parseXml($this->_wsdlXmlReader);
        $wsdlArray = $wsdlArray[0];

        $this->_wsdlXmlReader->close();

        $wsdlName = (isset($wsdlArray['attributes']['wsdl:name'])?$wsdlArray['attributes']['wsdl:name']:null);
        $doc      = $this->extractDocumentation($wsdlArray);
        $version  = Zend_Soap_Wsdl_Document::WSDL_11;

        $namespaces = $wsdlArray['attributes'];

        $types      = $this->extractTypes($wsdlArray['wsdl:types']);
        $messages   = $this->extractMessages($wsdlArray['wsdl:message'], $types);
        $operations = $this->extractOperations($wsdlArray['wsdl:portType'], $messages);
        $ports      = $this->extractPorts($wsdlArray['wsdl:portType'], $operations);
        $bindings   = $this->extractBindings($wsdlArray['wsdl:binding'], $operations);
        $services   = $this->extractServices($wsdlArray['wsdl:service'], $ports, $bindings);

        unset($namespaces['name']);

        return new Zend_Soap_Wsdl_Document(
            $wsdlName,
            $version,
            $operations,
            $ports,
            $bindings,
            $services,
            $types,
            $doc,
            $namespaces
        );
    }

    /**
     * Return the type of a node based on the ELEMENT_* class constants.
     *
     * @param  string $name
     * @return int
     */
    protected function getNodeType($name)
    {
        if(isset(self::$_subElementTypes[$name])) {
            return self::$_subElementTypes[$name];
        } else {
            return self::ELEMENT_TYPE_ARRAY;
        }
    }

    /**
     * Recursive method that parses the WSDL file into an Array by iterating over the XMLReader.
     *
     * The most important thing this parser has to do is normalize the nodes correctly according
     * to their namespaces, such that the second run of the parser to categorize the results
     * can split the results according to XSD, WSDL and SOAP namespaces and detect them correctly.
     *
     * Still its also simplifying the overhead XML, because we know where tags only occour once
     * and a subarray can be omitted.
     *
     * @param  XMLReader $xml
     * @return string|array
     */
    protected function parseXml(XMLReader $xml)
    {
        $ret = null;
        while($xml->read()) {
            switch ($xml->nodeType) {
                case XMLReader::ELEMENT:
                    $ns = $this->getNormalizedNamespace($xml->prefix);
                    $node = array(
                        'name'       => $ns.":".$xml->localName,
                        'namespace'  => $ns,
                        'value'      => null,
                        'attributes' => array('name' => null),
                    );

                    if(isset(self::$_requiredNodeSubElements[$node['name']])) {
                        foreach(self::$_requiredNodeSubElements[$node['name']] AS $sub) {
                            $type = $this->getNodeType($sub);
                            switch($type) {
                                case self::ELEMENT_TYPE_ARRAY:
                                    $node[$sub] = array();
                                    break;
                                case self::ELEMENT_TYPE_UNIQUE:
                                    $node[$sub] = null;
                                    break;
                            }
                        }
                    }
                    
                    $value = ($xml->isEmptyElement==true) ? '' : $this->parseXml($xml);
                    $children = array();
                    if(is_array($value)) {
                        $children = $value;
                        $value = '';
                    }
                    $node['value'] = $value;
                    
                    foreach($children AS $child) {
                        $name = $child['name'];
                        $type = $this->getNodeType($name);
                        switch($type) {
                            case self::ELEMENT_TYPE_ARRAY:
                                $node[$name][] = $child;
                                break;
                            case self::ELEMENT_TYPE_UNIQUE:
                                $node[$name] = $child;
                                break;
                        }
                    }

                    if($xml->hasAttributes) {
                        while($xml->moveToNextAttribute()) {
                            if($xml->prefix == "") {
                                $ns = $node['namespace'];
                            } else {
                                $ns = $this->getNormalizedNamespace($xml->prefix);
                            }
                            $attributeName = $ns.":".$xml->localName;
                            $node['attributes'][$attributeName] = $xml->value;
                        }
                    }
                    $ret[] = $node;
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $ret = $xml->value;
                    break;
                case XMLReader::END_ELEMENT:
                    return $ret;
                default:
                    break;
            }
        }
        return $ret;
    }

    /**
     * Extract Types from WSDL
     *
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    protected function extractTypes($typeData)
    {
        $types = new Zend_Soap_Wsdl_Element_Collection("Type");

        if(isset($typeData['xsd:schema']) && count($typeData['xsd:schema'])) {
            if(isset($typeData['xsd:schema']['xsd:complexType'])) {
                foreach($typeData['xsd:schema']['xsd:complexType'] AS $type) {
                    $xsdName = $type['attributes']['xsd:name'];
                    $documentation = $this->extractDocumentation($type);
                    if(isset($type['xsd:all']) && count($type['xsd:all'])) {
                        $subTypeName = "xsd:all";
                    } else if(isset($type['xsd:complexContent']) && count($type['xsd:complexContent'])) {
                        $subTypeName = "xsd:complexContent";
                    } else if(isset($type['xsd:sequence']) && count($type['xsd:sequence'])) {
                        $subTypeName = "xsd:sequence";
                    }
                    $subTypes = $this->extractSubTypes($type[$subTypeName]);
                    $type = new Zend_Soap_Wsdl_Element_Type($xsdName, $subTypeName, $subTypes, $documentation);

                    $types->addElement($type);
                }
            }
        }

        return $types;
    }

    protected function extractSubTypes($subTypeList)
    {
        $types = array();
        if(isset($subTypeList['xsd:element'])) {
            foreach($subTypeList['xsd:element'] AS $subType) {
                $xsdSubTypeName = $subType['attributes']['xsd:name'];
                unset($subType['attributes']['name']);
                $types[$xsdSubTypeName] = $subType['attributes'];
            }
        }
        return $types;
    }

    /**
     * Extract Messages from WSDL
     *
     * @param  DOMDocument $wsdl
     * @param  DOMXPath $xpath
     * @param  Zend_Soap_Wsdl_Element_Collection $types
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    protected function extractMessages($messageData, Zend_Soap_Wsdl_Element_Collection $types)
    {
        $messages = new Zend_Soap_Wsdl_Element_Collection("Message");

        foreach($messageData AS $msg) {
            $messageName = $msg['attributes']['wsdl:name'];
            $documentation = $this->extractDocumentation($msg);

            $parts = new Zend_Soap_Wsdl_Element_Collection("Type");
            foreach($msg['wsdl:part'] AS $part) {
                $partName = $part['attributes']['wsdl:name'];
                $partType = $part['attributes']['wsdl:type'];
                $part = new Zend_Soap_Wsdl_Element_Part($partName, $partType);
            }
            $messages->addElement(new Zend_Soap_Wsdl_Element_Message($messageName, $parts, $documentation));
        }

        return $messages;
    }

    /**
     * Extract Operations from WSDL
     *
     * @param  DOMDocument $wsdl
     * @param  DOMXPath $xpath
     * @param  Zend_Soap_Wsdl_Element_Collection $messages
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    protected function extractOperations($portTypeData, Zend_Soap_Wsdl_Element_Collection $messages)
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("Operation");

        foreach($portTypeData AS $port) {
            foreach($port['wsdl:operation'] AS $operation) {
                $operationName = $operation['attributes']['wsdl:name'];
                $documentation = $this->extractDocumentation($operation);

                // TODO: Parse and save soap:body and soap:header information.
                $inputMessage = null;
                if(is_array($operation['wsdl:input'])) {
                    $message      = explode(":", $operation['wsdl:input']['attributes']['wsdl:message']);
                    $messageName  = array_pop($message);
                    $inputMessage = $messages->getElement($messageName);
                }
                $outputMessage = null;
                if(is_array($operation['wsdl:output'])) {
                    $message       = explode(":", $operation['wsdl:output']['attributes']['wsdl:message']);
                    $messageName   = array_pop($message);
                    $outputMessage = $messages->getElement($messageName);
                }
                $operations->addElement(new Zend_Soap_Wsdl_Element_Operation($operationName, $documentation, $inputMessage, $outputMessage));
            }
        }

        return $operations;
    }

    /**
     * Extract Operations from WSDL
     *
     * @param  DOMDocument $wsdl
     * @param  DOMXPath $xpath
     * @param  Zend_Soap_Wsdl_Element_Collection $operations
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    protected function extractBindings($bindingData, Zend_Soap_Wsdl_Element_Collection $operations)
    {
        $bindings = new Zend_Soap_Wsdl_Element_Collection("Binding");

        foreach($bindingData AS $binding) {
            $bindingName = $binding['attributes']['wsdl:name'];
            $portName    = $binding['attributes']['wsdl:type'];
            $documentation = $this->extractDocumentation($binding);

            $bindingOperations = new Zend_Soap_Wsdl_Element_Collection("Operation");
            foreach($binding['wsdl:operation'] AS $bindingOperation) {
                $operationName = $bindingOperation['attributes']['wsdl:name'];
                $operation = $operations->getElement($operationName);
                $bindingOperations->addElement($operation);
            }

            $bindings->addElement(
                new Zend_Soap_Wsdl_Element_Binding($bindingName, $portName, $bindingOperations, $documentation)
            );
        }

        return $bindings;
    }

    /**
     * Extract Ports from WSDL
     *
     * @param  DOMDocument $wsdl
     * @param  DOMXPath $xpath
     * @param  Zend_Soap_Wsdl_Element_Collection $operations
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    protected function extractPorts($portData, Zend_Soap_Wsdl_Element_Collection $operations)
    {
        $ports = new Zend_Soap_Wsdl_Element_Collection("Port");

        foreach($portData AS $port) {
            $portName = $port['attributes']['wsdl:name'];
            $documentation = $this->extractDocumentation($port);

            $portOperations = new Zend_Soap_Wsdl_Element_Collection("Operation");
            foreach($port['wsdl:operation'] AS $portOperation) {
                $operationName = $portOperation['attributes']['wsdl:name'];
                $operation = $operations->getElement($operationName);
                $portOperations->addElement($operation);
            }

            $ports->addElement(new Zend_Soap_Wsdl_Element_Port($portName, $portOperations, $documentation));
        }

        return $ports;
    }

    /**
     * Extract Services from WSDL
     *
     * @param  DOMDocument $wsdl
     * @param  DOMXPath $xpath
     * @param  Zend_Soap_Wsdl_Element_Collection $ports
     * @param  Zend_Soap_Wsdl_Element_Collection $bindings
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    protected function extractServices($serviceData, Zend_Soap_Wsdl_Element_Collection $ports, Zend_Soap_Wsdl_Element_Collection $bindings)
    {
        $services = new Zend_Soap_Wsdl_Element_Collection("Service");

        foreach($serviceData AS $service) {
            $serviceName = $service['attributes']['wsdl:name'];
            $documentation = $this->extractDocumentation($service);

            $port = $service['wsdl:port'];
            $binding = null;
            $portType = null;
            $serviceLocation = null;
            if(is_array($port['soap:address']) && isset($port['soap:address']['attributes']['soap:location'])) {
                $serviceLocation = $port['soap:address']['attributes']['soap:location'];
            }
            if($port !== null) {
                $portName = $port['attributes']['wsdl:name'];
                $portType = $ports->getElement($portName);

                if(isset($port['attributes']['wsdl:binding'])) {
                    $bindingName = explode(":", $port['attributes']['wsdl:binding']);
                    $bindingName = array_pop($bindingName);
                    $binding     = $bindings->getElement($bindingName);
                }
            }

            $services->addElement(
                new Zend_Soap_Wsdl_Element_Service(
                    $serviceName,
                    $serviceLocation,
                    $portType,
                    $binding,
                    $documentation
                )
            );
        }
        return $services;
    }

    /**
     * Extract documentation from a node
     * 
     * @param  DOMNode $node
     * @return string
     */
    protected function extractDocumentation($node)
    {
        if(isset($node['wsdl:documentation'])) {
            return $node['wsdl:documentation']['value'];
        }
        return "";
    }
}