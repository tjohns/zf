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

/**
 * Zend_Soap_Wsdl_Element_Collection
 *
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_Wsdl_Element_Collection implements Iterator, Countable
{
    /**
     * Base Type of the Collection
     *
     * @var string
     */
    protected $_type;

    /**
     * Elements of the Collection
     *
     * @var Zend_Soap_Wsdl_Interface[]
     */
    protected $_elements = array();

    /**
     * Build new WSDL element collection
     *
     * @param string $type
     */
    public function __construct($type)
    {
        $this->setType($type);
    }

    /**
     * Set Type of WSDL Collection
     *
     * @throws Zend_Soap_Wsdl_Exception
     * @param string $type
     */
    protected function setType($type)
    {
        if(!is_string($type)) {
            /**
             * @see Zend_Soap_Wsdl_Exception
             */
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception(
                "Wsdl Element Collection has to be initialized with string name of the element type."
            );
        }
        switch($type) {
            case 'Type':
            case 'Service':
            case 'Port':
            case 'Binding':
            case 'Operation':
            case 'Message':
                $this->_type = sprintf('Zend_Soap_Wsdl_Element_%s', $type);
                break;
            default:
                $this->_type = $type;
                break;
        }
    }

    /**
     * Get Type of collection.
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Add new element to the collection
     *
     * @param  Zend_Soap_Wsdl_Element_Interface $element
     * @throws Zend_Soap_Wsdl_Exception
     * @return void
     */
    public function addElement(Zend_Soap_Wsdl_Element_Interface $element)
    {
        $name = $element->getName();
        if(!is_string($name)) {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception("Element name has to be a string, '".get_class($name)."' given!");
        }

        if(isset($this->_elements[$name])) {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception(
                sprintf("Adding duplicate elements (name '%s' in type '%s') is not allowed in WSDL documents",
                    $name, $this->getType()
                )
            );
        }

        if($element instanceof $this->_type) {
            $this->_elements[$name] = $element;
        } else {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception(
                sprintf("Element in Collection has to be of type '%s', but '%s' given!",
                    $this->_type, get_class($element)
                )
            );
        }
    }

    /**
     * Return element by given identifying name.
     * 
     * @throws Zend_Soap_Wsdl_Exception
     * @param  string $name
     * @return Zend_Soap_Wsdl_Element_Interface
     */
    public function getElement($name)
    {
        if(!isset($this->_elements[$name])) {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception(
                sprintf("No element of name '%s' in Wsdl Element Collection of type '%s'. Known elements are: %s",
                    $name, $this->_type, implode(", ", $this->getElementNames())
                )
            );
        }
        return $this->_elements[$name];
    }

    /**
     * Return names of all contained elements.
     * 
     * @return string[]
     */
    public function getElementNames()
    {
        return array_keys($this->_elements);
    }

    /**
     * Get number of elements in Collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->_elements);
    }

    /**
     * Iterator Interface: Rewind
     * 
     * @return void 
     */
    public function rewind()
    {
        reset($this->_elements);
    }

    /**
     * Iterator Interface: Current
     *
     * @return Zend_Soap_Wsdl_Element_Interface
     */
    public function current()
    {
        return current($this->_elements);
    }

    /**
     * Iterator Interface: key
     *
     * @return string
     */
    public function key()
    {
        return key($this->_elements);
    }

    /**
     * Iterator Interface: next
     *
     * @return Zend_Soap_Wsdl_Element_Interface
     */
    public function next()
    {
        return next($this->_elements);
    }

    /**
     * Iterator Interface: valid
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->current() !== false);
    }
}