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


/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Controller_Plugin_Interface
{
	/**
	 * Called before Zend_Controller_Front begins evaluating the
	 * request against its routes.
	 *
	 * @return void
	 */
	public function routeStartup();

	/**
	 * Called after Zend_Controller_Front exits from the router.
	 *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return mixed
	 */
	public function routeShutdown($request);

	/**
	 * Called before Zend_Controller_Front enters its dispatch loop.
	 *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return mixed
	 */
	public function dispatchLoopStartup($request);

	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior.  By altering the 
     * request and resetting its dispatched flag (via 
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
	 *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return mixed
	 */
	public function preDispatch($request);

	/**
	 * Called after an action is dispatched by Zend_Controller_Dispatcher.
	 *
     * This callback allows for proxy or filter behavior. By altering the 
     * request and resetting its dispatched flag (via 
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
	 * @param  Zend_Controller_Request_Abstract|boolean $request
	 * @return mixed
	 */
	public function postDispatch($request);

	/**
	 * Called before Zend_Controller_Front exits its dispatch loop.
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown();
}

