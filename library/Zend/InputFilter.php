<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE,
 * and is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not
 * receive a copy of the Zend Framework license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@zend.com so we can mail you a copy immediately.
 *
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc.
 *             (http://www.zend.com/)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework
 *             License version 1.0
 */

/**
 * Zend_InputFilter_Exception
 */
require_once 'Zend/InputFilter/Exception.php';

/**
 * Zend_Filter
 */
require_once 'Zend/Filter.php';

/**
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc.
 *             (http://www.zend.com/)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework
 *             License version 1.0
 */
class Zend_InputFilter
{
    protected $_source = NULL;

    public function __construct(&$source = NULL, $strict = TRUE)
    {
        $this->_source = $source;

        if ($strict) {
            $source = NULL;
        }
    }

    /**
     * Returns only the alphabetic characters in value.
     *
     * @param mixed $key
     * @return mixed
     */
    public function getAlpha($key)
    {
        return Zend_Filter::getAlpha($this->_source[$key]);
    }

    /**
     * Returns only the alphabetic characters and digits in value.
     *
     * @param mixed $key
     * @return mixed
     */
    public function getAlnum($key)
    {
        return Zend_Filter::getAlnum($this->_source[$key]);
    }

    /**
     * Returns only the digits in value. This differs from getInt().
     *
     * @param mixed $key
     * @return mixed
     */
    public function getDigits($key)
    {
        return Zend_Filter::getDigits($this->_source[$key]);
    }

    /**
     * Returns dirname(value).
     *
     * @param mixed $key
     * @return mixed
     */
    public function getDir($key)
    {
        return Zend_Filter::getDir($this->_source[$key]);
    }

    /**
     * Returns (int) value.
     *
     * @param mixed $key
     * @return int
     */
    public function getInt($key)
    {
        return Zend_Filter::getInt($this->_source[$key]);
    }

    /**
     * Returns first $length characters of value.
     *
     * @param mixed $key
     * @param int $length
     * @return mixed
     */
    public function getLength($key, $length = NULL)
    {
        return Zend_Filter::getLength($this->_source[$key], $length);
    }

    /**
     * Returns realpath(value).
     *
     * @param mixed $key
     * @return mixed
     */
    public function getPath($key)
    {
        return Zend_Filter::getPath($this->_source[$key]);
    }

    /**
     * Returns value.
     *
     * @param string $key
     * @return mixed
     */
    public function getRaw($key)
    {
        return $this->_source[$key];
    }

    /**
     * Returns value if every character is alphabetic or a digit,
     * FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isAlnum($key)
    {
        return Zend_Filter::isAlnum($this->_source[$key]);
    }

    /**
     * Returns value if every character is alphabetic, FALSE
     * otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isAlpha($key)
    {
        return Zend_Filter::isAlpha($this->_source[$key]);
    }

    /**
     * Returns value if it is greater than or equal to $min and less
     * than or equal to $max, FALSE otherwise. If $inc is set to
     * FALSE, then the value must be strictly greater than $min and
     * strictly less than $max.
     *
     * @param mixed $key
     * @param mixed $min
     * @param mixed $max
     * @param boolean $inclusive
     * @return mixed
     */
    public function isBetween($key, $min = NULL, $max = NULL, $inc = TRUE)
    {
        return Zend_Filter::isBetween($this->_source[$key], $min, $max, $inc);
    }

    /**
     * Returns value if it is a valid credit card number format. The
     * optional second argument allows developers to indicate the
     * type.
     *
     * @param mixed $key
     * @param mixed $type
     * @return mixed
     */
    public function isCcnum($key, $type = NULL)
    {
        return Zend_Filter::isCcnum($this->_source[$key], $type);
    }

    /**
     * Returns $value if it is a valid date, FALSE otherwise. The
     * date is required to be in ISO 8601 format.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isDate($key)
    {
        return Zend_Filter::isDate($this->_source[$key]);
    }

    /**
     * Returns value if every character is a digit, FALSE otherwise.
     * This is just like isInt(), except there is no upper limit.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isDigits($key)
    {
        return Zend_Filter::isDigits($this->_source[$key]);
    }

    /**
     * Returns value if it is a valid email format, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isEmail($key)
    {
        return Zend_Filter::isEmail($this->_source[$key]);
    }

    /**
     * Returns value if it is a valid float value, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isFloat($key)
    {
        return Zend_Filter::isFloat($this->_source[$key]);
    }

    /**
     * Returns value if it is greater than $min, FALSE otherwise.
     *
     * @param mixed $key
     * @param mixed $min
     * @return mixed
     */
    public function isGreaterThan($key, $min = NULL)
    {
        return Zend_Filter::isGreaterThan($this->_source[$key], $min);
    }

    /**
     * Returns value if it is a valid hexadecimal format, FALSE
     * otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isHex($key)
    {
        return Zend_Filter::isHex($this->_source[$key]);
    }

    /**
     * Returns value if it is a valid hostname, FALSE otherwise.
     * Depending upon the value of $allow, Internet domain names, IP
     * addresses, and/or local network names are considered valid.
     * The default is HOST_ALLOW_ALL, which considers all of the
     * above to be valid.
     *
     * @param mixed $key
     * @param integer $allow bitfield for HOST_ALLOW_DNS, HOST_ALLOW_IP, HOST_ALLOW_LOCAL
     * @throws Zend_Filter_Exception
     * @return mixed
     */
    public function isHostname($key, $allow = Zend_Filter::HOST_ALLOW_ALL)
    {
        return Zend_Filter::isHostname($this->_source[$key], $allow);
    }

    /**
     * Returns value if it is a valid integer value, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isInt($key)
    {
        return Zend_Filter::isInt($this->_source[$key]);
    }

    /**
     * Returns value if it is a valid IP format, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isIp($key)
    {
        return Zend_Filter::isIp($this->_source[$key]);
    }

    /**
     * Returns value if its length is greater than $min and less than
     * $max, FALSE otherwise.
     *
     * @param mixed $key
     * @param mixed $min
     * @param mixed $max
     * @return mixed
     */
    public function isLength($key, $min = NULL, $max = NULL)
    {
        return Zend_Filter::noPath($this->_source[$key], $min, $max);
    }

    /**
     * Returns value if it is less than $max, FALSE otherwise.
     *
     * @param mixed $key
     * @param mixed $max
     * @return mixed
     */
    public function isLessThan($key, $max = NULL)
    {
        return Zend_Filter::isLessThan($this->_source[$key], $max);
    }

    /**
     * Returns value if it is a valid format for a person's name,
     * FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isName($key)
    {
        return Zend_Filter::isName($this->_source[$key]);
    }

    /**
     * Returns value if it is one of $allowed, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isOneOf($key, $allowed = NULL)
    {
        return Zend_Filter::isOneOf($this->_source[$key], $allowed);
    }

    /**
     * Returns value if it is a valid phone number format, FALSE
     * otherwise. The optional second argument indicates the country.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isPhone($key, $country = 'US')
    {
        return Zend_Filter::isPhone($this->_source[$key], $country);
    }

    /**
     * Returns value if it matches $pattern, FALSE otherwise. Uses
     * preg_match() for the matching.
     *
     * @param mixed $key
     * @param mixed $pattern
     * @return mixed
     */
    public function isRegex($key, $pattern = NULL)
    {
        return Zend_Filter::isRegex($this->_source[$key], $pattern);
    }

    public function isUri($key)
    {
        return Zend_Filter::isUri($this->_source[$key]);
    }

    /**
     * Returns value if it is a valid US ZIP, FALSE otherwise.
     *
     * @param mixed $key
     * @return mixed
     */
    public function isZip($key)
    {
        return Zend_Filter::isZip($this->_source[$key]);
    }

    /**
     * Returns value with all tags removed.
     *
     * @param mixed $key
     * @return mixed
     */
    public function noTags($key)
    {
        return Zend_Filter::noTags($this->_source[$key]);
    }

    /**
     * Returns basename(value).
     *
     * @param mixed $key
     * @return mixed
     */
    public function noPath($key)
    {
        return Zend_Filter::noPath($this->_source[$key]);
    }
}
