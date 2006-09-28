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
 * @subpackage Zend_Measure_Acceleration
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Acceleration extends Zend_Measure_Abstract
{
    // Acceleration definitions
    const STANDARD = 'Acceleration::METER_PER_SQUARE_SECOND';

    const CENTIGAL                     = 'Acceleration::CENTIGAL';                 // Metric
    const CENTIMETER_PER_SQUARE_SECOND = 'Acceleration::CENTIMETER_PER_SQUARE_SECOND'; // Metric
    const DECIGAL                      = 'Acceleration::DECIGAL';                  // Metric
    const DECIMETER_PER_SQUARE_SECOND  = 'Acceleration::DECIMETER_PER_SQUARE_SECOND';  // Metric
    const DEKAMETER_PER_SQUARE_SECOND  = 'Acceleration::DEKAMETER_PER_SQUARE_SECOND';  // Metric
    const FOOT_PER_SQUARE_SECOND       = 'Acceleration::FOOT_PER_SQUARE_SECOND';       // US
    const G                            = 'Acceleration::G';                        // Gravity
    const GAL                          = 'Acceleration::GAL';                      // Metric = 1cm/s²
    const GALILEO                      = 'Acceleration::GALILEO';                  // Metric = 1cm/s²
    const GRAV                         = 'Acceleration::GRAV';                     // Gravity
    const HECTOMETER_PER_SQUARE_SECOND = 'Acceleration::HECTOMETER_PER_SQUARE_SECOND'; // Metric
    const INCH_PER_SQUARE_SECOND       = 'Acceleration::INCH_PER_SQUARE_SECOND';       // US
    const KILOMETER_PER_HOUR_SECOND    = 'Acceleration::KILOMETER_PER_HOUR_SECOND';    // Metric
    const KILOMETER_PER_SQUARE_SECOND  = 'Acceleration::KILOMETER_PER_SQUARE_SECOND';  // Metric
    const METER_PER_SQUARE_SECOND      = 'Acceleration::METER_PER_SQUARE_SECOND';      // Metric
    const MILE_PER_HOUR_MINUTE         = 'Acceleration::MILE_PER_HOUR_MINUTE';         // US
    const MILE_PER_HOUR_SECOND         = 'Acceleration::MILE_PER_HOUR_SECOND';         // US
    const MILE_PER_SQUARE_SECOND       = 'Acceleration::MILE_PER_SQUARE_SECOND';       // US
    const MILLIGAL                     = 'Acceleration::MILLIGAL';                 // Metric
    const MILLIMETER_PER_SQUARE_SECOND = 'Acceleration::MILLIMETER_PER_SQUARE_SECOND'; // Metric

    private static $_UNITS = array(
        'Acceleration::CENTIGAL'                     => array(0.0001,   'cgal'),
        'Acceleration::CENTIMETER_PER_SQUARE_SECOND' => array(0.01,     'cm/s²'),
        'Acceleration::DECIGAL'                      => array(0.001,    'dgal'),
        'Acceleration::DECIMETER_PER_SQUARE_SECOND'  => array(0.1,      'dm/s²'),
        'Acceleration::DEKAMETER_PER_SQUARE_SECOND'  => array(10,       'dam/s²'),
        'Acceleration::FOOT_PER_SQUARE_SECOND'       => array(0.3048,   'ft/s²'),
        'Acceleration::G'                            => array(9.80665,  'g'),
        'Acceleration::GAL'                          => array(0.01,     'gal'),
        'Acceleration::GALILEO'                      => array(0.01,     'gal'),
        'Acceleration::GRAV'                         => array(9.80665,  'g'),
        'Acceleration::HECTOMETER_PER_SQUARE_SECOND' => array(100,      'h/s²'),
        'Acceleration::INCH_PER_SQUARE_SECOND'       => array(0.0254,   'in/s²'),
        'Acceleration::KILOMETER_PER_HOUR_SECOND'    => array(array('' => 5,'/' => 18), 'km/h²'),
        'Acceleration::KILOMETER_PER_SQUARE_SECOND'  => array(1000,     'km/s²'),
        'Acceleration::METER_PER_SQUARE_SECOND'      => array(1,        'm/s²'),
        'Acceleration::MILE_PER_HOUR_MINUTE'         => array(array('' => 22, '/' => 15, '*' => 0.3048, '/' => 60), 'mph/m'),
        'Acceleration::MILE_PER_HOUR_SECOND'         => array(array('' => 22, '/' => 15, '*' => 0.3048), 'mph/s'),
        'Acceleration::MILE_PER_SQUARE_SECOND'       => array(1609.344, 'mi/s²'),
        'Acceleration::MILLIGAL'                     => array(0.00001,  'mgal'),
        'Acceleration::MILLIMETER_PER_SQUARE_SECOND' => array(0.001,    'mm/s²')
    );

    private $_Locale;

    /**
     * Zend_Measure_Acceleration provides an locale aware class for
     * conversion and formatting of acceleration values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Acceleration Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Acceleration Type
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
            self::throwException('unknown type of acceleration:' . $type);
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
            self::throwException('unknown type of acceleration:' . $type);
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