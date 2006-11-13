<?php

require_once 'Zend/Http/Client.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'SocketTest.php';

/**
 * This Testsuite includes all Zend_Http_Client that require a working web 
 * server to perform. It was designed to be extendable, so that several 
 * test suites could be run against several servers, with different client
 * adapters and configurations.
 * 
 * Note that $this->baseuri must point to a directory on a web server
 * containing all the files under the _files directory. You should symlink
 * or copy these files and set 'baseuri' properly.
 * 
 * You can also set the proper constand in your test configuration file to 
 * point to the right place.
 *
 * @category Zend
 * @package Zend_Http_Client
 * @subpackage UnitTests
 * @copyright 
 * @license
 */
class Zend_Http_Client_CurlTest extends Zend_Http_Client_SocketTest
{
	/**
	 * Identifier of this test suite. Should be the same as used in constants
	 * in TestConfiguration.php
	 *
	 * @var string
	 */
	protected $testname = 'CURL';
	
	/**
	 * Configuration array
	 *
	 * @var array
	 */
	protected $config = array(
		'adapter'     => 'Zend_Http_Client_Adapter_Curl'
	);
}