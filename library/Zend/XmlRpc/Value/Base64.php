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
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_XmlRpc_Value_Scalar
 */
require_once 'Zend/XmlRpc/Value/Scalar.php';


/**
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_XmlRpc_Value_Base64 extends Zend_XmlRpc_Value_Scalar
{

    /**
     * Set the value of a base64 native type
     * We keep this value in base64 encoding
     *
     * @param string $value
     * @param bool $already_encoded If set, it means that the given string is already base64 encoded
     */
    public function __construct($value, $already_encoded=false)
    {
        $this->_type = self::XMLRPC_TYPE_BASE64;

        $value = (string)$value;    // Make sure this value is string
        if (!$already_encoded) {
            $value = base64_encode($value);     // We encode it in base64
        }
        $this->_value = $value;
    }

    /**
     * Return the value of this object, convert the XML-RPC native base64 value into a PHP string
     * We return this value decoded (a normal string)
     *
     * @return string
     */
    public function getValue()
    {
        return base64_decode($this->_value);
    }

}

