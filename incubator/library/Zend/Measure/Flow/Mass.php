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
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement basic abstract class
 */
require_once 'Zend/Measure/Abstract.php';

/**
 * Implement Locale Data and Format class
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Format.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Flow_Mass
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Flow_Mass extends Zend_Measure_Abstract
{
    // Flow_Mass definitions
    const STANDARD = 'Flow_Mass::KILOGRAM_PER_SECOND';

    const CENTIGRAM_PER_DAY    = 'Flow_Mass::CENTIGRAM_PER_DAY';
    const CENTIGRAM_PER_HOUR   = 'Flow_Mass::CENTIGRAM_PER_HOUR';
    const CENTIGRAM_PER_MINUTE = 'Flow_Mass::CENTIGRAM_PER_MINUTE';
    const CENTIGRAM_PER_SECOND = 'Flow_Mass::CENTIGRAM_PER_SECOND';
    const GRAM_PER_DAY         = 'Flow_Mass::GRAM_PER_DAY';
    const GRAM_PER_HOUR        = 'Flow_Mass::GRAM_PER_HOUR';
    const GRAM_PER_MINUTE      = 'Flow_Mass::GRAM_PER_MINUTE';
    const GRAM_PER_SECOND      = 'Flow_Mass::GRAM_PER_SECOND';
    const KILOGRAM_PER_DAY     = 'Flow_Mass::KILOGRAM_PER_DAY';
    const KILOGRAM_PER_HOUR    = 'Flow_Mass::KILOGRAM_PER_HOUR';
    const KILOGRAM_PER_MINUTE  = 'Flow_Mass::KILOGRAM_PER_MINUTE';
    const KILOGRAM_PER_SECOND  = 'Flow_Mass::KILOGRAM_PER_SECOND';
    const MILLIGRAM_PER_DAY    = 'Flow_Mass::MILLIGRAM_PER_DAY';
    const MILLIGRAM_PER_HOUR   = 'Flow_Mass::MILLIGRAM_PER_HOUR';
    const MILLIGRAM_PER_MINUTE = 'Flow_Mass::MILLIGRAM_PER_MINUTE';
    const MILLIGRAM_PER_SECOND = 'Flow_Mass::MILLIGRAM_PER_SECOND';
    const OUNCE_PER_DAY        = 'Flow_Mass::OUNCE_PER_DAY';
    const OUNCE_PER_HOUR       = 'Flow_Mass::OUNCE_PER_HOUR';
    const OUNCE_PER_MINUTE     = 'Flow_Mass::OUNCE_PER_MINUTE';
    const OUNCE_PER_SECOND     = 'Flow_Mass::OUNCE_PER_SECOND';
    const POUND_PER_DAY        = 'Flow_Mass::POUND_PER_DAY';
    const POUND_PER_HOUR       = 'Flow_Mass::POUND_PER_HOUR';
    const POUND_PER_MINUTE     = 'Flow_Mass::POUND_PER_MINUTE';
    const POUND_PER_SECOND     = 'Flow_Mass::POUND_PER_SECOND';
    const TON_LONG_PER_DAY     = 'Flow_Mass::TON_LONG_PER_DAY';
    const TON_LONG_PER_HOUR    = 'Flow_Mass::TON_LONG_PER_HOUR';
    const TON_LONG_PER_MINUTE  = 'Flow_Mass::TON_LONG_PER_MINUTE';
    const TON_LONG_PER_SECOND  = 'Flow_Mass::TON_LONG_PER_SECOND';
    const TON_PER_DAY          = 'Flow_Mass::TON_PER_DAY';
    const TON_PER_HOUR         = 'Flow_Mass::TON_PER_HOUR';
    const TON_PER_MINUTE       = 'Flow_Mass::TON_PER_MINUTE';
    const TON_PER_SECOND       = 'Flow_Mass::TON_PER_SECOND';
    const TON_SHORT_PER_DAY    = 'Flow_Mass::TON_SHORT_PER_DAY';
    const TON_SHORT_PER_HOUR   = 'Flow_Mass::TON_SHORT_PER_HOUR';
    const TON_SHORT_PER_MINUTE = 'Flow_Mass::TON_SHORT_PER_MINUTE';
    const TON_SHORT_PER_SECOND = 'Flow_Mass::TON_SHORT_PER_SECOND';

    private static $_UNITS = array(
        'Flow_Mass::CENTIGRAM_PER_DAY'    => array(array('' => 0.00001, '/' => 86400),    'cg/day'),
        'Flow_Mass::CENTIGRAM_PER_HOUR'   => array(array('' => 0.00001, '/' => 3600),     'cg/h'),
        'Flow_Mass::CENTIGRAM_PER_MINUTE' => array(array('' => 0.00001, '/' => 60),       'cg/m'),
        'Flow_Mass::CENTIGRAM_PER_SECOND' => array(0.00001,                               'cg/s'),
        'Flow_Mass::GRAM_PER_DAY'         => array(array('' => 0.001, '/' => 86400),      'g/day'),
        'Flow_Mass::GRAM_PER_HOUR'        => array(array('' => 0.001, '/' => 3600),       'g/h'),
        'Flow_Mass::GRAM_PER_MINUTE'      => array(array('' => 0.001, '/' => 60),         'g/m'),
        'Flow_Mass::GRAM_PER_SECOND'      => array(0.001,                                 'g/s'),
        'Flow_Mass::KILOGRAM_PER_DAY'     => array(array('' => 1, '/' => 86400),          'kg/day'),
        'Flow_Mass::KILOGRAM_PER_HOUR'    => array(array('' => 1, '/' => 3600),           'kg/h'),
        'Flow_Mass::KILOGRAM_PER_MINUTE'  => array(array('' => 1, '/' => 60),             'kg/m'),
        'Flow_Mass::KILOGRAM_PER_SECOND'  => array(1,                                     'kg/s'),
        'Flow_Mass::MILLIGRAM_PER_DAY'    => array(array('' => 0.000001, '/' => 86400),   'mg/day'),
        'Flow_Mass::MILLIGRAM_PER_HOUR'   => array(array('' => 0.000001, '/' => 3600),    'mg/h'),
        'Flow_Mass::MILLIGRAM_PER_MINUTE' => array(array('' => 0.000001, '/' => 60),      'mg/m'),
        'Flow_Mass::MILLIGRAM_PER_SECOND' => array(0.000001,                              'mg/s'),
        'Flow_Mass::OUNCE_PER_DAY'        => array(array('' => 0.0283495, '/' => 86400),  'oz/day'),
        'Flow_Mass::OUNCE_PER_HOUR'       => array(array('' => 0.0283495, '/' => 3600),   'oz/h'),
        'Flow_Mass::OUNCE_PER_MINUTE'     => array(array('' => 0.0283495, '/' => 60),     'oz/m'),
        'Flow_Mass::OUNCE_PER_SECOND'     => array(0.0283495,                             'oz/s'),
        'Flow_Mass::POUND_PER_DAY'        => array(array('' => 0.453592, '/' => 86400),   'lb/day'),
        'Flow_Mass::POUND_PER_HOUR'       => array(array('' => 0.453592, '/' => 3600),    'lb/h'),
        'Flow_Mass::POUND_PER_MINUTE'     => array(array('' => 0.453592, '/' => 60),      'lb/m'),
        'Flow_Mass::POUND_PER_SECOND'     => array(0.453592,                              'lb/s'),
        'Flow_Mass::TON_LONG_PER_DAY'     => array(array('' => 1016.04608, '/' => 86400), 't/day'),
        'Flow_Mass::TON_LONG_PER_HOUR'    => array(array('' => 1016.04608, '/' => 3600),  't/h'),
        'Flow_Mass::TON_LONG_PER_MINUTE'  => array(array('' => 1016.04608, '/' => 60),    't/m'),
        'Flow_Mass::TON_LONG_PER_SECOND'  => array(1016.04608,                            't/s'),
        'Flow_Mass::TON_PER_DAY'          => array(array('' => 1000, '/' => 86400),       't/day'),
        'Flow_Mass::TON_PER_HOUR'         => array(array('' => 1000, '/' => 3600),        't/h'),
        'Flow_Mass::TON_PER_MINUTE'       => array(array('' => 1000, '/' => 60),          't/m'),
        'Flow_Mass::TON_PER_SECOND'       => array(1000,                                  't/s'),
        'Flow_Mass::TON_SHORT_PER_DAY'    => array(array('' => 907.184, '/' => 86400),    't/day'),
        'Flow_Mass::TON_SHORT_PER_HOUR'   => array(array('' => 907.184, '/' => 3600),     't/h'),
        'Flow_Mass::TON_SHORT_PER_MINUTE' => array(array('' => 907.184, '/' => 60),       't/m'),
        'Flow_Mass::TON_SHORT_PER_SECOND' => array(907.184,                               't/s')
    );

    private $_Locale;

    /**
     * Zend_Measure_Flow_Mass provides an locale aware class for
     * conversion and formatting of Flow_Mass values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Flow_Mass Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type, $locale = false)
    {
        if (empty($locale)) {
            $this->_Locale = new Zend_Locale();
        } else {
            $this->_Locale = $locale;
        }

        $this->setValue($value, $type, $this->_Locale);
    }


    /**
     * Compare if the value and type is equal
     *
     * @param $object  object to compare equality
     * @return boolean
     */
    public function equals($object)
    {
        if ($object->toString() == $this->toString()) {
            return true;
        }

        return false;
    }


    /**
     * Set a new value
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Flow_Mass Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of flow-mass:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @param $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of flow-mass:' . $type);
        }

        // Convert to standard value
        $value = parent::getValue();
        if (is_array(self::$_UNITS[parent::getType()][0])) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value /= $found;
                        break;
                    default:
                        $value *= $found;
                        break;
                }
            }
        } else {
            $value = $value * (self::$_UNITS[parent::getType()][0]);
        }

        // Convert to expected value
        if (is_array(self::$_UNITS[$type][0])) {
            foreach (self::$_UNITS[$type][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value *= $found;
                        break;
                    default:
                        $value /= $found;
                        break;
                }
            }
        } else {
            $value = $value / (self::$_UNITS[$type][0]);
        }

        parent::setValue($value, $type, $this->_Locale);
        parent::setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return parent::getValue() . ' ' . self::$_UNITS[parent::getType()][1];
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }


    /**
     * Returns the conversion list
     * 
     * @return array
     */
    public function getConversionList()
    {
        return self::$_UNITS;
    }
}