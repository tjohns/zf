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
 * @package    Zend_Server
 * @subpackage Method
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Method prototype metadata
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Method
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Server_Method_Prototype
{
    /**
     * @var string Return type
     */
    protected $_returnType = 'void';

    /**
     * @var array Method parameters
     */
    protected $_parameters = array();

    /**
     * Constructor 
     * 
     * @param  null|array $options 
     * @return void
     */
    public function __construct($options = null)
    {
        if ((null !== $options) && is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set return value
     *
     * @param  string $returnType
     * @return Zend_Server_Method_Prototype
     */
    public function setReturnType($returnType)
    {
        $this->_returnType = $returnType;
        return $this;
    }

    /**
     * Retrieve return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return $this->_returnType;
    }

    /**
     * Add a parameter
     * 
     * @param  string $parameter 
     * @return Zend_Server_Method_Prototype
     */
    public function addParameter($parameter)
    {
        $this->_parameters[] = (string) $parameter;
        return $this;
    }

    /**
     * Add parameters
     * 
     * @param  array $parameter 
     * @return Zend_Server_Method_Prototype
     */
    public function addParameters(array $parameters)
    {
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }
        return $this;
    }

    /**
     * Set parameters
     *
     * @param  array $parameters
     * @return Zend_Server_Method_Prototype
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = array();
        $this->addParameters($parameters);
        return $this;
    }

    /**
     * Retrieve parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Set object state from array
     * 
     * @param  array $options 
     * @return Zend_Server_Method_Prototype
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Serialize to array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'returnType' => $this->getReturnType(),
            'parameters' => $this->getParameters(),
        );
    }
}
