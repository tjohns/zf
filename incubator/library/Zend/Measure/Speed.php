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
 * @subpackage Zend_Measure_Speed
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Speed extends Zend_Measure_Abstract
{
    // Speed definitions
    const STANDARD = 'Speed::METER_PER_SECOND';

    const BENZ                       = 'Speed::BENZ';
    const CENTIMETER_PER_DAY         = 'Speed::CENTIMETER_PER_DAY';
    const CENTIMETER_PER_HOUR        = 'Speed::CENTIMETER_PER_HOUR';
    const CENTIMETER_PER_MINUTE      = 'Speed::CENTIMETER_PER_MINUTE';
    const CENTIMETER_PER_SECOND      = 'Speed::CENTIMETER_PER_SECOND';
    const DEKAMETER_PER_DAY          = 'Speed::DEKAMETER_PER_DAY';
    const DEKAMETER_PER_HOUR         = 'Speed::DEKAMETER_PER_HOUR';
    const DEKAMETER_PER_MINUTE       = 'Speed::DEKAMETER_PER_MINUTE';
    const DEKAMETER_PER_SECOND       = 'Speed::DEKAMETER_PER_SECOND';
    const FOOT_PER_DAY               = 'Speed::FOOT_PER_DAY';
    const FOOT_PER_HOUR              = 'Speed::FOOT_PER_HOUR';
    const FOOT_PER_MINUTE            = 'Speed::FOOT_PER_MINUTE';
    const FOOT_PER_SECOND            = 'Speed::FOOT_PER_SECOND';
    const FURLONG_PER_DAY            = 'Speed::FURLONG_PER_DAY';
    const FURLONG_PER_FORTNIGHT      = 'Speed::FURLONG_PER_FORTNIGHT';
    const FURLONG_PER_HOUR           = 'Speed::FURLONG_PER_HOUR';
    const FURLONG_PER_MINUTE         = 'Speed::FURLONG_PER_MINUTE';
    const FURLONG_PER_SECOND         = 'Speed::FURLONG_PER_SECOND';
    const HECTOMETER_PER_DAY         = 'Speed::HECTOMETER_PER_DAY';
    const HECTOMETER_PER_HOUR        = 'Speed::HECTOMETER_PER_HOUR';
    const HECTOMETER_PER_MINUTE      = 'Speed::HECTOMETER_PER_MINUTE';
    const HECTOMETER_PER_SECOND      = 'Speed::HECTOMETER_PER_SECOND';
    const INCH_PER_DAY               = 'Speed::INCH_PER_DAY';
    const INCH_PER_HOUR              = 'Speed::INCH_PER_HOUR';
    const INCH_PER_MINUTE            = 'Speed::INCH_PER_MINUTE';
    const INCH_PER_SECOND            = 'Speed::INCH_PER_SECOND';
    const KILOMETER_PER_DAY          = 'Speed::KILOMETER_PER_DAY';
    const KILOMETER_PER_HOUR         = 'Speed::KILOMETER_PER_HOUR';
    const KILOMETER_PER_MINUTE       = 'Speed::KILOMETER_PER_MINUTE';
    const KILOMETER_PER_SECOND       = 'Speed::KILOMETER_PER_SECOND';
    const KNOT                       = 'Speed::KNOT';
    const LEAGUE_PER_DAY             = 'Speed::LEAGUE_PER_DAY';
    const LEAGUE_PER_HOUR            = 'Speed::LEAGUE_PER_HOUR';
    const LEAGUE_PER_MINUTE          = 'Speed::LEAGUE_PER_MINUTE';
    const LEAGUE_PER_SECOND          = 'Speed::LEAGUE_PER_SECOND';
    const MACH                       = 'Speed::MACH';
    const MEGAMETER_PER_DAY          = 'Speed::MEGAMETER_PER_DAY';
    const MEGAMETER_PER_HOUR         = 'Speed::MEGAMETER_PER_HOUR';
    const MEGAMETER_PER_MINUTE       = 'Speed::MEGAMETER_PER_MINUTE';
    const MEGAMETER_PER_SECOND       = 'Speed::MEGAMETER_PER_SECOND';
    const METER_PER_DAY              = 'Speed::METER_PER_DAY';
    const METER_PER_HOUR             = 'Speed::METER_PER_HOUR';
    const METER_PER_MINUTE           = 'Speed::METER_PER_MINUTE';
    const METER_PER_SECOND           = 'Speed::METER_PER_SECOND';
    const MILE_PER_DAY               = 'Speed::MILE_PER_DAY';
    const MILE_PER_HOUR              = 'Speed::MILE_PER_HOUR';
    const MILE_PER_MINUTE            = 'Speed::MILE_PER_MINUTE';
    const MILE_PER_SECOND            = 'Speed::MILE_PER_SECOND';
    const MILLIMETER_PER_DAY         = 'Speed::MILLIMETER_PER_DAY';
    const MILLIMETER_PER_HOUR        = 'Speed::MILLIMETER_PER_HOUR';
    const MILLIMETER_PER_MINUTE      = 'Speed::MILLIMETER_PER_MINUTE';
    const MILLIMETER_PER_SECOND      = 'Speed::MILLIMETER_PER_SECOND';
    const MILLIMETER_PER_MICROSECOND = 'Speed::MILLIMETER_PER_MICROSECOND';
    const MILLIMETER_PER_100_MICROSECOND = 'Speed::MILLIMETER_PER_100_MICROSECOND';
    const NAUTIC_MILE_PER_DAY        = 'Speed::NAUTIC_MILE_PER_DAY';
    const NAUTIC_MILE_PER_HOUR       = 'Speed::NAUTIC_MILE_PER_HOUR';
    const NAUTIC_MILE_PER_MINUTE     = 'Speed::NAUTIC_MILE_PER_MINUTE';
    const NAUTIC_MILE_PER_SECOND     = 'Speed::NAUTIC_MILE_PER_SECOND';
    const LIGHTSPEED_AIR             = 'Speed::LIGHTSPEED_AIR';
    const LIGHTSPEED_GLASS           = 'Speed::LIGHTSPEED_GLASS';
    const LIGHTSPEED_ICE             = 'Speed::LIGHTSPEED_ICE';
    const LIGHTSPEED_VACUUM          = 'Speed::LIGHTSPEED_VACUUM';
    const LIGHTSPEED_WATER           = 'Speed::LIGHTSPEED_WATER';
    const SOUNDSPEED_AIR             = 'Speed::SOUNDSPEED_AIT';
    const SOUNDSPEED_METAL           = 'Speed::SOUNDSPEED_METAL';
    const SOUNDSPEED_WATER           = 'Speed::SOUNDSPEED_WATER';
    const YARD_PER_DAY               = 'Speed::YARD_PER_DAY';
    const YARD_PER_HOUR              = 'Speed::YARD_PER_HOUR';
    const YARD_PER_MINUTE            = 'Speed::YARD_PER_MINUTE';
    const YARD_PER_SECOND            = 'Speed::YARD_PER_SECOND';

    private static $_UNITS = array(
        'Speed::BENZ'                           => array(1,                                     'Bz'),
        'Speed::CENTIMETER_PER_DAY'             => array(array('' => 0.01, '/' => 86400),       'cm/day'),
        'Speed::CENTIMETER_PER_HOUR'            => array(array('' => 0.01, '/' => 3600),        'cm/h'),
        'Speed::CENTIMETER_PER_MINUTE'          => array(array('' => 0.01, '/' => 60),          'cm/m'),
        'Speed::CENTIMETER_PER_SECOND'          => array(0.01,                                  'cd/s'),
        'Speed::DEKAMETER_PER_DAY'              => array(array('' => 10, '/' => 86400),         'dam/day'),
        'Speed::DEKAMETER_PER_HOUR'             => array(array('' => 10, '/' => 3600),          'dam/h'),
        'Speed::DEKAMETER_PER_MINUTE'           => array(array('' => 10, '/' => 60),            'dam/m'),
        'Speed::DEKAMETER_PER_SECOND'           => array(10,                                    'dam/s'),
        'Speed::FOOT_PER_DAY'                   => array(array('' => 0.3048, '/' => 86400),     'ft/day'),
        'Speed::FOOT_PER_HOUR'                  => array(array('' => 0.3048, '/' => 3600),      'ft/h'),
        'Speed::FOOT_PER_MINUTE'                => array(array('' => 0.3048, '/' => 60),        'ft/m'),
        'Speed::FOOT_PER_SECOND'                => array(0.3048,                                'ft/s'),
        'Speed::FURLONG_PER_DAY'                => array(array('' => 201.1684, '/' => 86400),   'fur/day'),
        'Speed::FURLONG_PER_FORTNIGHT'          => array(array('' => 201.1684, '/' => 1209600), 'fur/fortnight'),
        'Speed::FURLONG_PER_HOUR'               => array(array('' => 201.1684, '/' => 3600),    'fur/h'),
        'Speed::FURLONG_PER_MINUTE'             => array(array('' => 201.1684, '/' => 60),      'fur/m'),
        'Speed::FURLONG_PER_SECOND'             => array(201.1684,                              'fur/s'),
        'Speed::HECTOMETER_PER_DAY'             => array(array('' => 100, '/' => 86400),        'hm/day'),
        'Speed::HECTOMETER_PER_HOUR'            => array(array('' => 100, '/' => 3600),         'hm/h'),
        'Speed::HECTOMETER_PER_MINUTE'          => array(array('' => 100, '/' => 60),           'hm/m'),
        'Speed::HECTOMETER_PER_SECOND'          => array(100,                                   'hm/s'),
        'Speed::INCH_PER_DAY'                   => array(array('' => 0.0254, '/' => 86400),     'in/day'),
        'Speed::INCH_PER_HOUR'                  => array(array('' => 0.0254, '/' => 3600),      'in/h'),
        'Speed::INCH_PER_MINUTE'                => array(array('' => 0.0254, '/' => 60),        'in/m'),
        'Speed::INCH_PER_SECOND'                => array(0.0254,                                'in/s'),
        'Speed::KILOMETER_PER_DAY'              => array(array('' => 1000, '/' => 86400),       'km/day'),
        'Speed::KILOMETER_PER_HOUR'             => array(array('' => 1000, '/' => 3600),        'km/h'),
        'Speed::KILOMETER_PER_MINUTE'           => array(array('' => 1000, '/' => 60),          'km/m'),
        'Speed::KILOMETER_PER_SECOND'           => array(1000,                                  'km/s'),
        'Speed::KNOT'                           => array(array('' => 1852, '/' => 3600),        'kn'),
        'Speed::LEAGUE_PER_DAY'                 => array(array('' => 4828.0417, '/' => 86400),  'league/day'),
        'Speed::LEAGUE_PER_HOUR'                => array(array('' => 4828.0417, '/' => 3600),   'league/h'),
        'Speed::LEAGUE_PER_MINUTE'              => array(array('' => 4828.0417, '/' => 60),     'league/m'),
        'Speed::LEAGUE_PER_SECOND'              => array(4828.0417,                             'league/s'),
        'Speed::MACH'                           => array(340.29,                                'M'),
        'Speed::MEGAMETER_PER_DAY'              => array(array('' => 1000000, '/' => 86400),    'Mm/day'),
        'Speed::MEGAMETER_PER_HOUR'             => array(array('' => 1000000, '/' => 3600),     'Mm/h'),
        'Speed::MEGAMETER_PER_MINUTE'           => array(array('' => 1000000, '/' => 60),       'Mm/m'),
        'Speed::MEGAMETER_PER_SECOND'           => array(1000000,                               'Mm/s'),
        'Speed::METER_PER_DAY'                  => array(array('' => 1, '/' => 86400),          'm/day'),
        'Speed::METER_PER_HOUR'                 => array(array('' => 1, '/' => 3600),           'm/h'),
        'Speed::METER_PER_MINUTE'               => array(array('' => 1, '/' => 60),             'm/m'),
        'Speed::METER_PER_SECOND'               => array(1,                                     'm/s'),
        'Speed::MILE_PER_DAY'                   => array(array('' => 1609.344, '/' => 86400),   'mi/day'),
        'Speed::MILE_PER_HOUR'                  => array(array('' => 1609.344, '/' => 3600),    'mi/h'),
        'Speed::MILE_PER_MINUTE'                => array(array('' => 1609.344, '/' => 60),      'mi/m'),
        'Speed::MILE_PER_SECOND'                => array(1609.344,                              'mi/s'),
        'Speed::MILLIMETER_PER_DAY'             => array(array('' => 0.001, '/' => 86400),      'mm/day'),
        'Speed::MILLIMETER_PER_HOUR'            => array(array('' => 0.001, '/' => 3600),       'mm/h'),
        'Speed::MILLIMETER_PER_MINUTE'          => array(array('' => 0.001, '/' => 60),         'mm/m'),
        'Speed::MILLIMETER_PER_SECOND'          => array(0.001,                                 'mm/s'),
        'Speed::MILLIMETER_PER_MICROSECOND'     => array(1000,                                  'mm/µs'),
        'Speed::MILLIMETER_PER_100_MICROSECOND' => array(10,                                    'mm/100µs'),
        'Speed::NAUTIC_MILE_PER_DAY'            => array(array('' => 1852, '/' => 86400),       'nmi/day'),
        'Speed::NAUTIC_MILE_PER_HOUR'           => array(array('' => 1852, '/' => 3600),        'nmi/h'),
        'Speed::NAUTIC_MILE_PER_MINUTE'         => array(array('' => 1852, '/' => 60),          'nmi/m'),
        'Speed::NAUTIC_MILE_PER_SECOND'         => array(1852,                                  'nmi/s'),
        'Speed::LIGHTSPEED_AIR'                 => array(299702547,                             'speed of light (air)'),
        'Speed::LIGHTSPEED_GLASS'               => array(199861638,                             'speed of light (glass)'),
        'Speed::LIGHTSPEED_ICE'                 => array(228849204,                             'speed of light (ice)'),
        'Speed::LIGHTSPEED_VACUUM'              => array(299792458,                             'speed of light (vacuum)'),
        'Speed::LIGHTSPEED_WATER'               => array(225407863,                             'speed of light (water)'),
        'Speed::SOUNDSPEED_AIT'                 => array(340.29,                                'speed of sound (air)'),
        'Speed::SOUNDSPEED_METAL'               => array(5000,                                  'speed of sound (metal)'),
        'Speed::SOUNDSPEED_WATER'               => array(1500,                                  'speed of sound (water)'),
        'Speed::YARD_PER_DAY'                   => array(array('' => 0.9144, '/' => 86400),     'yd/day'),
        'Speed::YARD_PER_HOUR'                  => array(array('' => 0.9144, '/' => 3600),      'yd/h'),
        'Speed::YARD_PER_MINUTE'                => array(array('' => 0.9144, '/' => 60),        'yd/m'),
        'Speed::YARD_PER_SECOND'                => array(0.9144,                                'yd/s')
    );

    private $_Locale;

    /**
     * Zend_Measure_Speed provides an locale aware class for
     * conversion and formatting of Speed values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Speed Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Speed Type
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
            self::throwException('unknown type of speed:' . $type);
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
            self::throwException('unknown type of speed:' . $type);
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