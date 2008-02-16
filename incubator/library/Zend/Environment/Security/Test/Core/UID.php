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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */

/**
 * require the Zend_Environment_Security_Test_Core class
 */
require_once 'Zend/Environment/Security/Test/Core.php';


/**
 * Test class for UID
 *
 * @package Zend_Environment
 */
class Zend_Environment_Security_Test_Core_Uid extends Zend_Environment_Security_Test_Core
{

    /**
	 * This should be a <b>unique</b>, human-readable identifier for this test
	 *
	 * @var string
	 */
    protected $_name = "user_id";

    protected $_recommended_value = 100;

    /**
	 * This test only works under Unix OSes
	 *
	 * @return boolean
	 */
    public function isTestable() {
        if (Zend_Environment_Security_Test::osIsWindows()) {
            return false;
        }
        return true;
    }


    protected function _retrieveCurrentValue() {
        $id = $this->getUnixId();
        $this->current_value = $id['uid'];
    }

    /**
	 * Checks the GID of the PHP process to make sure it is above PHPSECINFO_MIN_SAFE_UID
	 *
	 * @see PHPSECINFO_MIN_SAFE_UID
	 */
    protected function _execTest() {

        if ($this->_current_value >= $this->_recommended_value) {
            return self::RESULT_OK;
        }

        return self::RESULT_WARN;
    }


    /**
	 * Set the messages specific to this test
	 *
	 */
    protected function _setMessages() {
        parent::_setMessages();

        $this->setMessageForResult(self::RESULT_OK, 'en', 'PHP is executing as what is probably a non-privileged user');
        $this->setMessageForResult(self::RESULT_WARN, 'en', 'PHP may be executing as a "privileged" user,
				which could be a serious security vulnerability.');
    }


}