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
 * require the Zend_Environment_Security_Test_Session class
 */
require_once 'Zend/Environment/Security/Test/Session.php';


class Zend_Environment_Security_Test_Session_UseTransSid extends Zend_Environment_Security_Test_Session
{

    /**
	 * This should be a <b>unique</b>, human-readable identifier for this test
	 *
	 * @var string
	 */
    protected $_name = "use_trans_sid";


    protected $_recommended_value = FALSE;


    protected function _retrieveCurrentValue() {
        $this->_current_value = $this->getIniValue('session.use_trans_sid');
    }


    /**
	 * Checks to see if allow_url_fopen is enabled
	 *
	 */
    protected function _execTest() {
        if ($this->_current_value == $this->_recommended_value) {
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

        $this->setMessageForResult(self::RESULT_OK, 'en', 'use_trans_sid is disabled, which is the recommended setting');
        $this->setMessageForResult(self::RESULT_NOTICE, 'en', 'use_trans_sid is enabled.  This makes session hijacking easier.  Consider disabling this feature');

    }


}