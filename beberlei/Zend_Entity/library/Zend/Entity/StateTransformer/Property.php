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
 * @package    Zend_Entity
 * @subpackage StateTransformer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * StateTransformer working with ReflectionProperty instances.
 *
 * @uses       Zend_Entity_StateTransformer_Abstract
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage StateTransformer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_StateTransformer_Property extends Zend_Entity_StateTransformer_Abstract
{
    /**
     * @var array
     */
    protected $_reflProperties = array();

    /**
     *
     * @param string $class
     * @param array $propertyNames
     */
    public function setTargetClass($class, $propertyNames)
    {
        try {
            $reflClass = new ReflectionClass($class);
            foreach($propertyNames AS $propertyName) {
                $this->_reflProperties[$propertyName] = $reflClass->getProperty($propertyName);

                if($this->_reflProperties[$propertyName]->isPrivate() || $this->_reflProperties[$propertyName]->isProtected()) {
                    if (version_compare(PHP_VERSION, '5.3.0') === 1) {
                        $this->_reflProperties[$propertyName]->setAccesible(true);
                    } else {
                        throw new Zend_Entity_StateTransformer_Exception("Can only access private or protected properties with PHP Version 5.3");
                    }
                }
            }
        } catch(ReflectionException $e) {
            throw new Zend_Entity_StateTransformer_Exception($e->getMessage());
        }
    }

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object
     * @return array
     */
    public function getState($object)
    {
        $state = array();
        foreach($this->_reflProperties AS $propertyName => $reflProperty) {
            $state[$propertyName] = $reflProperty->getValue($object);
        }
        return $state;
    }

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object $object
     * @param array $state
     * @return void
     */
    public function setState($object, $state)
    {
        foreach($state AS $propertyName => $value) {
            $this->_reflProperties[$propertyName]->setValue($object, $value);
        }
        return $state;
    }

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object $object
     * @param string $idPropertyName
     * @param string|int $id
     */
    public function setId($object, $idPropertyName, $id)
    {
        $this->_reflProperties[$idPropertyName]->setValue($object, $id);
    }
}