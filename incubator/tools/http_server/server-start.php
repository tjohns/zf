#!/usr/bin/env php
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

	$current_directory = preg_replace( "!/" . basename( $_SERVER[ 'PHP_SELF' ] ) . "$!", "", realpath( $_SERVER[ 'PHP_SELF' ] ) );

	$framework_directory = realpath( $current_directory . "/../../../library" );
	$incubator_directory = realpath( $current_directory . "/../../library" );

	ini_set( "include_path", ini_get( "include_path" ) . ":" . $current_directory . "/src:" . $incubator_directory . ":" . $framework_directory );

	require_once( "Server.php" );

//	error_reporting( E_ALL );

// Not 100% sure, but aren't these all the defaults for the CLI SAPI?
// Doesn't matter, actually - still need to set them for the CGI SAPI
	set_time_limit( 0 );
	ini_set( "html_errors", "0" );
	ini_set( "display_errors", "1" );
	ob_implicit_flush();

	$address = '127.0.0.1';
	$port = 8888;
	$document_root = getcwd();

	$args = $_SERVER[ "argv" ];

	$script = array_shift( $args );

	$help = false;
	$rewrite = false;

	while( count( $args ) > 0 )
	{
		$arg = array_shift( $args );

		switch( $arg )
		{
			case "-p":
			{
				$port = trim( array_shift( $args ) );
			} break;

			case "-h":
			{
				$address = trim( array_shift( $args ) );
			} break;

			case "-d":
			{
				$document_root = trim( array_shift( $args ) );
			} break;

			case "--help":
			{
				$help = true;
			} break;

			case "--rewrite":
			{
				$rewrite = true;
			} break;
		}
	}

	if( $help )
	{
		print "Usage: {$script} [-p port] [-h host] [-d document_root] [--rewrite] [--help]\n";
		print "\nCurrent settings:\n";
		print "* Host          - {$address}\n";
		print "* Port          - {$port}\n";
		print "* Document Root - {$document_root}\n";
		print "* Rewrite Rule  - " . ( $rewrite ? "On" : "Off" ) . "\n";
		exit;
	}

	// Strip any trailing slash from the document root
	$document_root = preg_replace( "!^(.*)/$!", "$1", $document_root );

	$server = new Server( $address, $port );
	$server->document_root = $document_root;
	if( $rewrite )
	{
		$server->handler_options[ "rewrite" ] = true;
	}
	$server->listen();

?>
