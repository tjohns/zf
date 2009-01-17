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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Soap/Wsdl/Parser/Result.php';
require_once "Zend/Soap/Wsdl/Element/Collection.php";
require_once 'Zend/Soap/Wsdl/Element/Binding.php';
require_once 'Zend/Soap/Wsdl/Element/Message.php';
require_once 'Zend/Soap/Wsdl/Element/Operation.php';
require_once 'Zend/Soap/Wsdl/Element/Port.php';
require_once 'Zend/Soap/Wsdl/Element/Service.php';
require_once 'Zend/Soap/Wsdl/Element/Type.php';

/**
 * Zend_Soap_Wsdl_Parser
 *
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_Wsdl_Parser
{
    /**
     * Version numbers of WSDL document
     */
    const WSDL_11 = "1.1";
    const WSDL_20 = "2.0";

    /**
     * DOMDocument instance that represents WSDL File
     *
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * Construct Wsdl Parser from a WSDL definition.
     *
     * @param  Zend_Soap_Wsdl|DOMDocument|SimpleXMLElement|string $wsdl
     * @param  string $class
     * @return Zend_Soap_Wsdl_Parser
     */
    public static function factory($wsdl, $class="Zend_Soap_Wsdl_Parser")
    {
        $dom = null;
        if(is_string($wsdl)) {
            $dom = new DOMDocument();
            $dom->loadXml($wsdl);
        } else if($wsdl instanceof DOMDocument) {
            $dom = $wsdl;
        } else if($wsdl instanceof SimpleXMLElement) {
            $dom = new DOMDocument();
            $dom->loadXml($wsdl->asXml());
        } else if($wsdl instanceof Zend_Soap_Wsdl) {
            $dom = $wsdl->toDomDocument();
        }

        if($dom === null) {
            /**
             * @see Zend_Soap_Parser_Exception
             */
            require_once "Zend/Soap/Wsdl/Parser/Exception.php";
            throw new Zend_Soap_Wsdl_Parser_Exception(
                "Cannot read WSDL input file. Has to be of type ".
                "String, DomDocument, SimpleXML or Zend_Soap_Wsdl."
            );
        } else {
            $parser = new $class($dom);
            if( !($parser instanceof Zend_Soap_Wsdl_Parser) ) {
                /**
                 * @see Zend_Soap_Parser_Exception
                 */
                require_once "Zend/Soap/Wsdl/Parser/Exception.php";
                throw new Zend_Soap_Wsdl_Parser_Exception(
                    "Can only create parsers of type 'Zend_Soap_Wsdl_Parser'."
                );
            }
            return $parser;
        }
    }

    /**
     * Construct a WSDL Parser
     *
     * @param DOMDocument $wsdl
     */
    public function __construct(DOMDocument $wsdl)
    {
        $this->setDomDocumentContainingWsdl($wsdl);
    }

    /**
     * Set the WSDL in DOMDocument representation to be parsed.
     *
     * @param  DOMDocument $wsdl
     * @return Zend_Soap_Wsdl_Parser
     */
    public function setDomDocumentContainingWsdl(DOMDocument $wsdl)
    {
        $this->_dom = $wsdl;
        return $this;
    }

    /**
     * Parse a WSDL document into a generic object
     *
     * @return Zend_Soap_Wsdl_Parser_Result The contents of the WSDL file
     */
    public function parse()
    {
        $wsdl    = $this->_dom;
        $version = Zend_Soap_Wsdl_Parser::WSDL_11;

        $xpath = new DOMXPath($wsdl);
        $xpath->registerNamespace("zfwsdl", "http://schemas.xmlsoap.org/wsdl/");

        $name       = $wsdl->documentElement->getAttribute('name');
        $doc        = $this->extractDocumentation($wsdl->documentElement);
        $types      = $this->extractTypes($wsdl, $xpath);
        $messages   = $this->extractMessages($wsdl, $xpath, $types);
        $operations = $this->extractOperations($wsdl, $xpath, $messages);
        $ports      = $this->extractPorts($wsdl, $xpath, $operations);
        $bindings   = $this->extractBindings($wsdl, $xpath, $operations);
        $services   = $this->extractServices($wsdl, $xpath, $ports, $bindings);

        return new Zend_Soap_Wsdl_Parser_Result(
            $name,
            $version,
            $operations,
            $ports,
            $bindings,
            $services,
            $types,
            $doc
        );
    }

    /**
     * Extract Types from WSDL
     *
     * @param  DOMDocument $wsdl
     * @param  DOMXPath $xpath
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    protected function extractTypes(DOMDocument $wsdl, DOMXPath $xpath)
    {
        $types = new Zend_Soap_Wsdl_Element_Collection("Type");
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
    protected function extractMessages(DOMDocument $wsdl, DOMXPath $xpath, Zend_Soap_Wsdl_Element_Collection $types)
    {
        $messages = new Zend_Soap_Wsdl_Element_Collection("Message");

        $result = $xpath->query("/zfwsdl:definitions[1]/zfwsdl:message");

        foreach($result AS $message) {
            $messageName   = $message->getAttribute('name');
            $documentation = $this->extractDocumentation($message);

            $parts = new Zend_Soap_Wsdl_Element_Collection("Type");
            foreach($message->childNodes AS $part) {
                // TODO: Implement Types of MEssages, think of base XSD Types also!!!
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
    protected function extractOperations(DOMDocument $wsdl, DOMXPath $xpath, Zend_Soap_Wsdl_Element_Collection $messages)
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("Operation");

        $result = $xpath->query("/zfwsdl:definitions[1]/zfwsdl:portType/zfwsdl:operation");

        foreach($result AS $operation) {
            $operationName = $operation->getAttribute('name');
            $documentation = $this->extractDocumentation($operation);
            
            foreach($operation->childNodes AS $operationMessage) {
                $nodeName = $operationMessage->nodeName;
                if(in_array($nodeName, array("input", "output"))) {
                    $message = explode(":", $operationMessage->getAttribute('message'));
                    $messageName = array_pop($message);
                    if($operationMessage->nodeName == "input") {
                        $inputMessage = $messages->getElement($messageName);;
                    } else if($operationMessage->nodeName == "output") {
                        $outputMessage = $messages->getElement($messageName);;
                    }
                }
            }
            $operations->addElement(new Zend_Soap_Wsdl_Element_Operation($operationName, $inputMessage, $outputMessage, $documentation));
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
    protected function extractBindings(DOMDocument $wsdl, DOMXPath $xpath, Zend_Soap_Wsdl_Element_Collection $operations)
    {
        $bindings = new Zend_Soap_Wsdl_Element_Collection("Binding");

        $result = $xpath->query("/zfwsdl:definitions[1]/zfwsdl:binding");

        foreach($result AS $binding) {
            $bindingName   = $binding->getAttribute('name');
            $portName      = $binding->getAttribute('type');
            $documentation = $this->extractDocumentation($binding);

            $bindingOperations = new Zend_Soap_Wsdl_Element_Collection("Operation");
            foreach($binding->childNodes AS $bindingOperation) {
                if($bindingOperation->nodeName == "operation") {
                    $operationName = $bindingOperation->getAttribute('name');
                    $operation = $operations->getElement($operationName);
                    $bindingOperations->addElement($operation);
                }
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
    protected function extractPorts(DOMDocument $wsdl, DOMXPath $xpath, Zend_Soap_Wsdl_Element_Collection $operations)
    {
        $ports = new Zend_Soap_Wsdl_Element_Collection("Port");

        $result = $xpath->query("/zfwsdl:definitions[1]/zfwsdl:portType");

        foreach($result AS $port) {
            $portName      = $port->getAttribute('name');
            $documentation = $this->extractDocumentation($port);

            $portOperations = new Zend_Soap_Wsdl_Element_Collection("Operation");
            foreach($port->childNodes AS $portOperation) {
                if($portOperation->nodeName == "operation") {
                    $operationName = $portOperation->getAttribute('name');
                    $operation = $operations->getElement($operationName);
                    $portOperations->addElement($operation);
                }
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
    protected function extractServices(DOMDocument $wsdl, DOMXPath $xpath, Zend_Soap_Wsdl_Element_Collection $ports, Zend_Soap_Wsdl_Element_Collection $bindings)
    {
        $services = new Zend_Soap_Wsdl_Element_Collection("Service");

        $result = $xpath->query("/zfwsdl:definitions[1]/zfwsdl:service");

        foreach($result AS $service) {
            $serviceName   = $service->getAttribute('name');
            $documentation = $this->extractDocumentation($service);

            $port = null;
            $binding = null;
            $serviceLocation = null;
            foreach($service->childNodes AS $servicePort) {
                if($servicePort->nodeName == "port") {
                    $portName = $servicePort->getAttribute('name');
                    $port     = $ports->getElement($portName);

                    $bindingName = explode(":", $servicePort->getAttribute('binding'));
                    $bindingName = array_pop($bindingName);
                    $binding     = $bindings->getElement($bindingName);

                    foreach($servicePort->childNodes AS $soapLocation) {
                        if($soapLocation->nodeName == "soap:address") {
                            $serviceLocation = $soapLocation->getAttribute('location');
                        }
                    }
                }
            }

            $services->addElement(
                new Zend_Soap_Wsdl_Element_Service(
                    $serviceName,
                    $serviceLocation,
                    $port,
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
    protected function extractDocumentation(DOMNode $node)
    {
        if($node->hasChildNodes() == true) {
            foreach($node->childNodes AS $node) {
                if($node->nodeName == "wsdl:documentation") {
                    return $node->nodeValue;
                }
            }
        }
        return "";
    }
}


