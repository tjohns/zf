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
    const STANDARD = 'Acceleration::METER_SQUARE_SECOND';

    const CENTIGAL                 = 'Acceleration::CENTIGAL';                 // Metric
    const CENTIMETER_SQUARE_SECOND = 'Acceleration::CENTIMETER_SQUARE_SECOND'; // Metric
    const DECIGAL                  = 'Acceleration::DECIGAL';                  // Metric
    const DECIMETER_SQUARE_SECOND  = 'Acceleration::DECIMETER_SQUARE_SECOND';  // Metric
    const DEKAMETER_SQUARE_SECOND  = 'Acceleration::DEKAMETER_SQUARE_SECOND';  // Metric
    const FOOT_SQUARE_SECOND       = 'Acceleration::FOOT_SQUARE_SECOND';       // US
    const G                        = 'Acceleration::G';                        // Gravity
    const GAL                      = 'Acceleration::GAL';                      // Metric = 1cm/s²
    const GALILEO                  = 'Acceleration::GALILEO';                  // Metric = 1cm/s²
    const GRAV                     = 'Acceleration::GRAV';                     // Gravity
    const HECTOMETER_SQUARE_SECOND = 'Acceleration::HECTOMETER_SQUARE_SECOND'; // Metric
    const INCH_SQUARE_SECOND       = 'Acceleration::INCH_SQUARE_SECOND';       // US
    const KILOMETER_HOUR_SECOND    = 'Acceleration::KILOMETER_HOUR_SECOND';    // Metric
    const KILOMETER_SQUARE_SECOND  = 'Acceleration::KILOMETER_SQUARE_SECOND';  // Metric
    const METER_SQUARE_SECOND      = 'Acceleration::METER_SQUARE_SECOND';      // Metric
    const MILE_HOUR_MINUTE         = 'Acceleration::MILE_HOUR_MINUTE';         // US
    const MILE_HOUR_SECOND         = 'Acceleration::MILE_HOUR_SECOND';         // US
    const MILE_SQUARE_SECOND       = 'Acceleration::MILE_SQUARE_SECOND';       // US
    const MILLIGAL                 = 'Acceleration::MILLIGAL';                 // Metric
    const MILLIMETER_SQUARE_SECOND = 'Acceleration::MILLIMETER_SQUARE_SECOND'; // Metric

    private static $_UNITS = array(
        'Acceleration::CENTIGAL'                 => array(0.0001,'cgal'),
        'Acceleration::CENTIMETER_SQUARE_SECOND' => array(0.01,'cm/s²'),
        'Acceleration::DECIGAL'                  => array(0.001,'dgal'),
        'Acceleration::DECIMETER_SQUARE_SECOND'  => array(0.1,'dm/s²'),
        'Acceleration::DEKAMETER_SQUARE_SECOND'  => array(10,'dam/s²'),
        'Acceleration::FOOT_SQUARE_SECOND'       => array(0.3048,'ft/s²'),
        'Acceleration::G'                        => array(9.80665,'g'),
        'Acceleration::GAL'                      => array(0.01,'gal'),
        'Acceleration::GALILEO'                  => array(0.01,'gal'),
        'Acceleration::GRAV'                     => array(9.80665,'g'),
        'Acceleration::HECTOMETER_SQUARE_SECOND' => array(100,'h/s²'),
        'Acceleration::INCH_SQUARE_SECOND'       => array(0.0254,'in/s²'),
        'Acceleration::KILOMETER_HOUR_SECOND'    => array(array('' => 5,'/' => 18),'km/h²'),
        'Acceleration::KILOMETER_SQUARE_SECOND'  => array(1000,'km/s²'),
        'Acceleration::METER_SQUARE_SECOND'      => array(1,'m/s²'),
        'Acceleration::MILE_HOUR_MINUTE'         => array(array('' => 22, '/' => 15, '*' => 0.3048, '/' => 60),'mph/m'),
        'Acceleration::MILE_HOUR_SECOND'         => array(array('' => 22, '/' => 15, '*' => 0.3048),'mph/s'),
        'Acceleration::MILE_SQUARE_SECOND'       => array(1609.344,'mi/s²'),
        'Acceleration::MILLIGAL'                 => array(0.00001,'mgal/s²'),
        'Acceleration::MILLIMETER_SQUARE_SECOND' => array(0.001,'mm/s²')
    );

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
    public function __construct($value, $type, $locale)
    {
        $this->setValue($value, $type, $locale);
    }


    /**
     * Compare if the value and type is equal
     *
     * @return boolean
     */
    public function equals( Object $object )
    {
        if ($object->toString() == $this->toString())
        {
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
    public function setValue($value, $type, $locale)
    {
        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty(self::$_UNITS[$type]))
            self::throwException('unknown type of acceleration:'.$type);
        parent::setValue($value);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type]))
            self::throwException('unknown type of acceleration:'.$type);

        // Convert to standard value
        $value = parent::getValue();
        if (is_array(self::$_UNITS[parent::getType()][0])) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value /= $found;
                        break;
                    case "*":
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
                        $value /= $found;
                        break;
                    case "*":
                        $value *= $found;
                        break;
                    default:
                        $value *= $found;
                        break;
                }
            }
        } else {
            $value = $value * (self::$_UNITS[$type][0]);
        }
        parent::setValue($value);
        parent::setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return parent::getValue().' '.self::$_UNITS[parent::getType()][1];
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
}