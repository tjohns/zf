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

require_once "Zend/Soap/Wsdl/Element/Interface.php";

/**
 * Zend_Soap_Wsdl_Document
 *
 * Will be returned from the Zend_Soap_Wsdl_Parser and will in the future be used to generate WSDLs from.
 *
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_Wsdl_Document implements Zend_Soap_Wsdl_Element_Interface
{
    /**
     * Version numbers of WSDL document
     */
    const WSDL_11 = "1.1";

    /**
     * Service Name
     *
     * @var string
     */
    protected $_name;

    /**
     * WSDL Specification Version
     */
    protected $_version;

    /**
     * Operations
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $_operations;

    /**
     * Ports
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $_ports;

    /**
     * Bindings
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $_bindings;

    /**
     * Services
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $_services;

    /**
     * Types
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $_types;

    /**
     * Webservice Documentation
     *
     * @var string
     */
    protected $_documentation;

    /**
     * Namespaces in Key to Uri Hashmap
     *
     * @var array
     */
    protected $_namespaces;

    /**
     * Construct new WSDL document
     *
     * @param string $name
     * @param string $version
     * @param Zend_Soap_Wsdl_Element_Collection $operations
     * @param Zend_Soap_Wsdl_Element_Collection $ports
     * @param Zend_Soap_Wsdl_Element_Collection $bindings
     * @param Zend_Soap_Wsdl_Element_Collection $services
     * @param Zend_Soap_Wsdl_Element_Collection $types
     * @param string $documentation
     * @param array $namespaces
     */
    public function __construct($name, $version,
        Zend_Soap_Wsdl_Element_Collection $operations,
        Zend_Soap_Wsdl_Element_Collection $ports,
        Zend_Soap_Wsdl_Element_Collection $bindings,
        Zend_Soap_Wsdl_Element_Collection $services,
        Zend_Soap_Wsdl_Element_Collection $types,
        $documentation,
        array $namespaces=array()
        )
    {
        $this->_name             = $name;
        $this->_version          = $version;
        $this->_operations       = $operations;
        $this->_ports            = $ports;
        $this->_bindings         = $bindings;
        $this->_services         = $services;
        $this->_types            = $types;
        $this->_documentation    = $documentation;
        $this->_namespaces       = $namespaces;
    }

    /**
     * Get webservice name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get WSDL specification version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Get documentation of this WSDL document if existant.
     *
     * @return string
     */
    public function getDocumentation()
    {
        return $this->_documentation;
    }

    /**
     * Get Operations
     *
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    public function getOperations()
    {
        return $this->_operations;
    }

    /**
     * Get Bindings
     *
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    public function getBindings()
    {
        return $this->_bindings;
    }

    /**
     * Get Port Types
     *
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    public function getPortTypes()
    {
        return $this->_ports;
    }

    /**
     * Get services
     * 
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    public function getServices()
    {
        return $this->_services;
    }

    /**
     * Return Types
     *
     * @return Zend_Soap_Wsdl_Element_Collection
     */
    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * Get Namespaces
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }
}