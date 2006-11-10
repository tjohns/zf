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

	private $complete = true;

	public function isComplete()
	{
		return $this->complete;
	}

	public function __construct( $raw_request )
	{
		$lines = explode( "\r\n", $raw_request );

		list( $this->method, $uri, $this->protocol_version ) = explode( " ", array_shift( $lines ) );

		$this->setURI( $uri );

		$headers = array();

		do
		{
			if( count( $lines ) == 0 )
			{
				$this->complete = false;
				$line = "";
			}
			else
			{
				$line = array_shift( $lines );

				if( $line !== "" )
				{
					$header = explode( ":", $line );
					$headers[ trim( array_shift( $header ) ) ] = trim( join( ":", $header ) );
				}
			}
		} while( $line !== "" );

		if( !isset( $headers[ "Referer" ] ) )
		{
			$headers[ "Referer" ] = "-";
		}

		$this->headers = $headers;

		if( count( $lines ) > 0 )
		{
			$this->setData( array_shift( $lines ) );
		}

		if( ( $this->method == "POST" || $this->method == "PUT" ) && $this->data === null )
		{
				$this->complete = false;
		}
	}


	/**
	 * Extract the path, file and query string from the URI
	 *
	 * @param  string $uri
	 * @return void
	 */
	protected function setURI( $uri )
	{
		preg_match( "!^(.*)/([^/?]*)(\?.*$|$)!", $uri, $matches );
		list( $this->uri, $this->path, $this->file, $this->query_string ) = $matches;

		$query_string = preg_replace( "|^\?|", "", $this->query_string );

		if( trim( $query_string ) == "" )
			return;
		
		$params = explode( "&", trim( $query_string ) );
		foreach( $params as $param )
		{
			$parts = explode( "=", $param );

			if( count( $parts ) > 1 )
			{
				$this->get[ $parts[ 0 ] ] = urldecode( $parts[ 1 ] );
			}
			else
			{
				$this->get[ $parts[ 0 ] ] = 1;
			}
		}
	}

	protected function setData( $data )
	{
		$this->data = $data;

		if( trim( $data ) == "" )
			return;

		$params = explode( "&", trim( $data ) );

		foreach( $params as $param )
		{
			$parts = explode( "=", $param );

			if( count( $parts ) > 1 )
			{
				$this->post[ $parts[ 0 ] ] = urldecode( $parts[ 1 ] );
			}
			else
			{
				$this->post[ $parts[ 0 ] ] = 1;
			}
		}
	}
}
