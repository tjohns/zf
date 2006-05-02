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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Exception.php';


/**
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Controller_Router_Interface
{
    /**
     * Processes an HTTP request and routes to a Zend_Controller_Dispatcher_Token object.  If
     * no route was possible, an exception is thrown.
     *
	 * @param  Zend_Controller_Dispatcher_Interface
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Dispatcher_Token|boolean
     */
    public function route(Zend_Controller_Dispatcher_Interface $dispatcher);
}

