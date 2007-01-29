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
 * Test Class for upload_tmp_dir
 *
 * @package Zend_Environment
 */
class Zend_Environment_Security_Test_Core_UploadTmpDir extends Zend_Environment_Security_Test_Core
{

	/**
	 * This should be a <b>unique</b>, human-readable identifier for this test
	 *
	 * @var string
	 */
	protected $test_name = "upload_tmp_dir";

	protected $recommended_value = "A non-world readable/writable directory";

	protected function _retrieveCurrentValue() {
		$this->current_value =  ini_get('upload_tmp_dir');

		if( empty($this->current_value) ) {
			if (function_exists("sys_get_temp_dir")) {
		    	$this->current_value = sys_get_temp_dir();
			} else {
				$this->current_value = $this->sys_get_temp_dir();
			}
		}
	}

	/**
	 * We are disabling this function on Windows OSes right now until
	 * we can be certain of the proper way to check world-readability
	 *
	 * @return unknown
	 */
	public function isTestable() {
		if ($this->osIsWindows()) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Check if upload_tmp_dir matches self::COMMON_TMPDIR, or is word-writable
	 *
	 * This is still unix-specific, and it's possible that for now
	 * this test should be disabled under Windows builds.
	 *
	 * @see self::COMMON_TMPDIR
	 */
	protected function _execTest() {

		$perms = fileperms($this->current_value);

		if ($this->current_value
			&& !preg_match("|".self::COMMON_TMPDIR."/?|", $this->current_value)
			&& ! ($perms & 0x0004)
			&& ! ($perms & 0x0002) ) {
			return self::RESULT_OK;
		}

		// rewrite current_value to display perms
		$this->current_value .= " (".substr(sprintf('%o', $perms), -4).")";

		return self::RESULT_NOTICE;
	}


	/**
	 * Set the messages specific to this test
	 *
	 */
	protected function _setMessages() {
		parent::_setMessages();

		$this->setMessageForResult(self::RESULT_NOTRUN, 'en', 'Test not run -- currently disabled on Windows OSes');
		$this->setMessageForResult(self::RESULT_OK, 'en', 'upload_tmp_dir is enabled, which is the
						recommended setting. Make sure your upload_tmp_dir path is not world-readable');
		$this->setMessageForResult(self::RESULT_NOTICE, 'en', 'upload_tmp_dir is disabled, or is set to a
						common world-writable directory.  This typically allows other users on this server
						to access temporary copies of files uploaded via your PHP scripts.  You should set
						upload_tmp_dir to a non-world-readable directory');
	}

}