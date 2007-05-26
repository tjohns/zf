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
 * @package    Zend_Gdata_Calendar
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * Represents the gCal:selected element used by the Calendar data API
 *
 * @category   Zend
 * @package    Zend_Gdata_Calendar
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_Extension_Selected extends Zend_Gdata_Extension
{

    protected $_rootNamespace = 'gCal';
    protected $_rootElement = 'gCal:selected';
    protected $_value = null;

    /**
     * Constructs a new Zend_Gdata_Calendar_Extension_Selected object.
     * @param bool $value (optional) The value of the element.
     */
    public function __construct($value = null) 
    {
        parent::__construct();
        foreach (Zend_Gdata_Calendar::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        $this->_value = $value; 
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
        $element = parent::getDOM($doc);
        $element->setAttribute('value', $this->_value);
        return $element;
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
        switch ($attribute->localName) {
        case 'value':
            $this->_value = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

	/**
	 * Get the value for this element's value attribute.
	 *
	 * @return bool The value associated with this attribute.
	 */
	public function getSelected()
	{
		return $this->_value;
	}

	/**
	 * Set the value for this element's value attribute.
	 *
	 * @param bool $value The desired value for this attribute.
     * @return Zend_GData_Calendar_Extension_Selected The element being modified.
	 */
	public function setSelected($value)
	{
		$this->_value = $value;
		return $this;
	}

	/**
     * Retrieves a human redable string describing this attribute's value.
	 *
	 * @return string The attribute value.
     */
    public function __toString() 
    {
        return $this->_value;
    }

}
