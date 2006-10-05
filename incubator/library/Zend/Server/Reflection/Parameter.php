<?php
/**
 * Zend_Server_Reflection_Exception
 */
require_once 'Zend/Server/Reflection/Exception.php';

/**
 * Parameter Reflection 
 *
 * Decorates a ReflectionParameter to allow setting the parameter type
 * 
 * @package Zend_Server
 * @subpackage Reflection
 * @version $Id$
 */
class Zend_Server_Reflection_Parameter
{
    /**
     * @var ReflectionParameter
     */
    protected $_reflection;

    /**
     * Parameter position
     * @var int 
     */
    protected $_position;

    /**
     * Parameter type
     * @var string 
     */
    protected $_type;

    /**
     * Parameter description
     * @var string 
     */
    protected $_description;

    /**
     * Constructor
     * 
     * @param ReflectionParameter $r 
     * @param string $type Parameter type
     * @param string $description Parameter description
     */
    public function __construct(ReflectionParameter $r, $type = 'mixed', $description = '')
    {
        $this->_reflection = $r;
        $this->setType($type);
        $this->setDescription($description);
    }

    /**
     * Proxy reflection calls
     * 
     * @param string $method 
     * @param array $args 
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (method_exists($this->_reflection, $method)) {
            return call_user_func_array(array($this->_reflection, $method), $args);
        }

        throw new Zend_Server_Reflection_Exception('Invalid reflection method');
    }

    /**
     * Retrieve parameter type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set parameter type
     * 
     * @param string|null $type
     * @return void
     */
    public function setType($type)
    {
        if (!is_string($type) && (null !== $type)) {
            throw new Zend_Server_Reflection_Exception('Invalid parameter type');
        }

        $this->_type = $type;
    }

    /**
     * Retrieve parameter description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set parameter description
     * 
     * @param string|null $description
     * @return void
     */
    public function setDescription($description)
    {
        if (!is_string($description) && (null !== $description)) {
            throw new Zend_Server_Reflection_Exception('Invalid parameter description');
        }

        $this->_description = $description;
    }

    /**
     * Set parameter position
     * 
     * @param int $index 
     * @return void
     */
    public function setPosition($index)
    {
        $this->_position = (int) $index;
    }

    /**
     * Return parameter position
     * 
     * @return int
     */
    public function getPosition()
    {
        return $this->_position;
    }
}
