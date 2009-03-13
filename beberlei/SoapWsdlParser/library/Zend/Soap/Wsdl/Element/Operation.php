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
 * Zend_Soap_Wsdl_Element_Operation
 *
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Wsdl
 */
class Zend_Soap_Wsdl_Element_Operation implements Zend_Soap_Wsdl_Element_Interface
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

    /**
     * Input Message
     *
     * @var Zend_Soap_Wsdl_Element_Message|null
     */
    protected $_inputMessage;

    /**
     * Output Message
     *
     * @var Zend_Soap_Wsdl_Element_Message|null
     */
    protected $_outputMessage;

    /**
     * Construct new Operation
     * 
     * @param string $name
     * @param string $documentation
     * @param Zend_Soap_Wsdl_Element_Message $inputMessage
     * @param Zend_Soap_Wsdl_Element_Message $outputMessage
     */
    public function __construct($name, $documentation="", Zend_Soap_Wsdl_Element_Message $inputMessage=null, Zend_Soap_Wsdl_Element_Message $outputMessage=null)
    {
        if(!is_string($name)) {
            require_once "Zend/Soap/Wsdl/Exception.php";
            throw new Zend_Soap_Wsdl_Exception("Operation Element has to given a string as name.");
        }
        $this->_name          = $name;
        $this->_inputMessage  = $inputMessage;
        $this->_outputMessage = $outputMessage;
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
     * Get input message of this operation.
     *
     * @return Zend_Soap_Wsdl_Element_Message|null
     */
    public function getInputMessage()
    {
        return $this->_inputMessage;
    }

    /**
     * Get output message of this operation.
     *
     * @return Zend_Soap_Wsdl_Element_Message|null
     */
    public function getOutputMessage()
    {
        return $this->_outputMessage;
    }
}