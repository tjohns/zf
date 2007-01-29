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
 * require the Zend_Environment_Security_Test_Core class
 */
require_once('Zend/Environment/Security/Test/Core.php');

/**
 * Test Class for open_basedir
 * 
 * @package Zend_Environment
 */
class Zend_Environment_Security_Test_Core_OpenBasedir extends Zend_Environment_Security_Test_Core
{

	/**
	 * This should be a <b>unique</b>, human-readable identifier for this test
	 *
	 * @var string
	 */
	protected $test_name = "open_basedir";

	protected $recommended_value = TRUE;

	
	protected function _retrieveCurrentValue() {
		$this->current_value = $this->getIniValue('open_basedir');
	}
	
	
	/**
	 * Checks to see if allow_url_fopen is enabled
	 *
	 */
	protected function _execTest() {
		if ($this->current_value == $this->recommended_value) {
			return self::RESULT_OK;
		}

		return self::RESULT_NOTICE;
	}
		
	
	/**
	 * Set the messages specific to this test
	 *
	 */
	protected function _setMessages() {
		parent::_setMessages();
		
		$this->setMessageForResult(self::RESULT_OK, 'en', 'open_basedir is enabled, which is the
				recommended setting. Keep in mind that other web applications not written in PHP will not
				be restricted by this setting.');
		$this->setMessageForResult(self::RESULT_NOTICE, 'en', 'open_basedir is disabled.  When
					this is enabled, only files that are in the
					given directory/directories and their subdirectories can be read by PHP scripts.
					You should consider turning this on.  Keep in mind that other web applications not
					written in PHP will not be restricted by this setting.');
	}
	

}