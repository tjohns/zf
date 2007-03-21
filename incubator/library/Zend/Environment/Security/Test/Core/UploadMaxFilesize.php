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
require_once 'Zend/Environment/Security/Test/Core.php';


/**
 * Test Class for upload_max_filesize
 *
 * @package Zend_Environment
 */
class Zend_Environment_Security_Test_Core_UploadMaxFilesize extends Zend_Environment_Security_Test_Core
{


    /**
	 * This should be a <b>unique</b>, human-readable identifier for this test
	 *
	 * @var string
	 */
    protected $_name = "upload_max_filesize";

    protected $_recommended_value = 262144;

    protected function _retrieveCurrentValue() {
        $this->_current_value =  $this->returnBytes(ini_get('upload_max_filesize'));
    }

    /**
	 * Check to see if the post_max_size setting is enabled.
	 */
    protected function _execTest() {

        if ($this->_current_value
        && $this->_current_value <= $this->_recommended_value
        && $post_max_size != -1) {
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

        $this->setMessageForResult(self::RESULT_OK, 'en', 'upload_max_filesize is enabled, and appears to be a relatively low value.');
        $this->setMessageForResult(self::RESULT_NOTICE, 'en', 'upload_max_filesize is not enabled, or is set to a high value.  Are you sure your apps require uploading files of this size?  If not, lower the limit, as large file uploads can impact server performance');
    }


}