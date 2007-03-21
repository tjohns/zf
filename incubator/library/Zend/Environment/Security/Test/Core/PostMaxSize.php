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
 * Test Class for post_max_size
 *
 * @package Zend_Environment
 */
class Zend_Environment_Security_Test_Core_PostMaxSize extends Zend_Environment_Security_Test_Core
{

    /**
	 * This should be a <b>unique</b>, human-readable identifier for this test
	 *
	 * @var string
	 */
    protected $_name = "post_max_size";

    protected $_recommended_value = 262144;

    protected function _retrieveCurrentValue() {
        $this->_current_value =  $this->returnBytes(ini_get('post_max_size'));
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

        $this->setMessageForResult(self::RESULT_OK, 'en', 'post_max_size is enabled, and appears to
				be a relatively low value');
        $this->setMessageForResult(self::RESULT_NOTICE, 'en', 'post_max_size is not enabled, or is set to
				a high value.  Allowing a large value may open up your server to denial-of-service attacks');
    }


}