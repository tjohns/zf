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
 * @version    $Id$
 */

/**
 * @see Zend_Soap_Wsdl_Element_Interface
 */
require_once "Zend/Soap/Wsdl/Element/Interface.php";

/**
 * Zend_Soap_Wsdl_Element_Binding
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Wsdl
 */
class Zend_Soap_Wsdl_Element_Binding implements Zend_Soap_Wsdl_Element_Interface
{
    /**
     * Element name
     *
     * @var string
     */
    protected $_name;

    /**
     * Element documentation
     *
     * @var string
     */
    protected $_documentation;

    public $_portName;

    public $_operations;

    public function __construct($name, $portName, Zend_Soap_Wsdl_Element_Collection $operations, $documentation)
    {
        if(!is_string($name)) {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception("Binding Element has to given a string as name.");
        }
        $this->_name          = $name;
        $this->_portName       = $portName;
        $this->_operations     = $operations;
        $this->_documentation = $documentation;
    }

    /**
     * Return unique name of this element
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Return documentation of this element if present.
     *
     * @return string
     */
    public function getDocumentation()
    {
        return $this->_documentation;
    }

    /**
     * Get Name of the Port that is responsible for this binding.
     *
     * @return string
     */
    public function getPortName()
    {
        return $this->_portName;
    }

    /**
     * Get Operatios that belong into this port.
     *
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    public function getOperations()
    {
        return $this->_operations;
    }
}