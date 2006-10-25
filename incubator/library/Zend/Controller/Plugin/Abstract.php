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
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Controller_Plugin_Interface */
require_once 'Zend/Controller/Plugin/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Controller_Plugin_Abstract implements Zend_Controller_Plugin_Interface
{
	/**
	 * Called before Zend_Controller_Front begins evaluating the
	 * request against its routes.
	 *
	 * @return void
	 */
	public function routeStartup()
	{}

	/**
	 * Called after Zend_Controller_Router exits.
	 *
	 * Called after Zend_Controller_Front exits from the router.
	 *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return Zend_Controller_Request_Abstract
	 */
	public function routeShutdown($request)
	{
	    return $request;
	}

	/**
	 * Called before Zend_Controller_Front enters its dispatch loop.
	 *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return Zend_Controller_Request_Abstract
	 */
	public function dispatchLoopStartup($request)
	{
	    return $request;
	}

	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior.  By altering the 
     * request and resetting its dispatched flag (via 
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
	 *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return Zend_Controller_Request_Abstract
	 */
	public function preDispatch($request)
	{
	    return $request;
	}

	/**
	 * Called after an action is dispatched by Zend_Controller_Dispatcher.
	 *
     * This callback allows for proxy or filter behavior. By altering the 
     * request and resetting its dispatched flag (via 
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
	 *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return Zend_Controller_Request_Abstract
	 */
	public function postDispatch($request)
	{
	    return $request;
	}

	/**
	 * Called before Zend_Controller_Front exits its dispatch loop.
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown()
	{}
}
