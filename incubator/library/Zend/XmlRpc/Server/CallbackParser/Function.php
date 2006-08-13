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
 * Zend_XmlRpc_Server_CallbackParser_Function : Return XMLRPC method signature 
 * based on a function definition
 * 
 * @package Zend_XmlRpc
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_XmlRpc_Server_CallbackParser_Function extends Zend_XmlRpc_Server_CallbackParser_Core
{
    /**
     * Examine a function to create an XMLRPC method signature
     *
     * @param string $function Function name
     * @param string $namespace
     * @return array
     * @throws Zend_XmlRpc_Server_Exception
     */
    public function parse($function, $namespace = '', $argv = false) 
    {
        if (!is_string($function) || !function_exists($function)) {
            throw Zend_XmlRpc_Server_Exception('Invalid function ' . $function, 613);
        }

        $reflection = new ReflectionFunction($function);

        $dispatch = self::getDispatchFromComment($reflection->getDocComment());
        
        // Create xmlrpc method name
        $xmlRpcMethod = empty($namespace) ? $function : $namespace . '.' . $function;

        $dispatch['function'] = $function;

        return array($xmlRpcMethod => $dispatch);
    }
}
