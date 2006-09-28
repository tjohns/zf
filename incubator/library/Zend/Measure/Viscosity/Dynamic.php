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
 * @subpackage Zend_Measure_Viscosity_Dynamic
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Viscosity_Dynamic extends Zend_Measure_Abstract
{
    // Viscosity_Dynamic definitions
    const STANDARD = 'Viscosity_Dynamic::KILOGRAM_PER_METER_SECOND';

    const CENTIPOISE           = 'Viscosity_Dynamic::CENTIPOISE';
    const DECIPOISE            = 'Viscosity_Dynamic::DECIPOISE';
    const DYNE_SECOND_PER_SQUARE_CENTIMETER       = 'Viscosity_Dynamic::DYNE_SECOND_PER_SQUARE_CENTIMETER';
    const GRAM_FORCE_SECOND_PER_SQUARE_CENTIMETER = 'Viscosity_Dynamic::GRAM_FORCE_SECOND_PER_SQUARE_CENTIMETER';
    const GRAM_PER_CENTIMETER_SECOND              = 'Viscosity_Dynamic::GRAM_PER_CENTIMETER_SECOND';
    const KILOGRAM_FORCE_SECOND_PER_SQUARE_METER  = 'Viscosity_Dynamic::KILOGRAM_FORCE_SECOND_PER_SQUARE_METER';
    const KILOGRAM_PER_METER_HOUR    = 'Viscosity_Dynamic::KILOGRAM_PER_METER_HOUR';
    const KILOGRAM_PER_METER_SECOND  = 'Viscosity_Dynamic::KILOGRAM_PER_METER_SECOND';
    const MILLIPASCAL_SECOND   = 'Viscosity_Dynamic::MILLIPASCAL_SECOND';
    const MILLIPOISE           = 'Viscosity_Dynamic::MILLIPOISE';
    const NEWTON_SECOND_PER_SQUARE_METER = 'Viscosity_Dynamic::NEWTON_SECOND_PER_SQUARE_METER';
    const PASCAL_SECOND        = 'Viscosity_Dynamic::PASCAL_SECOND';
    const POISE                = 'Viscosity_Dynamic::POISE';
    const POISEUILLE           = 'Viscosity_Dynamic::POISEUILLE';
    const POUND_FORCE_SECOND_PER_SQUARE_FEET = 'Viscosity_Dynamic::POUND_FORCE_SECOND_PER_SQUARE_FEET';
    const POUND_FORCE_SECOND_PER_SQUARE_INCH = 'Viscosity_Dynamic::POUND_FORCE_SECOND_PER_SQUARE_INCH';
    const POUND_PER_FOOT_HOUR                = 'Viscosity_Dynamic::POUND_PER_FOOT_HOUR';
    const POUND_PER_FOOT_SECOND              = 'Viscosity_Dynamic::POUND_PER_FOOT_SECOND';
    const POUNDAL_HOUR_PER_SQUARE_FOOT       = 'Viscosity_Dynamic::POUNDAL_HOUR_PER_SQUARE_FOOT';
    const POUNDAL_SECOND_PER_SQUARE_FOOT     = 'Viscosity_Dynamic::POUNDAL_SECOND_PER_SQUARE_FOOT';
    const REYN                 = 'Viscosity_Dynamic::REYN';
    const SLUG_PER_FOOT_SECOND = 'Viscosity_Dynamic::SLUG_PER_FOOT_SECOND';
    const LBFS_PER_SQUARE_FOOT = 'Viscosity_Dynamic::LBFS_PER_SQUARE_FOOT';
    const NS_PER_SQUARE_METER  = 'Viscosity_Dynamic::NS_PER_SQUARE_METER';
    const WATER_20C            = 'Viscosity_Dynamic::WATER_20C';
    const WATER_40C            = 'Viscosity_Dynamic::WATER_40C';
    const HEAVY_OIL_20C        = 'Viscosity_Dynamic::HEAVY_OIL_20C';
    const HEAVY_OIL_40C        = 'Viscosity_Dynamic::HEAVY_OIL_40C';
    const GLYCERIN_20C         = 'Viscosity_Dynamic::GLYCERIN_20C';
    const GLYCERIN_40C         = 'Viscosity_Dynamic::GLYCERIN_40C';
    const SAE_5W_MINUS18C      = 'Viscosity_Dynamic::SAE_5W_MINUS18C';
    const SAE_10W_MINUS18C     = 'Viscosity_Dynamic::SAE_10W_MINUS18C';
    const SAE_20W_MINUS18C     = 'Viscosity_Dynamic::SAE_20W_MINUS18C';
    const SAE_5W_99C           = 'Viscosity_Dynamic::SAE_5W_99C';
    const SAE_10W_99C          = 'Viscosity_Dynamic::SAE_10W_99C';
    const SAE_20W_99C          = 'Viscosity_Dynamic::SAE_20W_99C';

    private static $_UNITS = array(
        'Viscosity_Dynamic::CENTIPOISE'          => array(0.001,      'cP'),
        'Viscosity_Dynamic::DECIPOISE'           => array(0.01,       'dP'),
        'Viscosity_Dynamic::DYNE_SECOND_PER_SQUARE_CENTIMETER'       => array(0.1,     'dyn s/cm²'),
        'Viscosity_Dynamic::GRAM_FORCE_SECOND_PER_SQUARE_CENTIMETER' => array(98.0665, 'gf s/cm²'),
        'Viscosity_Dynamic::GRAM_PER_CENTIMETER_SECOND'              => array(0.1,     'g/cm s'),
        'Viscosity_Dynamic::KILOGRAM_FORCE_SECOND_PER_SQUARE_METER'  => array(9.80665, 'kgf s/m²'),
        'Viscosity_Dynamic::KILOGRAM_PER_METER_HOUR'    => array(array('' => 1, '/' => 3600), 'kg/m h'),
        'Viscosity_Dynamic::KILOGRAM_PER_METER_SECOND'  => array(1,   'kg/ms'),
        'Viscosity_Dynamic::MILLIPASCAL_SECOND'  => array(0.001,      'mPa s'),
        'Viscosity_Dynamic::MILLIPOISE'          => array(0.0001,     'mP'),
        'Viscosity_Dynamic::NEWTON_SECOND_PER_SQUARE_METER' => array(1, 'N s/m²'),
        'Viscosity_Dynamic::PASCAL_SECOND'       => array(1,          'Pa s'),
        'Viscosity_Dynamic::POISE'               => array(0.1,        'P'),
        'Viscosity_Dynamic::POISEUILLE'          => array(1,          'Pl'),
        'Viscosity_Dynamic::POUND_FORCE_SECOND_PER_SQUARE_FEET' => array(47.880259,  'lbf s/ft²'),
        'Viscosity_Dynamic::POUND_FORCE_SECOND_PER_SQUARE_INCH' => array(6894.75729, 'lbf s/in²'),
        'Viscosity_Dynamic::POUND_PER_FOOT_HOUR' => array(0.00041337887,             'lb/ft h'),
        'Viscosity_Dynamic::POUND_PER_FOOT_SECOND'          => array(1.4881639,      'lb/ft s'),
        'Viscosity_Dynamic::POUNDAL_HOUR_PER_SQUARE_FOOT'   => array(0.00041337887,  'pdl h/ft²'),
        'Viscosity_Dynamic::POUNDAL_SECOND_PER_SQUARE_FOOT' => array(1.4881639,      'pdl s/ft²'),
        'Viscosity_Dynamic::REYN'                => array(6894.75729, 'reyn'),
        'Viscosity_Dynamic::SLUG_PER_FOOT_SECOND'=> array(47.880259,  'slug/ft s'),
        'Viscosity_Dynamic::WATER_20C'           => array(0.001,      'water (20°)'),
        'Viscosity_Dynamic::WATER_40C'           => array(0.00065,    'water (40°)'),
        'Viscosity_Dynamic::HEAVY_OIL_20C'       => array(0.45,       'oil (20°)'),
        'Viscosity_Dynamic::HEAVY_OIL_40C'       => array(0.11,       'oil (40°)'),
        'Viscosity_Dynamic::GLYCERIN_20C'        => array(1.41,       'glycerin (20°)'),
        'Viscosity_Dynamic::GLYCERIN_40C'        => array(0.284,      'glycerin (40°)'),
        'Viscosity_Dynamic::SAE_5W_MINUS18C'     => array(1.2,        'SAE 5W (-18°)'),
        'Viscosity_Dynamic::SAE_10W_MINUS18C'    => array(2.4,        'SAE 10W (-18°)'),
        'Viscosity_Dynamic::SAE_20W_MINUS18C'    => array(9.6,        'SAE 20W (-18°)'),
        'Viscosity_Dynamic::SAE_5W_99C'          => array(0.0039,     'SAE 5W (99°)'),
        'Viscosity_Dynamic::SAE_10W_99C'         => array(0.0042,     'SAE 10W (99°)'),
        'Viscosity_Dynamic::SAE_20W_99C'         => array(0.0057,     'SAE 20W (99°)')
    );

    private $_Locale;

    /**
     * Zend_Measure_Viscosity_Dynamic provides an locale aware class for
     * conversion and formatting of viscosity-dynamic values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Viscosity_Dynamic Type
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
    public function equals( $object )
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Viscosity_Dynamic Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty( $locale )) {
            $locale = $this->_Locale;
        }

        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty( self::$_UNITS[$type] )) {
            self::throwException('unknown type of dynamic viscosity:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType( $type );
    }


    /**
     * Set a new type, and convert the value
     *
     * @param $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType( $type )
    {
        if (empty( self::$_UNITS[$type] )) {
            self::throwException('unknown type of dynamic viscosity:'.$type);
        }

        // Convert to standard value
        $value = parent::getValue();
        if (is_array(self::$_UNITS[parent::getType()][0])) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ( $key ) {
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
                switch ( $key ) {
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
        parent::setType( $type );
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