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
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_XmlRpc_Exception
 */
require_once 'Zend/XmlRpc/Exception.php';


/**
 * ZXmlRpcClientException add 2 functions for handling the fault response of failed XML-RPC requests
 * The fault response has 2 parameters, the fault code and fault string
 *
 * @package    Zend_XmlRpc
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_XmlRpc_Client_Exception extends Zend_XmlRpc_Exception
{

    /**
     * In case there was a failure in the request, this function gets the failure code
     *
     * @return int The error code
     */
    public function faultCode()
    {
        return $this->getCode();
    }


    /**
     * In case there was a failure in the request, this function gets the failure string
     *
     * @return string The error string
     */
    public function faultString()
    {
        return $this->getMessage();
    }

}

