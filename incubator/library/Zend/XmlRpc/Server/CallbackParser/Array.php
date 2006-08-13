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
 * Zend_XmlRpc_Server_CallbackParser_Array : Return XMLRPC method signatures 
 * defined in an array
 * 
 * @package Zend_XmlRpc
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_XmlRpc_Server_CallbackParser_Array extends Zend_XmlRpc_Server_CallbackParser_Core
{
    /**
     * Parse an array of xmlrpc method signatures
     *
     * @param array $methods
     * @param string $namespace
     * @return array
     * @throws Zend_XmlRpc_Server_Exception
     */
    public function parse($methods, $namespace = '', $argv = false) 
    {
        $signatures = array(); // final array of signatures
        $e          = false;   // throwing exceptions from loops is bad
        foreach ($methods as $methodName => $info)
        {
            // Do not load system.* methods
            if (in_array($methodName, Zend_XmlRpc_Server::$systemMethods)) {
                continue;
            }

            switch (true) {
                case (isset($info['function'])):
                    if (!function_exists($info['function'])) {
                        $e = 'Invalid function callback for ' . $methodName;
                        break 2;
                    }
                    $callback = array('function' => $info['function']);
                    break;
                case (isset($info['method'])):
                    if (!isset($info['method']['class'])
                        || !isset($info['method']['method']))
                    {
                        $e = 'Invalid instance method callback for ' . $methodName;
                        break 2;
                    }
                    $callback = array('method' => $info['method']);

                    if (isset($info['params'])) {
                        $callback['params'] = $info['params'];
                    }
                    break;
                case (isset($info['static'])):
                    if (!isset($info['static']['class'])
                        || !isset($info['static']['method']))
                    {
                        $e = 'Invalid static method callback for ' . $methodName;
                        break 2;
                    }
                    $callback = array('static' => $info['static']);
                    break;
                default:
                    $e = 'No function, instance method, or static method provided for ' . $methodName;
                    break 2;
            }

            // Prepend namespace, if necessary
            if (!empty($namespace)) {
                $methodName = $namespace . '.' . $methodName;
            }

            // Add method to dispatch table, with callback
            $signatures[$methodName] = $callback;

            // Add methodHelp, if available
            if (isset($info['methodHelp'])) {
                $signatures[$methodName]['methodHelp'] = $info['methodHelp'];
            }

            // Add signatures, if available
            if (isset($info['signatures']) && is_array($info['signatures'])) {
                $sigs = array();
                foreach ($info['signatures'] as $signature) {
                    if (is_array($signature)) {
                        $sigs[] = $signature;
                    }
                }

                if (!empty($sigs)) {
                    $signatures[$methodName]['signatures'] = $sigs;
                }
            }
        }

        if ($e) {
            throw new Zend_XmlRpc_Server_Exception($e);
        }

        return $signatures;
    }
}
