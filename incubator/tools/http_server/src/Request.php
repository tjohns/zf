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

require_once 'Zend/Uri/Http.php';

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Request
{
	/**
	 * HTTP method used (GET, POST, etc.)
	 *
	 * @var string
	 */
	public $method;

	/**
	 * HTTP protocol version
	 *
	 * @var string
	 */
	public $protocol_version;

	/**
	 * URI requested
	 *
	 * @var string
	 */
	public $uri;

	/**
	 * Path part of URI
	 *
	 * @var string
	 */
	public $path;

	/**
	 * File part of URI
	 *
	 * @var string
	 */
	public $file;

	/**
	 * Query string part of URI
	 *
	 * @var string
	 */
	public $query_string;

	/**
	 * HTTP headers
	 *
	 * @var array
	 */
	public $headers;

	/**
	 * IP address of the requester
	 *
	 * @var string
	 */
	public $remote_ip;

	/**
	 * Data part of the request
	 *
	 * @var string
	 */
	public $data;

	/**
	 * Document root to be used when serving the request
	 *
	 * @var string
	 */
	public $document_root;

	/**
	 * GET (query string) parameters
	 *
	 * @var array
	 */
	public $get = array();

	/**
	 * POST parameters
	 *
	 * @var array
	 */
	public $post = array();

	/**
	 * Cookies parameters
	 *
	 * @var array
	 */
	public $cookies = array();

	public function isComplete()
	{
		$request_not_set = $this->method == "";
		$uri_not_set = $this->uri == "";
		$not_enough_data = isset( $this->headers[ "Content-Length" ] ) && ( strlen( $this->data ) < $this->headers[ "Content-Length" ] );
		$no_data = !isset( $this->headers[ "Content-Length" ] ) && $this->data === null;
		$data_expected = $this->method == "POST" || $this->method == "PUT";

		return !( $request_not_set || $uri_not_set || ( $data_expected && ( $not_enough_data || $no_data ) ) );
	}

	public function __construct( $raw_request )
	{
		if( !preg_match( "/\r\n\r\n$/", $raw_request ) )
		{
			return;
		}
		else
		{
			$raw_request = preg_replace( "/\r\n\r\n$/", "", $raw_request );
		}

		$lines = explode( "\r\n", $raw_request );

		$this->setRequestLine( array_shift( $lines ) );

		while( ( $line = array_shift( $lines ) ) != "" )
		{
			$this->setHeader( $line );
		}

		if( count( $lines ) > 0 )
		{
			$this->setData( array_shift( $lines ) );
		}
	}

	protected function setRequestLine( $request_line )
	{
		list( $this->method, $uri, $this->protocol_version ) = explode( " ", $request_line );

		$this->setUri( $uri );
	}

	protected function setHeader( $header_line )
	{
		$header = explode( ":", $header_line );
		$this->headers[ trim( array_shift( $header ) ) ] = trim( join( ":", $header ) );
	}

	/**
	 * Extract the path, file and query string from the URI
	 *
	 * @param  string $uri
	 * @return void
	 */
	protected function setURI( $uri )
	{
		$this->uri = $uri;
		if (Zend_Uri_Http::check($uri)) {
			$uri = Zend_Uri::factory($uri);
			$this->query_string = $uri->getQuery();
			$this->path = dirname($uri->getPath());
			$this->file = basename($uri->getPath());
		} else {
			list($this->path, $this->query_string) = explode('?', $uri, 2);
			$this->file = basename($this->path);
			$this->path = dirname($this->path);
		}
		
		if (! $this->path) $this->path = '/';

		$this->path = urldecode( $this->path );
		$this->file = urldecode( $this->file );
		
		parse_str($this->query_string, $this->get);
	}

	protected function setData( $data )
	{
		$this->data = $data;

		parse_str($this->data, $this->post);
	}
}
