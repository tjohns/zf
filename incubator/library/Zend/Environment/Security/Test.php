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
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */

/**
 * Zend_Environment_Container_Abstract
 */
require_once 'Zend/Environment/Container/Abstract.php';



abstract class Zend_Environment_Security_Test
{
    const LANG_DEFAULT = 'en';

    const RESULT_OK = -1;

    const RESULT_NOTICE = -2;

    const RESULT_WARN = -4;

    const RESULT_ERROR = -1024;

    const RESULT_NOTRUN = -2048;

    const COMMON_TMPDIR = '/tmp';

    const MOREINFO_BASEURL = 'http://phpsec.org/projects/phpsecinfo/tests/';



    /**
	 * This value is used to group test results together.
	 *
	 * For example, all tests related to the mysql lib should be grouped under "mysql."
	 *
	 * @var string
	 */
	protected $_group = NULL;


	/**
	 * This should be a <b>unique</b>, human-readable identifier for this test
	 *
	 * @var string
	 */
	protected $_name  = NULL;


	/**
	 * This is the recommended value the test will be looking for
	 *
	 * @var mixed
	 */
	protected $_recommended_value = NULL;


	/**
	 * The result returned from the test
	 *
	 * @var integer
	 */
	protected $_result = self::RESULT_NOTRUN;


	/**
	 * The message corresponding to the result of the test
	 *
	 * @var string
	 */
	protected $_message = NULL;


	/**
	 * the language code.  Should be a pointer to the setting in the PhpSecInfo object
	 *
	 * @var string
	 */
	protected $_language = self::LANG_DEFAULT;

	/**
	 * Enter description here...
	 *
	 * @var mixed
	 */
	protected $_current_value = NULL;

	/**
	 * This is a hash of messages that correspond to various test result levels.
	 *
	 * There are five messages, each corresponding to one of the result constants
	 * (self::RESULT_OK, self::RESULT_NOTICE, self::RESULT_WARN,
	 * self::RESULT_ERROR, self::RESULT_NOTRUN)
	 *
	 *
	 * @var array
	 */
	protected $_messages = array();

    /**
     * @todo move most properties into this array
     */
	protected $_data = array(


	                        );


	/**
	 * Constructor for Test skeleton class
	 *
	 * @return PhpSecInfo_Test
	 */
	public function __construct() {
		$this->_setMessages();
	    $this->_retrieveCurrentValue();


		//$this->_setMessages();
	}


	/**
	 * Determines whether or not it's appropriate to run this test (for example, if
	 * this test is for a particular library, it shouldn't be run if the lib isn't
	 * loaded).
	 *
	 * This is a terrible name, but I couldn't think of a better one atm.
	 *
	 * @return boolean
	 */
	public abstract function isTestable();


	/**
	 * The "meat" of the test.  This is where the real test code goes.  You should override this when extending
	 *
	 * @return integer
	 */
	protected abstract function _execTest();


	/**
	 * This function loads up result messages into the $this->_messages array.
	 *
	 * Using this method rather than setting $this->_messages directly allows result
	 * messages to be inherited.  This is broken out into a separate function rather
	 * than the constructor for ease of extension purposes (don't have to include a
	 * __construct() method in all extended classes).
	 *
	 */
	protected function _setMessages() {
		$this->setMessageForResult(self::RESULT_OK,		'en', 'This setting should be safe');
		$this->setMessageForResult(self::RESULT_NOTICE,	'en', 'This could potentially be a security issue');
		$this->setMessageForResult(self::RESULT_WARN,	'en', 'This setting may be a serious security problem');
		$this->setMessageForResult(self::RESULT_ERROR,	'en', 'There was an error running this test');
		$this->setMessageForResult(self::RESULT_NOTRUN,	'en', 'This test cannot be run');
	}


	/**
	 * Placeholder - extend for tests
	 */
	protected abstract function _retrieveCurrentValue();



	/**
	 * This is the wrapper that executes the test and sets the result code and message
	 */
	public function test() {
		$result = $this->_execTest();
		$this->_setResult($result);

	}



	/**
	 * Retrieves the result
	 *
	 * @return integer
	 */
	public function getResult() {
		return $this->_result;
	}




	/**
	 * Retrieves the message for the current result
	 *
	 * @return string
	 */
	public function getMessage() {
		if (!isset($this->_message) || empty($this->_message)) {
			$this->_setMessage($this->_result, $this->_language);
		}

		return $this->_message;
	}



	/**
	 * Sets the message for a given result code and language
	 *
	 * <code>
	 * $this->setMessageForResult(self::RESULT_NOTRUN,	'en', 'This test cannot be run');
	 * </code>
	 *
	 * @param integer $result_code
	 * @param string $language_code
	 * @param string $message
	 *
	 */
	protected function setMessageForResult($result_code, $language_code=self::LANG_DEFAULT, $message) {

		if ( !isset($this->_messages[$result_code]) ) {
			$this->_messages[$result_code] = array();
		}

		if ( !is_array($this->_messages[$result_code]) ) {
			$this->_messages[$result_code] = array();
		}

		$this->_messages[$result_code][$language_code] = $message;

	}




	/**
	 * returns the current value.  This function should be used to access the
	 * value for display.  All values are cast as strings
	 *
	 * @return string
	 */
	public function getCurrentTestValue() {
		return $this->getStringValue($this->_current_value);
	}

	/**
	 * returns the recommended value.  This function should be used to access the
	 * value for display.  All values are cast as strings
	 *
	 * @return string
	 */
	public function getRecommendedTestValue() {
		return $this->getStringValue($this->_recommended_value);
	}


	/**
	 * Sets the result code
	 *
	 * @param integer $result_code
	 */
	protected function _setResult($result_code) {
		$this->_result = $result_code;
	}


	/**
	 * Sets the $this->_message variable based on the passed result and language codes
	 *
	 * @param integer $result_code
	 * @param string $language_code
	 */
	protected function _setMessage($result_code, $language_code) {
		$messages = $this->_messages[$result_code];
		$message  = $messages[$language_code];
		$this->_message = $message;
	}


	/**
	 * Returns a link to a page with detailed information about the test
	 *
	 * URL is formatted as self::MOREINFO_BASEURL + testName
	 *
	 * @see self::MOREINFO_BASEURL
	 *
	 * @return string|boolean
	 */
	public function getMoreInfoURL() {
		if ($tn = $this->getTestName()) {
			return self::MOREINFO_BASEURL.strtolower("{$tn}.html");
		} else {
			return false;
		}
	}




	/**
	 * This retrieves the name of this test.
	 *
	 * If a name has not been set, this returns a formatted version of the class name.
	 *
	 * @return string
	 */
	public function getTestName() {
		if (isset($this->_name) && !empty($this->_name)) {
			return $this->_name;
		} else {
			return ucwords(
						str_replace('_', ' ',
							get_class($this)
							)
						);
		}

	}


	/**
	 * sets the test name.  This is private, and intended for loading
	 * data from an external config file (to-do)
	 *
	 * @param string $test_name
	 */
	protected function setTestName($_name) {
		$this->_name = $_name;
	}


	/**
	 * Returns the test group this test belongs to
	 *
	 * @return string
	 */
	public function getTestGroup() {
		return $this->_group;
	}


	/**
	 * sets the test group.  This is private, and intended for loading
	 * data from an external config file (to-do)
	 *
	 * @param string $test_group
	 */
	protected function setTestGroup($_group) {
		$this->_group = $_group;
	}



	/**
	 * This function takes the shorthand notation used in memory limit settings for PHP
	 * and returns the byte value.  Totally stolen from http://us3.php.net/manual/en/function.ini-get.php
	 *
	 * <code>
	 * echo 'post_max_size in bytes = ' . $this->return_bytes(ini_get('post_max_size'));
	 * </code>
	 *
	 * @link http://php.net/manual/en/function.ini-get.php
	 * @param string $val
	 * @return integer
	 */
	protected function returnBytes($val) {
		$val = trim($val);

		if ( (int)$val === 0 ) {
			return 0;
		}

		$last = strtolower($val{strlen($val)-1});
		switch($last) {
		   // The 'G' modifier is available since PHP 5.1.0
		   case 'g':
		       $val *= 1024;
		   case 'm':
		       $val *= 1024;
		   case 'k':
		       $val *= 1024;
		}

		return $val;
	}


	/**
	 * This just does the usual PHP string casting, except for
	 * the boolean FALSE value, where the string "0" is returned
	 * instead of an empty string
	 *
	 * @param mixed $val
	 * @return string
	 */
	protected function getStringValue($val) {
		if ($val === FALSE || !isset($val)) {
			return "0";
		} else {
			return (string)$val;
		}
	}


	/**
	 * This method converts the several possible return values from
	 * allegedly "boolean" ini settings to proper booleans
	 *
	 * Properly converted input values are: 'off', 'on', 'false', 'true', '', '0', '1'
	 * (the last two might not be neccessary, but I'd rather be safe)
	 *
	 * If the ini_value doesn't match any of those, the value is returned as-is.
	 *
	 * @param string $ini_key   the ini_key you need the value of
	 * @return boolean|mixed
	 */
	protected function getIniValue($ini_key) {

		$ini_val = ini_get($ini_key);

		switch ( strtolower($ini_val) ) {

			case 'off':
				return false;
				break;
			case 'on':
				return true;
				break;
			case 'false':
				return false;
				break;
			case 'true':
				return true;
				break;
			case '0':
				return false;
				break;
			case '1':
				return true;
				break;
			case '':
				return false;
				break;
			default:
				return $ini_val;

		}

	}

	/**
	 * sys_get_temp_dir provides some temp dir detection capability
	 * that is lacking in versions of PHP that do not have the
	 * sys_get_temp_dir() function
	 *
	 * @return string|NULL
	 */
	public function sys_get_temp_dir() {
		// Try to get from environment variable
		if ( !empty($_ENV['TMP']) ) {
		   return realpath( $_ENV['TMP'] );
		} else if ( !empty($_ENV['TMPDIR']) ) {
		   return realpath( $_ENV['TMPDIR'] );
		} else if ( !empty($_ENV['TEMP']) ) {
		   return realpath( $_ENV['TEMP'] );
		} else {
			return NULL;
		}
	}


	/**
	 * A quick function to determine whether we're running on Windows.
	 * Uses the PHP_OS constant.
	 *
	 * @return boolean
	 */
	public function osIsWindows() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			return true;
		} else {
			return false;
		}
	}


}