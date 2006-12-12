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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement needed classes
 */
require_once 'Zend.php';
require_once 'Zend/Measure/Abstract.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Viscosity_Kinematic
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Viscosity_Kinematic extends Zend_Measure_Abstract
{
    // Viscosity_Kinematic definitions
    const STANDARD = 'Viscosity_Kinematic::SQUARE_METER_PER_SECOND';

    const CENTISTOKES                  = 'Viscosity_Kinematic::CENTISTOKES';
    const LENTOR                       = 'Viscosity_Kinematic::LENTOR';
    const LITER_PER_CENTIMETER_DAY     = 'Viscosity_Kinematic::LITER_PER_CENTIMETER_DAY';
    const LITER_PER_CENTIMETER_HOUR    = 'Viscosity_Kinematic::LITER_PER_CENTIMETER_HOUR';
    const LITER_PER_CENTIMETER_MINUTE  = 'Viscosity_Kinematic::LITER_PER_CENTIMETER_MINUTE';
    const LITER_PER_CENTIMETER_SECOND  = 'Viscosity_Kinematic::LITER_PER_CENTIMETER_SECOND';
    const POISE_CUBIC_CENTIMETER_PER_GRAM = 'Viscosity_Kinematic::POISE_CUBIC_CENTIMETER_PER_GRAM';
    const SQUARE_CENTIMETER_PER_DAY    = 'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_DAY';
    const SQUARE_CENTIMETER_PER_HOUR   = 'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_HOUR';
    const SQUARE_CENTIMETER_PER_MINUTE = 'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_MINUTE';
    const SQUARE_CENTIMETER_PER_SECOND = 'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_SECOND';
    const SQUARE_FOOT_PER_DAY          = 'Viscosity_Kinematic::SQUARE_FOOT_PER_DAY';
    const SQUARE_FOOT_PER_HOUR         = 'Viscosity_Kinematic::SQUARE_FOOT_PER_HOUR';
    const SQUARE_FOOT_PER_MINUTE       = 'Viscosity_Kinematic::SQUARE_FOOT_PER_MINUTE';
    const SQUARE_FOOT_PER_SECOND       = 'Viscosity_Kinematic::SQUARE_FOOT_PER_SECOND';
    const SQUARE_INCH_PER_DAY          = 'Viscosity_Kinematic::SQUARE_INCH_PER_DAY';
    const SQUARE_INCH_PER_HOUR         = 'Viscosity_Kinematic::SQUARE_INCH_PER_HOUR';
    const SQUARE_INCH_PER_MINUTE       = 'Viscosity_Kinematic::SQUARE_INCH_PER_MINUTE';
    const SQUARE_INCH_PER_SECOND       = 'Viscosity_Kinematic::SQUARE_INCH_PER_SECOND';
    const SQUARE_METER_PER_DAY         = 'Viscosity_Kinematic::SQUARE_METER_PER_DAY';
    const SQUARE_METER_PER_HOUR        = 'Viscosity_Kinematic::SQUARE_METER_PER_HOUR';
    const SQUARE_METER_PER_MINUTE      = 'Viscosity_Kinematic::SQUARE_METER_PER_MINUTE';
    const SQUARE_METER_PER_SECOND      = 'Viscosity_Kinematic::SQUARE_METER_PER_SECOND';
    const SQUARE_MILLIMETER_PER_DAY    = 'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_DAY';
    const SQUARE_MILLIMETER_PER_HOUR   = 'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_HOUR';
    const SQUARE_MILLIMETER_PER_MINUTE = 'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_MINUTE';
    const SQUARE_MILLIMETER_PER_SECOND = 'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_SECOND';
    const STOKES                       = 'Viscosity_Kinematic::STOKES';

    private static $_UNITS = array(
        'Viscosity_Kinematic::CENTISTOKES'                  => array(0.000001,        'cSt'),
        'Viscosity_Kinematic::LENTOR'                       => array(0.0001,          'lentor'),
        'Viscosity_Kinematic::LITER_PER_CENTIMETER_DAY'     => array(array('' => 1, '/' => 864000), 'l/cm day'),
        'Viscosity_Kinematic::LITER_PER_CENTIMETER_HOUR'    => array(array('' => 1, '/' => 36000),  'l/cm h'),
        'Viscosity_Kinematic::LITER_PER_CENTIMETER_MINUTE'  => array(array('' => 1, '/' => 600),    'l/cm m'),
        'Viscosity_Kinematic::LITER_PER_CENTIMETER_SECOND'  => array(0.1,             'l/cm s'),
        'Viscosity_Kinematic::POISE_CUBIC_CENTIMETER_PER_GRAM' => array(0.0001,       'P cm³/g'),
        'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_DAY'    => array(array('' => 1, '/' => 864000000),'cm²/day'),
        'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_HOUR'   => array(array('' => 1, '/' => 36000000),'cm²/h'),
        'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_MINUTE' => array(array('' => 1, '/' => 600000),'cm²/m'),
        'Viscosity_Kinematic::SQUARE_CENTIMETER_PER_SECOND' => array(0.0001,          'cm²/s'),
        'Viscosity_Kinematic::SQUARE_FOOT_PER_DAY'          => array(0.0000010752667, 'ft²/day'),
        'Viscosity_Kinematic::SQUARE_FOOT_PER_HOUR'         => array(0.0000258064,    'ft²/h'),
        'Viscosity_Kinematic::SQUARE_FOOT_PER_MINUTE'       => array(0.001548384048,  'ft²/m'),
        'Viscosity_Kinematic::SQUARE_FOOT_PER_SECOND'       => array(0.09290304,      'ft²/s'),
        'Viscosity_Kinematic::SQUARE_INCH_PER_DAY'          => array(7.4671296e-9,    'in²/day'),
        'Viscosity_Kinematic::SQUARE_INCH_PER_HOUR'         => array(0.00000017921111, 'in²/h'),
        'Viscosity_Kinematic::SQUARE_INCH_PER_MINUTE'       => array(0.000010752667,  'in²/m'),
        'Viscosity_Kinematic::SQUARE_INCH_PER_SECOND'       => array(0.00064516,      'in²/s'),
        'Viscosity_Kinematic::SQUARE_METER_PER_DAY'         => array(array('' => 1, '/' => 86400), 'm²/day'),
        'Viscosity_Kinematic::SQUARE_METER_PER_HOUR'        => array(array('' => 1, '/' => 3600),  'm²/h'),
        'Viscosity_Kinematic::SQUARE_METER_PER_MINUTE'      => array(array('' => 1, '/' => 60),    'm²/m'),
        'Viscosity_Kinematic::SQUARE_METER_PER_SECOND'      => array(1,               'm²/s'),
        'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_DAY'    => array(array('' => 1, '/' => 86400000000), 'mm²/day'),
        'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_HOUR'   => array(array('' => 1, '/' => 3600000000),  'mm²/h'),
        'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_MINUTE' => array(array('' => 1, '/' => 60000000),    'mm²/m'),
        'Viscosity_Kinematic::SQUARE_MILLIMETER_PER_SECOND' => array(0.000001,        'mm²/s'),
        'Viscosity_Kinematic::STOKES'                       => array(0.0001,          'St')
    );

    private $_Locale;

    /**
     * Zend_Measure_Viscosity_Kinematic provides an locale aware class for
     * conversion and formatting of kinematic viscosity values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Viscosity_Kinematic Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Viscosity_Kinematic Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty( $locale )) {
            $locale = $this->_Locale;
        }

        try {
            $value = Zend_Locale_Format::getNumber($value, $locale);
        } catch(Exception $e) {
            throw Zend::exception('Zend_Measure_Exception', $e->getMessage());
        }

        if (empty( self::$_UNITS[$type] )) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of kinematic viscosity:' . $type);
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
        if (empty(self::$_UNITS[$type])) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of kinematic viscosity:' . $type);
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