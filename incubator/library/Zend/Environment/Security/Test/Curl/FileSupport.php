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
 * Zend_Environment_Security_Test_Curl abstract
 */
require_once('Zend/Environment/Security/Test/Curl.php');


class Zend_Environment_Security_Test_Curl_FileSupport extends Zend_Environment_Security_Test_Curl
{

	protected $_name = "file_support";

	protected $_recommended_value = '5.1.6+ or 4.4.4+';


	protected function _retrieveCurrentValue() {
		$this->_current_value = PHP_VERSION;
	}


	/**
	 * Checks to see if libcurl's "file://" support is enabled by examining the "protocols" array
	 * in the info returned from curl_version()
	 * @return integer
	 *
	 */
	protected function _execTest() {

		$curlinfo = curl_version();

		if ( version_compare($this->_current_value, '5.1.6', '>=') ||
			(version_compare($this->_current_value, '4.4.4', '>=')) && ( version_compare($this->_current_value, '5', '<') )
			) {
			return self::RESULT_OK;
		} else {
			return self::RESULT_WARN;
		}

	}



	/**
	 * Set the messages specific to this test
	 *
	 */
	protected function _setMessages() {
	    parent::_setMessages();
	    $this->setMessageForResult(self::RESULT_OK, 'en', "You are running PHP 4.4.4 or higher, or PHP 5.1.6 or higher.  These versions fix the security hole present in the cURL functions that allow it to bypass safe_mode and open_basedir restrictions.");
		$this->setMessageForResult(self::RESULT_WARN, 'en', "A security hole present in your version of PHP allows the cURL functions to bypass safe_mode and open_basedir restrictions.  You should upgrade to the latest version of PHP.");
	}

}