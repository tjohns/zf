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
 * @subpackage Zend_Measure_Speed
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Speed extends Zend_Measure_Abstract
{
    // Speed definitions
    const STANDARD = 'Speed::METER_SECOND';

    const BENZ                       = 'Speed::BENZ';
    const CENTIMETER_DAY             = 'Speed::CENTIMETER_DAY';
    const CENTIMETER_HOUR            = 'Speed::CENTIMETER_HOUR';
    const CENTIMETER_MINUTE          = 'Speed::CENTIMETER_MINUTE';
    const CENTIMETER_SECOND          = 'Speed::CENTIMETER_SECOND';
    const DEKAMETER_DAY              = 'Speed::DEKAMETER_DAY';
    const DEKAMETER_HOUR             = 'Speed::DEKAMETER_HOUR';
    const DEKAMETER_MINUTE           = 'Speed::DEKAMETER_MINUTE';
    const DEKAMETER_SECOND           = 'Speed::DEKAMETER_SECOND';
    const FOOT_DAY                   = 'Speed::FOOT_DAY';
    const FOOT_HOUR                  = 'Speed::FOOT_HOUR';
    const FOOT_MINUTE                = 'Speed::FOOT_MINUTE';
    const FOOT_SECOND                = 'Speed::FOOT_SECOND';
    const FURLONG_DAY                = 'Speed::FURLONG_DAY';
    const FURLONG_FORTNIGHT          = 'Speed::FURLONG_FORTNIGHT';
    const FURLONG_HOUR               = 'Speed::FURLONG_HOUR';
    const FURLONG_MINUTE             = 'Speed::FURLONG_MINUTE';
    const FURLONG_SECOND             = 'Speed::FURLONG_SECOND';
    const HECTOMETER_DAY             = 'Speed::HECTOMETER_DAY';
    const HECTOMETER_HOUR            = 'Speed::HECTOMETER_HOUR';
    const HECTOMETER_MINUTE          = 'Speed::HECTOMETER_MINUTE';
    const HECTOMETER_SECOND          = 'Speed::HECTOMETER_SECOND';
    const INCH_DAY                   = 'Speed::INCH_DAY';
    const INCH_HOUR                  = 'Speed::INCH_HOUR';
    const INCH_MINUTE                = 'Speed::INCH_MINUTE';
    const INCH_SECOND                = 'Speed::INCH_SECOND';
    const KILOMETER_DAY              = 'Speed::KILOMETER_DAY';
    const KILOMETER_HOUR             = 'Speed::KILOMETER_HOUR';
    const KILOMETER_MINUTE           = 'Speed::KILOMETER_MINUTE';
    const KILOMETER_SECOND           = 'Speed::KILOMETER_SECOND';
    const KNOT                       = 'Speed::KNOT';
    const LEAGUE_DAY                 = 'Speed::LEAGUE_DAY';
    const LEAGUE_HOUR                = 'Speed::LEAGUE_HOUR';
    const LEAGUE_MINUTE              = 'Speed::LEAGUE_MINUTE';
    const LEAGUE_SECOND              = 'Speed::LEAGUE_SECOND';
    const MACH                       = 'Speed::MACH';
    const MEGAMETER_DAY              = 'Speed::MEGAMETER_DAY';
    const MEGAMETER_HOUR             = 'Speed::MEGAMETER_HOUR';
    const MEGAMETER_MINUTE           = 'Speed::MEGAMETER_MINUTE';
    const MEGAMETER_SECOND           = 'Speed::MEGAMETER_SECOND';
    const METER_DAY                  = 'Speed::METER_DAY';
    const METER_HOUR                 = 'Speed::METER_HOUR';
    const METER_MINUTE               = 'Speed::METER_MINUTE';
    const METER_SECOND               = 'Speed::METER_SECOND';
    const MILE_DAY                   = 'Speed::MILE_DAY';
    const MILE_HOUR                  = 'Speed::MILE_HOUR';
    const MILE_MINUTE                = 'Speed::MILE_MINUTE';
    const MILE_SECOND                = 'Speed::MILE_SECOND';
    const MILLIMETER_DAY             = 'Speed::MILLIMETER_DAY';
    const MILLIMETER_HOUR            = 'Speed::MILLIMETER_HOUR';
    const MILLIMETER_MINUTE          = 'Speed::MILLIMETER_MINUTE';
    const MILLIMETER_SECOND          = 'Speed::MILLIMETER_SECOND';
    const MILLIMETER_MICROSECOND     = 'Speed::MILLIMETER_MICROSECOND';
    const MILLIMETER_100_MICROSECOND = 'Speed::MILLIMETER_100_MICROSECOND';
    const NAUTIC_MILE_DAY            = 'Speed::NAUTIC_MILE_DAY';
    const NAUTIC_MILE_HOUR           = 'Speed::NAUTIC_MILE_HOUR';
    const NAUTIC_MILE_MINUTE         = 'Speed::NAUTIC_MILE_MINUTE';
    const NAUTIC_MILE_SECOND         = 'Speed::NAUTIC_MILE_SECOND';
    const LIGHTSPEED_AIR             = 'Speed::LIGHTSPEED_AIR';
    const LIGHTSPEED_GLASS           = 'Speed::LIGHTSPEED_GLASS';
    const LIGHTSPEED_ICE             = 'Speed::LIGHTSPEED_ICE';
    const LIGHTSPEED_VACUUM          = 'Speed::LIGHTSPEED_VACUUM';
    const LIGHTSPEED_WATER           = 'Speed::LIGHTSPEED_WATER';
    const SOUNDSPEED_AIR             = 'Speed::SOUNDSPEED_AIT';
    const SOUNDSPEED_METAL           = 'Speed::SOUNDSPEED_METAL';
    const SOUNDSPEED_WATER           = 'Speed::SOUNDSPEED_WATER';
    const YARD_DAY                   = 'Speed::YARD_DAY';
    const YARD_HOUR                  = 'Speed::YARD_HOUR';
    const YARD_MINUTE                = 'Speed::YARD_MINUTE';
    const YARD_SECOND                = 'Speed::YARD_SECOND';

    private static $_UNITS = array(
        'Speed::BENZ'                       => array(1,'Bz'),
        'Speed::CENTIMETER_DAY'             => array(array('' => 0.01, '/' => 86400),'cm/day'),
        'Speed::CENTIMETER_HOUR'            => array(array('' => 0.01, '/' => 3600),'cm/h'),
        'Speed::CENTIMETER_MINUTE'          => array(array('' => 0.01, '/' => 60),'cm/min'),
        'Speed::CENTIMETER_SECOND'          => array(0.01,'cd/sec'),
        'Speed::DEKAMETER_DAY'              => array(array('' => 10, '/' => 86400),'dam/day'),
        'Speed::DEKAMETER_HOUR'             => array(array('' => 10, '/' => 3600),'dam/h'),
        'Speed::DEKAMETER_MINUTE'           => array(array('' => 10, '/' => 60),'dam/min'),
        'Speed::DEKAMETER_SECOND'           => array(10,'dam/sec'),
        'Speed::FOOT_DAY'                   => array(array('' => 0.3048, '/' => 86400),'ft/day'),
        'Speed::FOOT_HOUR'                  => array(array('' => 0.3048, '/' => 3600),'ft/h'),
        'Speed::FOOT_MINUTE'                => array(array('' => 0.3048, '/' => 60),'ft/min'),
        'Speed::FOOT_SECOND'                => array(0.3048,'ft/sec'),
        'Speed::FURLONG_DAY'                => array(array('' => 201.1684, '/' => 86400),'fur/day'),
        'Speed::FURLONG_FORTNIGHT'          => array(array('' => 201.1684, '/' => 1209600),'fur/fortnight'),
        'Speed::FURLONG_HOUR'               => array(array('' => 201.1684, '/' => 3600),'fur/h'),
        'Speed::FURLONG_MINUTE'             => array(array('' => 201.1684, '/' => 60),'fur/min'),
        'Speed::FURLONG_SECOND'             => array(201.1684,'fur/sec'),
        'Speed::HECTOMETER_DAY'             => array(array('' => 100, '/' => 86400),'hm/day'),
        'Speed::HECTOMETER_HOUR'            => array(array('' => 100, '/' => 3600),'hm/h'),
        'Speed::HECTOMETER_MINUTE'          => array(array('' => 100, '/' => 60),'hm/min'),
        'Speed::HECTOMETER_SECOND'          => array(100,'hm/sec'),
        'Speed::INCH_DAY'                   => array(array('' => 0.0254, '/' => 86400),'in/day'),
        'Speed::INCH_HOUR'                  => array(array('' => 0.0254, '/' => 3600),'in/h'),
        'Speed::INCH_MINUTE'                => array(array('' => 0.0254, '/' => 60),'in/min'),
        'Speed::INCH_SECOND'                => array(0.0254,'in/sec'),
        'Speed::KILOMETER_DAY'              => array(array('' => 1000, '/' => 86400),'km/day'),
        'Speed::KILOMETER_HOUR'             => array(array('' => 1000, '/' => 3600),'km/h'),
        'Speed::KILOMETER_MINUTE'           => array(array('' => 1000, '/' => 60),'km/min'),
        'Speed::KILOMETER_SECOND'           => array(1000,'km/sec'),
        'Speed::KNOT'                       => array(array('' => 1852, '/' => 3600),'kn'),
        'Speed::LEAGUE_DAY'                 => array(array('' => 4828.0417, '/' => 86400),'league/day'),
        'Speed::LEAGUE_HOUR'                => array(array('' => 4828.0417, '/' => 3600),'league/h'),
        'Speed::LEAGUE_MINUTE'              => array(array('' => 4828.0417, '/' => 60),'league/min'),
        'Speed::LEAGUE_SECOND'              => array(4828.0417,'league/sec'),
        'Speed::MACH'                       => array(340.29,'M'),
        'Speed::MEGAMETER_DAY'              => array(array('' => 1000000, '/' => 86400),'Mm/day'),
        'Speed::MEGAMETER_HOUR'             => array(array('' => 1000000, '/' => 3600),'Mm/h'),
        'Speed::MEGAMETER_MINUTE'           => array(array('' => 1000000, '/' => 60),'Mm/min'),
        'Speed::MEGAMETER_SECOND'           => array(1000000,'Mm/sec'),
        'Speed::METER_DAY'                  => array(array('' => 1, '/' => 86400),'m/day'),
        'Speed::METER_HOUR'                 => array(array('' => 1, '/' => 3600),'m/h'),
        'Speed::METER_MINUTE'               => array(array('' => 1, '/' => 60),'m/min'),
        'Speed::METER_SECOND'               => array(1,'m/s'),
        'Speed::MILE_DAY'                   => array(array('' => 1609.344, '/' => 86400),'mi/day'),
        'Speed::MILE_HOUR'                  => array(array('' => 1609.344, '/' => 3600),'mi/h'),
        'Speed::MILE_MINUTE'                => array(array('' => 1609.344, '/' => 60),'mi/min'),
        'Speed::MILE_SECOND'                => array(1609.344,'mi/sec'),
        'Speed::MILLIMETER_DAY'             => array(array('' => 0.001, '/' => 86400),'mm/day'),
        'Speed::MILLIMETER_HOUR'            => array(array('' => 0.001, '/' => 3600),'mm/h'),
        'Speed::MILLIMETER_MINUTE'          => array(array('' => 0.001, '/' => 60),'mm/min'),
        'Speed::MILLIMETER_SECOND'          => array(0.001,'mm/sec'),
        'Speed::MILLIMETER_MICROSECOND'     => array(1000,'mm/µsec'),
        'Speed::MILLIMETER_100_MICROSECOND' => array(10,'mm/100µsec'),
        'Speed::NAUTIC_MILE_DAY'            => array(array('' => 1852, '/' => 86400),'nmi/day'),
        'Speed::NAUTIC_MILE_HOUR'           => array(array('' => 1852, '/' => 3600),'nmi/h'),
        'Speed::NAUTIC_MILE_MINUTE'         => array(array('' => 1852, '/' => 60),'nmi/min'),
        'Speed::NAUTIC_MILE_SECOND'         => array(1852,'nmi/sec'),
        'Speed::LIGHTSPEED_AIR'             => array(299702547,'speed of light (air)'),
        'Speed::LIGHTSPEED_GLASS'           => array(199861638,'speed of light (glass)'),
        'Speed::LIGHTSPEED_ICE'             => array(228849204,'speed of light (ice)'),
        'Speed::LIGHTSPEED_VACUUM'          => array(299792458,'speed of light (vacuum)'),
        'Speed::LIGHTSPEED_WATER'           => array(225407863,'speed of light (water)'),
        'Speed::SOUNDSPEED_AIT'             => array(340.29,'speed of sound (air)'),
        'Speed::SOUNDSPEED_METAL'           => array(5000,'speed of sound (metal)'),
        'Speed::SOUNDSPEED_WATER'           => array(1500,'speed of sound (water)'),
        'Speed::YARD_DAY'                   => array(array('' => 0.9144, '/' => 86400),'yd/day'),
        'Speed::YARD_HOUR'                  => array(array('' => 0.9144, '/' => 3600),'yd/h'),
        'Speed::YARD_MINUTE'                => array(array('' => 0.9144, '/' => 60),'yd/min'),
        'Speed::YARD_SECOND'                => array(0.9144,'yd/sec')
    );

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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Speed Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale)
    {
        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty(self::$_UNITS[$type]))
            self::throwException('unknown type of speed:'.$type);
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
            self::throwException('unknown type of speed:'.$type);

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