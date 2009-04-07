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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Identical.php 8118 2008-02-18 16:10:32Z matthew $
 */

/** Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Identical extends Zend_Validate_Abstract
{
    /**#@+
     * Error codes
     * @const string
     */
    const NOT_SAME      = 'notSame';
    const MISSING_TOKEN = 'missingToken';
    /**#@-*/

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_SAME      => 'Tokens do not match',
        self::MISSING_TOKEN => 'No token was provided to match against',
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $_token;

    /**
     * Original element against which to validate
     * @var string
     */
    protected $_element;

    /**
     * Sets validator options
     *
     * @param  array|string $token
     * @param  array|string $element
     * @return void
     */
    public function __construct($token = null, $element = null)
    {
        if (null !== $token) {
            $this->setToken($token);
        }

        if (null !== $element) {
            $this->setElement($element);
        }
    }

    /**
     * Set token against which to compare
     *
     * @param  array|string $token
     * @return Zend_Validate_Identical
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    /**
     * Retrieve token
     *
     * @return array|string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Set element against which to compare
     *
     * @param  array|string $element
     * @return Zend_Validate_Identical
     */
    public function setElement($element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve element name
     *
     * @return array|string
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token. When element is set, only this element of an array
     * will be validated
     *
     * @param  array|string $value
     * @param  array|string $element
     * @return boolean
     */
    public function isValid($value, $element = null)
    {
        $this->_setValue($value);
        $token   = $this->getToken();
        $element = ($element === null) ? $this->_element : $element;

        if (empty($token) && empty($element)) {
            $this->_error(self::MISSING_TOKEN);
            return false;
        }

        if ($element === null) {
            if (serialize($value) === serialize($token)) {
                return true;
            }
        } else {
            if (is_array($value) && is_array($element)) {
                if (serialize($value) === serialize($element)) {
                    return true;
                }
            } else if (is_string($value) && is_string($element)) {
                if ($value === $element) {
                    return true;
                }
            } else if (is_string($value) && is_array($element)) {
                if (isset($element[$token]) && ($element[$token] === $value)) {
                    return true;
                }
            }
        }

        $this->_error(self::NOT_SAME);
        return false;
    }
}
