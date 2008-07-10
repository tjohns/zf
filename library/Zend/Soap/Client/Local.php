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
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Soap_Client_Exception */
require_once 'Zend/Soap/Server/Exception.php';


/** Zend_Soap_Server */
require_once 'Zend/Soap/Server.php';

if (extension_loaded('soap')) {

/**
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_Client_Local extends SoapClient
{
    /**
     * Server object
     *
     * @var Zend_Soap_Server
     */
    protected $_server;

    /**
     * Local client constructor
     *
     * @param Zend_Soap_Server $server
     * @param string $wsdl
     * @param array $options
     */
    function __construct(Zend_Soap_Server $server, $wsdl, $options) {
    	$this->_server = $server;
        parent::__construct($wsdl, $options);
    }

    function __doRequest($request, $location, $action, $version) {
    	ob_start();
        $this->_server->handle($request);
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }

}

} // end if (extension_loaded('soap')
