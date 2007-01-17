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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Validate_Interface
 */
require_once 'Zend/Validate/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Date implements Zend_Validate_Interface
{
    /**
     * Date format option
     *
     * Date type CLDR format to parse. Only single-letter codes (H, m, s, y, M, d), and MMMM and EEEE are supported.
     *
     * @var string
     */
    protected $_format;

    /**
     * Date locale option
     *
     * @var Zend_Locale
     */
    protected $_locale;

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Sets validator options
     *
     * @param  string             $format
     * @param  Zend_Locale|string $locale
     * @return void
     */
    public function __construct($format = null, $locale = null)
    {
        $this->setFormat($format);
        $this->setLocale($locale);
    }

    /**
     * Returns the format option
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Sets the format option
     *
     * @param  string $format
     * @return Zend_Validate_Date Provides a fluent interface
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Sets the locale option
     *
     * @param  mixed $locale
     * @return Zend_Validate_Date Provides a fluent interface
     */
    public function setLocale($locale)
    {
        /**
         * @see Zend_Locale
         */
        require_once 'Zend/Locale.php';
        if (!Zend_Locale::isLocale($locale)) {
            $locale = new Zend_Locale();
            $this->_locale = $locale->toString();
        } else {
            $this->_locale = $locale;
        }

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid date
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();
        do {
            if (is_numeric($value)) {
                break;
            }
            /**
             * @see Zend_Locale_Format
             */
            require_once 'Zend/Locale/Format.php';
            if (Zend_Locale_Format::isDate($value, $this->_format, $this->_locale)) {
                break;
            }
            $this->_messages[] = "'$value' does not appear to be a valid date";
            return false;
        } while (false);
        return true;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
