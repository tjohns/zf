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
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_App_InvalidArgumentException
 */
require_once 'Zend/Gdata/App/InvalidArgumentException.php';

/**
 * Abstract class for all XML elements
 *
 * @category   Zend
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Gdata_App_Base
{

    /**
     * @var string The XML element name, including prefix if desired
     */
    protected $_rootElement = null;

    /**
     * @var string The XML namespace prefix
     */
    protected $_rootNamespace = 'atom';

    /**
     * @var string The XML namespace URI - takes precedence over lookup up the
     * corresponding URI for $_rootNamespace
     */
    protected $_rootNamespaceURI = null;

    /**
     * @var array Leftover elements which were not handled
     */
    protected $_extensionElements = array();

    /**
     * @var array Leftover attributes which were not handled
     */
    protected $_extensionAttributes = array();

    /**
     * @var string XML child text node content
     */
    protected $_text = null;

    public function __construct($text = null)
    {
        $this->_text = $text;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setText($value)
    {
        $this->_text = $value;
        return $this;
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all 
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.  
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all 
     * child properties.
     */
    public function getDOM($doc = null)
    {
        if (is_null($doc)) {
            $doc = new DOMDocument('1.0', 'utf-8');
        }
        if ($this->_rootNamespaceURI != null) { 
            $element = $doc->createElementNS($this->_rootNamespaceURI, $this->_rootElement);
        } elseif ($this->_rootNamespace !== null) {
            $element = $doc->createElementNS(Zend_Gdata_Data::lookupNamespace($this->_rootNamespace), $this->_rootElement);
        } else {
            $element = $doc->createElement($this->_rootElement);
        }
        if ($this->_text != null) {
            $element->appendChild($element->ownerDocument->createTextNode($this->_text));
        }
        foreach ($this->_extensionElements as $extensionElement) {
            $element->appendChild($extensionElement->getDOM($element->ownerDocument));
        }
        foreach ($this->_extensionAttributes as $extensionAttribute) {
            $element->setAttribute($extensionAttribute['name'], $extensionAttribute['value']);
        }
        return $element;
    }

    /**
     * Given a child DOMNode, tries to determine how to map the data into
     * object instance members.  If no mapping is defined, Extension_Element
     * objects are created and stored in an array.
     *
     * @param DOMNode $child The DOMNode needed to be handled
     */
    protected function takeChildFromDOM($child)
    {
        if ($child->nodeType == XML_TEXT_NODE) {
            $this->_text = $child->nodeValue;
        } else {
            $extensionElement = new Zend_Gdata_App_Extension_Element();
            $extensionElement->transferFromDOM($child);
            $this->_extensionElements[] = $extensionElement; 
        }
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are 
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        $this->_extensionAttributes[] =
                array(namespaceURI => $attribute->namespaceURI,
                      name => $attribute->localName,
                      value => $attribute->nodeValue);
    }

    /**
     * Transfers each child and attribute into member variables.
     * This is called when XML is received over the wire and the data
     * model needs to be built to represent this XML.
     *
     * @param DOMNode $node The DOMNode that represents this object's data
     */
    public function transferFromDOM($node)
    {
        foreach ($node->childNodes as $child) {
            $this->takeChildFromDOM($child);
        }
        foreach ($node->attributes as $attribute) {
            $this->takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Converts this element and all children into XML using getDOM()
     *
     * @return string XML content
     */
    public function saveXML()
    {
        $element = $this->getDOM();
        return $element->ownerDocument->saveXML($element);
    }

    /**
     * Magic getter to allow acces like $entry->foo to call $entry->getFoo()
     * Alternatively, if no getFoo() is defined, but a $_foo protected variable
     * is defined, this is returned.
     *
     * TODO Remove ability to bypass getFoo() methods??
     *
     * @param string $name The variable name sought
     */
    public function __get($name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method));
        } else if (property_exists($this, "_${name}")) {
            return $this->{'_' . $name};
        } else {
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Property ' . $name . '  does not exist');
        }
    }

    /**
     * Magic setter to allow acces like $entry->foo='bar' to call 
     * $entry->setFoo('bar') automatically.  
     *
     * Alternatively, if no setFoo() is defined, but a $_foo protected variable
     * is defined, this is returned.
     *
     * TODO Remove ability to bypass getFoo() methods??
     *
     * @param string $name 
     * @param string $value
     */
    public function __set($name, $val)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method), $val);
        } else if (isset($this->{'_' . $name}) || is_null($this->{'_' . $name})) {
            $this->{'_' . $name} = $val;
        } else {
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Property ' . $name . '  does not exist');
        }
    }

}
