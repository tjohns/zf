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
 * @package    Zend_Http
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once( "Zend/Http/Response.php" );
require_once( "Request.php" );

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHandler
{
	/**
	 * HTTP Request
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * Class Constructor, sets the request object
	 *
	 * @param  Request $request
	 * @return void
	 */
	public function __construct( Request $request )
	{
		$this->request = $request;
	}

	/**
	 * Process the request object
	 *
	 * @return Zend_Http_Response
	 */
	abstract public function handle();
}

