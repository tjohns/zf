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

require_once 'Zend/Server/Exception.php';

class Zend_Soap_Wsdl {
    /**
     * @var object DomDocument Instance
     */

    private $dom;

    /**
     * @var object WSDL Root XML_Tree_Node
     */

    private $wsdl;
    
    /**
     * @var array Class information
     */
     
    public $class;

    /**
     * Constructor
     *
     * @param string $name Name of the Web Service being Described
     * @param string $uri URI where the WSDL will be available
     */

    public function __construct($name, $uri)
    {
    	if ($uri instanceof Zend_Uri_Http) {
    		$uri = $uri->getUri();
    	}
        $wsdl = "<?xml version='1.0' ?>
                <definitions name='$name' targetNamespace='$uri'
                    xmlns='http://schemas.xmlsoap.org/wsdl/'
                    xmlns:tns='$uri'
                    xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/'
                    xmlns:xsd='http://www.w3.org/2001/XMLSchema'
                    xmlns:soap-enc='http://schemas.xmlsoap.org/soap/encoding/'></definitions>";
        if (!$this->dom = DomDocument::loadXML($wsdl)) {
            throw new Zend_Server_Exception('Unable to create DomDocument');
        } else {
            $this->wsdl = $this->dom->documentElement;
        }
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_messages message} element to the WSDL
     *
     * @param string $name Name for the {@link http://www.w3.org/TR/wsdl#_messages message}
     * @param array An array of {@link http://www.w3.org/TR/wsdl#_message parts}
     * The array is constructed like: 'name of part' => 'part xml schema data type'
     * @return object The new message's XML_Tree_Node for use in {@link function addDocumentation}
     */

    public function &addMessage($name, $parts)
    {
        $message = $this->dom->createElement('message');
        
        $message->setAttribute('name', $name);
        
        if (sizeof($parts) > 0) {
	        foreach ($parts as $name => $type) {
    	        $part = $this->dom->createElement('part');
        	    $part->setAttribute('name', $name);
            	$part->setAttribute('type', $type);
            	$message->appendChild($part);
        	}
        }
        
        $this->wsdl->appendChild($message);
        
        return $message;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_porttypes portType} element to the WSDL
     *
     * @param string $name portType element's name
     * @return object The new portType's XML_Tree_Node for use in {@link function addPortOperation} and {@link function addDocumentation}
     */

    public function &addPortType($name)
    {
        $portType = $this->dom->createElement('portType');
        $portType->setAttribute('name', $name);
        $this->wsdl->appendChild($portType);
        
        return $portType;
    }

    /**
     * Add an {@link http://www.w3.org/TR/wsdl#_request-response operation} element to a portType element
     *
     * @param object &$portType a portType XML_Tree_Node, from {@link function addPortType}
     * @param string $name Operation name
     * @param string $input Input Message
     * @param string $output Output Message
     * @param string $fault Fault Message
     * @return object The new operation's XML_Tree_Node for use in {@link function addDocumentation}
     */

    public function &addPortOperation(&$portType, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->dom->createElement('operation');
        $operation->setAttribute('name', $name);
        
        if (is_string($input) && (strlen(trim($input)) >= 1)) {
            $node = $this->dom->createElement('input');
            $node->setAttribute('message', $input);
            $operation->appendChild($node);
        }
        if (is_string($output) && (strlen(trim($output)) >= 1)) {
            $node= $this->dom->createElement('output');
            $node->setAttribute('message', $output);
            $operation->appendChild($node);
        }
        if (is_string($fault) && (strlen(trim($fault)) >= 1)) {
            $node = $this->dom->createElement('fault');
            $node->setAttribute('message', $fault);
            $operation->appendChild($node);
        }
        
        $portType->appendChild($operation);
        
        return $operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_bindings binding} element to WSDL
     *
     * @param string $name Name of the Binding
     * @param string $type name of the portType to bind
     * @return object The new binding's XML_Tree_Node for use with {@link function addBindingOperation} and {@link function addDocumentation}
     */

    public function &addBinding($name, $portType)
    {
        $binding = $this->dom->createElement('binding');
        $binding->setAttribute('name', $name);
        $binding->setAttribute('type', $portType);
        
        $this->wsdl->appendChild($binding);
        
        return $binding;
    }

    /**
     * Add an operation to a binding element
     *
     * @param object &$binding A binding XML_Tree_Node returned by {@link function addBinding}
     * @param array $input An array of attributes for the input element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array $output An array of attributes for the output element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array $fault An array of attributes for the fault element, allowed keys are: 'name', 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @return object The new Operation's XML_Tree_Node for use with {@link function addSoapOperation} and {@link function addDocumentation}
     */

    public function &addBindingOperation(&$binding, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->dom->createElement('operation');
        $operation->setAttribute('name', $name);

        if (is_array($input)) {
            $node = $this->dom->createElement('input');
            $soap_node = $this->dom->createElement('soap:body');
            foreach ($input as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
            $operation->appendChild($node);
        }

        if (is_array($output)) {
            $node = $this->dom->createElement('output');
            $soap_node = $this->dom->createElement('soap:body');
            foreach ($output as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
            $operation->appendChild($node);
        }

        if (is_array($fault)) {
            $node = $this->dom->createElement('fault');
            if (isset($fault['name'])) {
                $node->setAttribute('name', $fault['name']);
            }
            $soap_node = $this->dom->createElement('soap:body');
            foreach ($output as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
            $operation->appendChild($node);
        }
        
        $binding->appendChild($operation);

        return $operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:binding SOAP binding} element to a Binding element
     *
     * @param object &$binding A binding XML_Tree_Node returned by {@link function addBinding}
     * @param string $style binding style, possible values are "rpc" (the default) and "document"
     * @param string $transport Transport method (defaults to HTTP)
     * @return boolean
     */

    public function addSoapBinding(&$binding, $style = 'document', $transport = 'http://schemas.xmlsoap.org/soap/http')
    {
        $soap_binding = $this->dom->createElement('soap:binding');
        $soap_binding->setAttribute('style', $style);
        $soap_binding->setAttribute('transport', $transport);
        
        $binding->appendChild($soap_binding);
        
        return $soap_binding;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:operation SOAP operation} to an operation element
     *
     * @param object &$operation An operation XML_Tree_Node returned by {@link function addBindingOperation}
     * @param string $soap_action SOAP Action
     * @return boolean
     */

    public function addSoapOperation(&$binding, $soap_action)
    {
    	if ($soap_action instanceof Zend_Uri_Http) {
    		$soap_action = $soap_action->getUri();
    	}
        $soap_operation = $this->dom->createElement('soap:operation');
        $soap_operation->setAttribute('soapAction', $soap_action);
        
        $binding->insertBefore($soap_operation, $binding->firstChild);
        
        return $soap_operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_services service} element to the WSDL
     *
     * @param string $name Service Name
     * @param string $port_name Name of the port for the service
     * @param string $binding Binding for the port
     * @param string $location SOAP Address for the service
     * @return object The new service's XML_Tree_Node for use with {@link function addDocumentation}
     */

    public function &addService($name, $port_name, $binding, $location)
    {
    	if ($location instanceof Zend_Uri_Http) {
    		$location = $location->getUri();
    	}
        $service = $this->dom->createElement('service');
        $service->setAttribute('name', $name);
        
        $port = $this->dom->createElement('port');
        $port->setAttribute('name', $port_name);
        $port->setAttribute('binding', $binding);
        
        $soap_address = $this->dom->createElement('soap:address');
        $soap_address->setAttribute('location', $location);
        
        $port->appendChild($soap_address);
        $service->appendChild($port);
        
        $this->wsdl->appendChild($service);
        
        return $service;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_documentation document} element to any element in the WSDL
     *
     * @param object $input_node An XML_Tree_Node returned by another method to add the document to
     * @param string $document Human readable documentation for the node
     * @return boolean
     */

    public function addDocumentation($input_node, $documenation)
    {
        if ($input_node === $this) {
            $node = $this->dom->documentElement;
        } else {
            $node = $input_node;
        }
        $doc = $this->dom->createElement('documentation');
        $doc_cdata = $this->dom->createTextNode($documenation);
        $doc->appendChild($doc_cdata);
        $node->appendChild($doc);

        return $doc;
    }

    /**
     * Add WSDL Types element
     *
     * @param object $types A DomDocument|DomNode|DomElement|DomDocumentFragment with all the XML Schema types defined in it
     */

    public function addTypes($types)
    {
        if ($types instanceof DomDocument) {
            $dom = $this->wsdl->importNode($types->documentElement);
            $this->wsdl->appendChild($types->documentElement);
        } elseif ($types instanceof DomNode || $types instanceof DomElement || $types instanceof DomDocumentFragment ) {
            $dom = $this->wsdl->importNode($types);
            $this->wsdl->appendChild($dom);
        }
    }
    
    /**
     * Return the WSDL as XML
     *
     * @return string WSDL as XML
     */

    public function toXML()
    {
   		return $this->dom->saveXML();
    }
    
    /**
     * Return DOM Document
     *
     * @return object DomDocum ent
     */
     
    public function toDomDocument()
    {
        return $this->dom;
    }

    /**
     * Echo the WSDL as XML
     *
     * @return boolean
     */

    public function dump($filename = false)
    {
        if (!$filename) {
            echo $this->toXML();
            return true;
        } else {
            return file_put_contents($filename, $this->toXML());
        }
    }
}

?>
