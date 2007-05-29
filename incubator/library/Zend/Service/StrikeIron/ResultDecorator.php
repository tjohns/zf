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
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Decorates a StrikeIron response object returned by the SOAP extension
 * to provide more a PHP-like interface.  Subclass this to provide behaviors
 * for specific types of results.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_StrikeIron_ResultDecorator
{
    /**
     * Object to decorate
     * @var object
     */
    protected $_object = null;
    
    /**
     * Class constructor
     *
     * @param object $object  Object to decorate
     */
    public function __construct($object)
    {
        $this->_object = $object;
    }
    
    /**
     * Proxy property access to the decorated object, inflecting
     * the property name and decorating any child objects returned.  
     * If the property is not found in the decorated object, return
     * NULL as a convenience feature to avoid notices.
     *
     * @param  string $property  Property name to retrieve
     * @return mixed             Value of property or NULL
     */
    public function __get($property)
    {
        $result = null;

        if (! isset($this->_object->$property)) {
            $property = $this->_inflect($property);
        }
        
        if (isset($this->_object->$property)) {
            $result = $this->_object->$property;
            $result = $this->_decorate($result);
        }
        return $result;
    }
    
    /**
     * Inflect a property name from PHP-style to the result object's
     * style.  The default implementation here only inflects the case
     * of the first letter, e.g. from "fooBar" to "FooBar".
     * 
     * @param  string $property  Property name to inflect
     * @return string            Inflected property name
     */
    protected function _inflect($property)
    {
        return ucfirst($property);        
    }

    /**
     * Decorate a value returned by the result object.  The default
     * implementation here only decorates child objects.  
     *
     * @param  mixed  $result  Value to decorate
     * @return mixed           Decorated result
     */
    protected function _decorate($result)
    {
        if (is_object($result)) {
            $result = new self($result);
        }
        return $result;
    }
}
