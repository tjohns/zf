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

class Zend_Soap_Wsdl_Element_Part implements Zend_Soap_Wsdl_Element_Interface
{
    /**
     * Part Name
     * @var string
     */
    protected $_name;

    /**
     * Part Type
     * 
     * @var string|Zend_Soap_Wsdl_Element_Type
     */
    protected $_type;

    /**
     * Construct a Message Part
     *
     * @param string $name
     * @param string|Zend_Soap_Wsdl_Element_Type $type
     */
    public function __construct($name, $type, $documentation="")
    {
        $this->_name          = $name;
        $this->_type          = $type;
        $this->_documentation = $documentation;
    }

    /**
     * Get Message Part Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get Message Part documentation
     *
     * @return string
     */
    public function getDocumentation()
    {
        return $this->_documentation;
    }

    /**
     * Get Message Part Type
     *
     * @return string¦Zend_Soap_Wsdl_Element_Type
     */
    public function getType()
    {
        return $this->_type;
    }
}