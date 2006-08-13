<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_XmlRpc
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/**
 * Exceptions
 */
require_once 'Zend/XmlRpc/Server/Exception.php';

/**
 * Core methods
 */
require_once 'Zend/XmlRpc/Server/CallbackParser/Core.php';

/**
 * Zend_XmlRpc_Server_CallbackParser_Object : Return XMLRPC method signature 
 * based on an object instance
 * 
 * @package Zend_XmlRpc
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_XmlRpc_Server_CallbackParser_Object extends Zend_XmlRpc_Server_CallbackParser_Core
{
    /**
     * Treat as object instance
     * @var boolean 
     */
    protected $_isObject = true;

    /**
     * Examine a class definition to create XMLRPC method signatures
     *
     * @param string $class Class name
     * @param string $namespace
     * @param false|array Array of constructor arguments
     * @return array
     * @throws Zend_XmlRpc_Server_Exception
     */
    public function parse($class, $namespace = '', $argv = false) 
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        return $this->_parseClass($class, $namespace, $argv);
    }
}

