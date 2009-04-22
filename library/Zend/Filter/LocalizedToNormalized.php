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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Locale/Format.php';

/**
 * Encrypts a given string
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_LocalizedToNormalized implements Zend_Filter_Interface
{
    /**
     * Set options
     */
    protected $_options = array(
        'locale'      => null,
        'date_format' => null,
        'precision'   => null
    );

    /**
     * Class constructor
     *
     * @param string|Zend_Locale $locale (Optional) Locale to set
     */
    public function __construct($options = null)
    {
        $this->setOptions($options);
    }

    /**
     * Returns the set options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets options to use
     *
     * @param  array $options (Optional) Options to use
     * @return Zend_Filter_LocalizedToNormalized
     */
    public function setOptions($options = null)
    {
        $this->_options = $options + $this->_options;
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Normalizes the given input
     *
     * @param  string $value Value to normalized
     * @return string|array The normalized value
     */
    public function filter($value)
    {
        if (($this->_options['date_format'] === null) && (strpos($value, ':') !== false)) {
            // Special case, no date format specified, detect time input
            return Zend_Locale_Format::getTime($value, $this->_options);
        } else if (Zend_Locale_Format::checkDateFormat($value, $this->_options)) {
            // Detect date or time input
            return Zend_Locale_Format::getDate($value, $this->_options);
        } else if (($this->_options['precision'] === 0) && Zend_Locale_Format::isInteger($value, $this->_options)) {
            // Detect integer
            return Zend_Locale_Format::getInteger($value, $this->_options);
        } else if (($this->_options['precision'] === null) && Zend_Locale_Format::isFloat($value, $this->_options)) {
            // Detect float
            return Zend_Locale_Format::getFloat($value, $this->_options);
        } else if (Zend_Locale_Format::isNumber($value, $this->_options)) {
            // Detect all other numbers
            return Zend_Locale_Format::getNumber($value, $this->_options);
        }

        return $value;
    }
}
