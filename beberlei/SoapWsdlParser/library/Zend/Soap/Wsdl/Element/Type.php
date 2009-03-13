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
 * Zend_Soap_Wsdl_Element_Type
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Wsdl
 */
class Zend_Soap_Wsdl_Element_Type implements Zend_Soap_Wsdl_Element_Interface
{
    /**
     * name of the complex type
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

    /**
     * What type of aggregation is the sub-type?
     *
     * @var string
     */
    protected $_subTypeSpec;

    /**
     * Associative array of subtypes with the name of the sub
     * 
     * @var array
     */
    protected $_subTypes;

    /**
     * Object representation of a WSDL Complex Type (Using XML Schema).
     *
     * @param string $name
     * @param string $type
     * @param string $subTypeSpec
     * @param array  $subTypes
     * @param string $documentation
     */
    public function __construct($name, $subTypeSpec="xsd:all", $subTypes=array(), $documentation="")
    {
        if(!is_string($name)) {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception("Type Element has to given a string as name.");
        }
        $this->_name          = $name;
        $this->_subTypeSpec   = $subTypeSpec;
        $this->_subTypes      = $subTypes;
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
     * Get specification details of the subtype aggregation.
     *
     * @return string
     */
    public function getSubTypeSpec()
    {
        return $this->_subTypeSpec;
    }

    /**
     * Get an associative array of subtypes for this complex-type.
     *
     * @return array
     */
    public function getSubTypes()
    {
        return $this->_subTypes;
    }
}