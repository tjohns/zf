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

require_once( "DefaultHandler.php" );
require_once( "Exception.php" );
require_once( "Request.php" );
require_once( "Zend/Http/Response.php" );

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Server
{
	private $socket;

	private $connections = array();

	public $handler = "DefaultHandler";
	public $document_root = "";

	public function __construct( $address = '127.0.0.1', $port = 8000 )
	{
		if( !function_exists( "socket_create" ) )
		{
			throw new Zend_Http_Server_Exception( "Socket extension not found" );
		}
		
		// Make sure pcntl functions are available
		if (! function_exists('pcntl_fork')) {
			throw new Zend_Http_Server_Exception('PCNTL exntension not found');
		}

		if( ( $this->socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP ) ) < 0 )
		{
			throw new Zend_Http_Server_Exception( "socket_create() failed: Reason: " . socket_strerror( $this->socket ) );
		}

		socket_set_option( $this->socket, SOL_SOCKET, SO_REUSEADDR, 1 );

		if( ( $bind_result = socket_bind( $this->socket, $address, $port ) ) < 0 )
		{
			throw new Zend_Http_Server_Exception( "socket_bind() failed: Reason: " . socket_strerror( $bind_result ) );
		}

		if( !( $nonblock_result = socket_set_nonblock( $this->socket ) ) )
		{
			throw new Zend_Http_Server_Exception( "socket_set_nonblock() failed: Reason: " .
				socket_strerror( $nonblock_result ) );
		}
	}

	public function __destruct()
	{
		socket_close( $this->socket );

		foreach( $this->connections as $connection )
		{
			socket_close( $connection );
		}
	}

	public function setHandler( $handler_class )
	{
		$this->handler = $handler_class;
	}

	public function getHandler()
	{
		return $this->handler;
	}


	public function listen()
	{
		if( ( $ret = socket_listen( $this->socket, 5 ) ) < 0 )
		{
			throw new Zend_Http_Server_Exception( "socket_listen() failed: Reason: " . socket_strerror($ret) );
		}

		// Listening...

		while( true )
		{
			$sockets = array( $this->socket );
			$client = null;

			if( socket_select( $sockets, $w = NULL, $e = NULL, 0 ) )
			{
				// Waiting for connections...

				if( ( $client = socket_accept( $this->socket ) ) < 0 )
				{
					throw new Zend_Http_Server_Exception( "socket_accept() failed: Reason: " .
						socket_strerror( $client ) );
				}
			}
			else
			{
				// Without this, the server will use 100% of available CPU constantly!
				usleep( 100 ); // The only sensible place to sleep.  Sleep for 0.0001 seconds if no new connections have arrived. Still allows a potential 10000 requests a second :)
			}

			// New connection, forking.

			if( $client )
			{
				$pid = pcntl_fork();

				if( $pid == -1 )
				{
					die( "Could not fork new process!" );
				}
				else if( $pid )
				{
//					print "Storing socket for $pid\n";
					$this->connections[ $pid ] = $client;
				}
				else
				{
					$raw_request = "";

					$i = 1;

					while( $i < 10 )
					{
	//					print "Read attempt: $i\n";
						$i++;
						// Child: reading
						$buffer = socket_read( $client, 2048, PHP_BINARY_READ );
	//					print strlen( $buffer ) . " bytes read\n";


						if( $buffer === false )
						{
							// Child: Error or EOF.  Exiting.
							exit;
						}
						else
						{
							$raw_request .= $buffer;

							if( $buffer === "" || strlen( $buffer ) < 2048 )
							{
								$request = new Request( $raw_request );
								$request->document_root = $this->document_root;
								socket_getpeername( $client, $request->remote_ip );

								if( $request->isComplete() )
								{
									$handler = new $this->handler( $request );

									$response = $handler->handle();
									socket_write( $client, $response->asString() );

									print( $request->remote_ip . " - - [" . date( "d/M/Y:H:i:s O" ) . "] \"" . 
										$request->method . " " . $request->uri . " " . $request->protocol_version .
										"\" " . $response->getStatus() . " " . strlen( $response->getBody() ) . " \"" .
										trim( $request->headers[ "Referer" ] ) . "\" \"" .
										trim( $request->headers[ "User-Agent" ] ) . "\"\n" );
									exit;
								}
								else
								{
									unset( $request );
								}
							}
						}
					}
//					print "Here!\n";
					print( "Read more than ten times for request and still incomplete - bailing out" );
					exit;
				}
			}

			// Parent: Check for finished children

			while( ( $child = pcntl_wait( $status, WNOHANG ) ) > 0 )
			{
//				print "Closing socket for $child\n";

				socket_close( $this->connections[ $child ] );

				unset( $this->connections[ $child ] );
//				print "Connection count: " . count( $this->connections ) . "\n";
			}
		}
	}
}

