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
 * Zend_Soap_Wsdl_Parser_Result
 * 
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_Wsdl_Parser_Result
{
    /**
     * Service Name
     * 
     * @var string
     */
    protected $name;

    /**
     * WSDL Specification Version
     */
    protected $version;

    /**
     * Operations
     * 
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $operations;

    /**
     * Ports
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $ports;

    /**
     * Bindings
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $bindings;

    /**
     * Services
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $services;

    /**
     * Types
     *
     * @var Zend_Soap_Wsdl_Element_Collection
     */
    protected $types;

    /**
     * Webservice Documentation
     *
     * @var string
     */
    protected $documentation;
    
    public function __construct($name,
        $version,
        Zend_Soap_Wsdl_Element_Collection $operations,
        Zend_Soap_Wsdl_Element_Collection $ports,
        Zend_Soap_Wsdl_Element_Collection $bindings,
        Zend_Soap_Wsdl_Element_Collection $services,
        Zend_Soap_Wsdl_Element_Collection $types,
        $documentation)
    {
        $this->name             = $name;
        $this->version          = $version;
        $this->operations       = $operations;
        $this->ports            = $ports;
        $this->bindings         = $bindings;
        $this->services         = $services;
        $this->types            = $types;
        $this->documentation    = $documentation;
    }

    public function __get($name)
    {
        if(isset($this->$name)) {
            return $this->$name;
        } else {
            require_once "Zend/Soap/Wsdl/Parser/Exception.php";
            throw new Zend_Soap_Wsdl_Parser_Exception(sprintf(
                "Trying to access illegal value '%s' in SOAP Parser Result.",
                 $name
            ));
        }
    }

    /**
     * Get webservice name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get WSDL specification version
     */
    public function getVersion()
    {
        return $this->version;
    }
}


