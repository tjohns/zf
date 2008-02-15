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
 * Zend_Environment_Security_Test_CGI abstract
 */
require_once 'Zend/Environment/Security/Test/CGI.php';

class Zend_Environment_Security_Test_CGI_ForceRedirect extends Zend_Environment_Security_Test_CGI
{

    protected $_name = "force_redirect";

    protected $_recommended_value = TRUE;

    protected function _retrieveCurrentValue() {
        $this->_current_value = $this->getIniValue('cgi.force_redirect');
    }


    /**
	 * Checks to see if cgi.force_redirect is enabled
	 * @return integer
	 *
	 */
    protected function _execTest() {

        if ($this->_current_value == $this->_recommended_value) {
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
        $this->setMessageForResult(self::RESULT_OK, 'en', "force_redirect is enabled, which is the recommended setting");
        $this->setMessageForResult(self::RESULT_WARN, 'en', "force_redirect is disabled.  In most cases, this is a <strong>serious</strong> security vulnerability.  Unless you are absolutely sure this is not needed, enable this setting");
    }

}