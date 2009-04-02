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
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Reflection_Parameter extends ReflectionParameter 
{

    protected $_factory;

    /**
     * @var bool
     */
    protected $_isFromMethod = false;
    
    function __construct($function, $parameter, $factory) {
        $this->_factory = $factory;
        parent::__construct($function, $parameter);
    }
    
    /**
     * getDeclaringClass()
     *
     * @return Zend_Reflection_Class
     */
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = $this->_factory->createClass($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    /**
     * getClass()
     *
     * @return Zend_Reflection_Class
     */
    public function getClass()
    {
        $phpReflection = parent::getClass();
        $zendReflection = $this->_factory->createClass($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    /**
     * getDeclaringFunction()
     *
     * @return Zend_Reflection_Function|Zend_Reflection_Method
     */
    public function getDeclaringFunction()
    {
        $phpReflection = parent::getDeclaringFunction();
        if ($phpReflection instanceof ReflectionMethod) {
            $zendReflection = $this->_factory->createMethod($this->getDeclaringClass()->getName(), $phpReflection->getName());
        } else {
            $zendReflection = $this->_factory->createFunction($phpReflection->getName());
        }
        unset($phpReflection);
        return $zendReflection;
    }
    
    /**
     * getType()
     *
     * @return string
     */
    public function getType()
    {
        if ($docblock = $this->getDeclaringFunction()->getDocblock()) {
            $params = $docblock->getTags('param');
            
            if (isset($params[$this->getPosition() - 1])) {
                return $params[$this->getPosition() - 1]->getType();
            }
            
        }
        
        return null;
    }
    
}

