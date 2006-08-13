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
 * Zend_XmlRpc_Server
 */
require_once 'Zend/XmlRpc/Server.php';

/**
 * Exceptions
 */
require_once 'Zend/XmlRpc/Server/Exception.php';

abstract class Zend_XmlRpc_Server_CallbackParser_Core
{
    /**
     * Flag: whether or not the class should be treated as an object instance
     * @var boolean 
     */
    protected $_isObject = false;

    /**
     * Transform a phpdoc type to an xmlrpc type
     * 
     * @param string $type 
     * @return string
     */
    protected static function _xmlRpcType($type) 
    {
        switch (strtolower($type)) {
            case 'mixed':
            case 'object':
            case 'struct':
                return Zend_XmlRpc_Value::XMLRPC_TYPE_STRUCT;
            case 'false':
            case 'true':
            case 'bool':
            case 'boolean':
                return Zend_XmlRpc_Value::XMLRPC_TYPE_BOOLEAN;
            case 'double':
            case 'float':
                return Zend_XmlRpc_Value::XMLRPC_TYPE_DOUBLE;
            case 'int':
            case 'integer':
            case 'i4':
                return Zend_XmlRpc_Value::XMLRPC_TYPE_INTEGER;
            case 'datetime.iso8601':
                return 'dateTime.iso8601';
            case 'array':
                return Zend_XmlRpc_Value::XMLRPC_TYPE_ARRAY;
            case 'void':
            case 'base64':
                return strtolower($type);
            case 'string':
            default:
                return Zend_XmlRpc_Value::XMLRPC_TYPE_STRING;
        }
    }

    /**
     * Determines method signature methodHelp from a DocBlock comment
     *
     * Determines the method signature and methodHelp from a DocBlock comment.
     * Returns an associative array with the keys 'methodHelp' and 'signatures'.
     *
     * @todo Determine how to handle OR'd parameters
     * @param ReflectionFunction $function
     * @return array
     */
    public static function getDispatchFromFunction(ReflectionFunction $function)
    {
        $helpText   = '';
        $signatures = array();

        $docBlock   = $function->getDocComment();
        $parameters = $function->getParameters();
        $required   = $function->getNumberOfRequiredParameters();

        if (!empty($docBlock)) {
            // Get help text
            if (preg_match(':/\*\*\s*\r?\n\s*\* (.*?)\r?\n\s*\*( @|/):s', $docBlock, $matches))
            {
                $helpText = $matches[1];
                $helpText = preg_replace('/(^\s*\* )/m', '', $helpText);
                $helpText = preg_replace('/\r?\n\s*\*\s*(\r?\n)*/s', "\n", $helpText);
                $helpText = trim($helpText);
            }

            // Get return type(s)
            $return = 'void';
            if (preg_match('/@return ([^ ]*) /', $docBlock, $matches)) {
                $return = self::_xmlRpcType(trim($matches[1]));
            }

            if (strstr($return, '|')) {
                $return = explode($return, '|');
            } else {
                $return = (array) $return;
            }


            if (0 < $parameters) {
                // Get param types
                $params = array();
                if (preg_match_all('/@param ([^ ]*) /', $docBlock, $matches)) {
                    $params = $matches[1];
                    foreach ($params as $key => $param) {
                        $params[$key] = self::_xmlRpcType($param);
                    }
                    if (count($params) < count($parameters)) {
                        $start = count($params);
                        $end   = count($parameters);
                        for ($i = $start; $i < $end; ++$i) {
                            $params[$i] = 'mixed';
                        }
                    }
                } else {
                    if (count($parameters)) {
                        $params = array_fill(0, count($parameters) - 1, 'mixed');
                    }
                }

                foreach ($return as $ret) {
                    $sig = $params;
                    array_unshift($sig, $ret);
                    $signatures[] = $sig;
                }
            }
        } else {
            if (count($parameters)) {
                $signature = array_fill(0, count($parameters), 'mixed');
            } else {
                $signature = array('mixed');
            }
            $signatures[] =  $signature;
        }

        return array(
            'methodHelp' => $helpText,
            'signatures' => $signatures
        );
    }

    protected function _parseClass($class, $namespace = '', $argv = false)
    {
        // Get reflection object
        if (!is_string($class) || !class_exists($class)) {
            throw new Zend_XmlRpc_Server_Exception('Cannot attach class to server; invalid class');
        }

        $reflection = new ReflectionClass($class);

        $dispatchers = array();
        $dispatch    = array();
        $className   = $reflection->getName();
        foreach ($reflection->getMethods() as $method) {
            // Don't aggregate magic methods
            if (0 === strpos($method->getName(), '__')) {
                continue;
            }

            if (($this->_isObject && $method->isPublic())
                || (!$this->_isObject && $method->isPublic() && $method->isStatic()))
            {
                $dispatch = self::getDispatchFromFunction($method);
                $methodName = $method->getName();

                // Determine callback
                if ($this->_isObject) {
                    if ($method->isStatic()) {
                        $dispatch['static'] = array(
                            'class'  => $className,
                            'method' => $methodName
                        );
                    } else {
                        $dispatch['method'] = array(
                            'class'  => $className,
                            'method' => $methodName
                        );
                        if ($argv) {
                            $dispatch['method']['params'] = $argv;
                        }
                    }
                } else {
                    $dispatch['static'] = array(
                        'class'  => $className,
                        'method' => $methodName
                    );
                }

                // Create xmlrpc method name
                $xmlRpcMethod = empty($namespace) ? $methodName : $namespace . '.' . $methodName;

                $dispatchers[$xmlRpcMethod] = $dispatch;
            }
        }

        return $dispatchers;
    }

    abstract public function parse($method, $namespace = '', $argv = false);
}
