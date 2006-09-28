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
 * @subpackage Zend_Measure_Power
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Power extends Zend_Measure_Abstract
{
    // Power definitions
    const STANDARD = 'Power::WATT';

    const ATTOWATT                    = 'Power::ATTOWATT';
    const BTU_PER_HOUR                = 'Power::BTU_PER_HOUR';
    const BTU_PER_MINUTE              = 'Power::BTU_PER_MINUTE';
    const BTU_PER_SECOND              = 'Power::BTU_PER_SECOND';
    const CALORIE_PER_HOUR            = 'Power::CALORIE_PER_HOUR';
    const CALORIE_PER_MINUTE          = 'Power::CALORIE_PER_MINUTE';
    const CALORIE_PER_SECOND          = 'Power::CALORIE_PER_SECOND';
    const CENTIWATT                   = 'Power::CENTIWATT';
    const CHEVAL_VAPEUR               = 'Power::CHEVAL_VAPEUR';
    const CLUSEC                      = 'Power::CLUSEC';
    const DECIWATT                    = 'Power::DECIWATT';
    const DEKAWATT                    = 'Power::DEKAWATT';
    const DYNE_CENTIMETER_PER_HOUR    = 'Power::DYNE_CENTIMETER_PER_HOUR';
    const DYNE_CENTIMETER_PER_MINUTE  = 'Power::DYNE_CENTIMETER_PER_MINUTE';
    const DYNE_CENTIMETER_PER_SECOND  = 'Power::DYNE_CENTIMETER_PER_SECOND';
    const ERG_PER_HOUR                = 'Power::ERG_PER_HOUR';
    const ERG_PER_MINUTE              = 'Power::ERG_PER_MINUTE';
    const ERG_PER_SECOND              = 'Power::ERG_PER_SECOND';
    const EXAWATT                     = 'Power::EXAWATT';
    const FEMTOWATT                   = 'Power::FEMTOWATT';
    const FOOT_POUND_FORCE_PER_HOUR   = 'Power::FOOT_POUND_FORCE_PER_HOUR';
    const FOOT_POUND_FORCE_PER_MINUTE = 'Power::FOOT_POUND_FORCE_PER_MINUTE';
    const FOOT_POUND_FORCE_PER_SECOND = 'Power::FOOT_POUND_FORCE_PER_SECOND';
    const FOOT_POUNDAL_PER_HOUR       = 'Power::FOOT_POUNDAL_PER_HOUR';
    const FOOT_POUNDAL_PER_MINUTE     = 'Power::FOOT_POUNDAL_PER_MINUTE';
    const FOOT_POUNDAL_PER_SECOND     = 'Power::FOOT_POUNDAL_PER_SECOND';
    const GIGAWATT                    = 'Power::GIGAWATT';
    const GRAM_FORCE_CENTIMETER_PER_HOUR   = 'Power::GRAM_FORCE_CENTIMETER_PER_HOUR';
    const GRAM_FORCE_CENTIMETER_PER_MINUTE = 'Power::GRAM_FORCE_CENTIMETER_PER_MINUTE';
    const GRAM_FORCE_CENTIMETER_PER_SECOND = 'Power::GRAM_FORCE_CENTIMETER_PER_SECOND';
    const HECTOWATT                   = 'Power::HECTOWATT';
    const HORSEPOWER_INTERNATIONAL    = 'Power::HORSEPOWER_INTERNATIONAL';
    const HORSEPOWER_ELECTRIC         = 'Power::HORSEPOWER_ELECTRIC';
    const HORSEPOWER                  = 'Power::HORSEPOWER';
    const HORSEPOWER_WATER            = 'Power::HORSEPOWER_WATER';
    const INCH_OUNCE_FORCE_REVOLUTION_PER_MINUTE = 'Power::INCH_OUNCH_FORCE_REVOLUTION_PER_MINUTE';
    const JOULE_PER_HOUR              = 'Power::JOULE_PER_HOUR';
    const JOULE_PER_MINUTE            = 'Power::JOULE_PER_MINUTE';
    const JOULE_PER_SECOND            = 'Power::JOULE_PER_SECOND';
    const KILOCALORIE_PER_HOUR        = 'Power::KILOCALORIE_PER_HOUR';
    const KILOCALORIE_PER_MINUTE      = 'Power::KILOCALORIE_PER_MINUTE';
    const KILOCALORIE_PER_SECOND      = 'Power::KILOCALORIE_PER_SECOND';
    const KILOGRAM_FORCE_METER_PER_HOUR   = 'Power::KILOGRAM_FORCE_METER_PER_HOUR';
    const KILOGRAM_FORCE_METER_PER_MINUTE = 'Power::KILOGRAM_FORCE_METER_PER_MINUTE';
    const KILOGRAM_FORCE_METER_PER_SECOND = 'Power::KILOGRAM_FORCE_METER_PER_SECOND';
    const KILOPOND_METER_PER_HOUR     = 'Power::KILOPOND_METER_PER_HOUR';
    const KILOPOND_METER_PER_MINUTE   = 'Power::KILOPOND_METER_PER_MINUTE';
    const KILOPOND_METER_PER_SECOND   = 'Power::KILOPOND_METER_PER_SECOND';
    const KILOWATT                    = 'Power::KILOWATT';
    const MEGAWATT                    = 'Power::MEGAWATT';
    const MICROWATT                   = 'Power::MICROWATT';
    const MILLION_BTU_PER_HOUR        = 'Power::MILLION_BTU_PER_HOUR';
    const MILLIWATT                   = 'Power::MILLIWATT';
    const NANOWATT                    = 'Power::NANOWATT';
    const NEWTON_METER_PER_HOUR       = 'Power::NEWTON_METER_PER_HOUR';
    const NEWTON_METER_PER_MINUTE     = 'Power::NEWTON_METER_PER_MINUTE';
    const NEWTON_METER_PER_SECOND     = 'Power::NEWTON_METER_PER_SECOND';
    const PETAWATT                    = 'Power::PETAWATT';
    const PFERDESTAERKE               = 'Power::PFERDESTAERKE';
    const PICOWATT                    = 'Power::PICOWATT';
    const PONCELET                    = 'Power::PONCELET';
    const POUND_SQUARE_FOOR_PER_CUBIC_SECOND = 'Power::POUND_SQUARE_FOOT_PER_CUBIC_SECOND';
    const TERAWATT                    = 'Power::TERAWATT';
    const TON_OF_REFRIGERATION        = 'Power::TON_OF_REFRIGERATION';
    const WATT                        = 'Power::WATT';
    const YOCTOWATT                   = 'Power::YOCTOWATT';
    const YOTTAWATT                   = 'Power::YOTTAWATT';
    const ZEPTOWATT                   = 'Power::ZEPTOWATT';
    const ZETTAWATT                   = 'Power::ZETTAWATT';

    private static $_UNITS = array(
        'Power::ATTOWATT'                    => array(1.0e-18,           'aW'),
        'Power::BTU_PER_HOUR'                => array(0.29307197,        'BTU/h'),
        'Power::BTU_PER_MINUTE'              => array(17.5843182,        'BTU/m'),
        'Power::BTU_PER_SECOND'              => array(1055.059092,       'BTU/s'),
        'Power::CALORIE_PER_HOUR'            => array(array('' => 11630, '*' => 1.0e-7),    'cal/h'),
        'Power::CALORIE_PER_MINUTE'          => array(array('' => 697800, '*' => 1.0e-7),   'cal/m'),
        'Power::CALORIE_PER_SECOND'          => array(array('' => 41868000, '*' => 1.0e-7), 'cal/s'),
        'Power::CENTIWATT'                   => array(0.01,              'cW'),
        'Power::CHEVAL_VAPEUR'               => array(735.49875,         'cv'),
        'Power::CLUSEC'                      => array(0.0000013332237,   'clusec'),
        'Power::DECIWATT'                    => array(0.1,               'dW'),
        'Power::DEKAWATT'                    => array(10,                'daW'),
        'Power::DYNE_CENTIMETER_PER_HOUR'    => array(array('' => 1.0e-7,'/' => 3600), 'dyn cm/h'),
        'Power::DYNE_CENTIMETER_PER_MINUTE'  => array(array('' => 1.0e-7,'/' => 60),   'dyn cm/m'),
        'Power::DYNE_CENTIMETER_PER_SECOND'  => array(1.0e-7,            'dyn cm/s'),
        'Power::ERG_PER_HOUR'                => array(array('' => 1.0e-7,'/' => 3600), 'erg/h'),
        'Power::ERG_PER_MINUTE'              => array(array('' => 1.0e-7,'/' => 60),   'erg/m'),
        'Power::ERG_PER_SECOND'              => array(1.0e-7,            'erg/s'),
        'Power::EXAWATT'                     => array(1.0e+18,           'EW'),
        'Power::FEMTOWATT'                   => array(1.0e-15,           'fW'),
        'Power::FOOT_POUND_FORCE_PER_HOUR'   => array(array('' => 1.3558179, '/' => 3600), 'ft lb/h'),
        'Power::FOOT_POUND_FORCE_PER_MINUTE' => array(array('' => 1.3558179, '/' => 60),   'ft lb/m'),
        'Power::FOOT_POUND_FORCE_PER_SECOND' => array(1.3558179,         'ft lb/s'),
        'Power::FOOT_POUNDAL_PER_HOUR'       => array(array('' => 0.04214011,'/' => 3600), 'ft pdl/h'),
        'Power::FOOT_POUNDAL_PER_MINUTE'     => array(array('' => 0.04214011, '/' => 60),  'ft pdl/m'),
        'Power::FOOT_POUNDAL_PER_SECOND'     => array(0.04214011,        'ft pdl/s'),
        'Power::GIGAWATT'                    => array(1.0e+9,            'GW'),
        'Power::GRAM_FORCE_CENTIMETER_PER_HOUR' => array(array('' => 0.0000980665,'/' => 3600), 'gf cm/h'),
        'Power::GRAM_FORCE_CENTIMETER_PER_MINUTE' => array(array('' => 0.0000980665,'/' => 60), 'gf cm/m'),
        'Power::GRAM_FORCE_CENTIMETER_PER_SECOND' => array(0.0000980665, 'gf cm/s'),
        'Power::HECTOWATT'                   => array(100,               'hW'),
        'Power::HORSEPOWER_INTERNATIONAL'    => array(745.69987,         'hp'),
        'Power::HORSEPOWER_ELECTRIC'         => array(746,               'hp'),
        'Power::HORSEPOWER'                  => array(735.49875,         'hp'),
        'Power::HORSEPOWER_WATER'            => array(746.043,           'hp'),
        'Power::INCH_OUNCH_FORCE_REVOLUTION_PER_MINUTE' => array(0.00073948398,    'in ocf/m'),
        'Power::JOULE_PER_HOUR'              => array(array('' => 1, '/' => 3600), 'J/h'),
        'Power::JOULE_PER_MINUTE'            => array(array('' => 1, '/' => 60),   'J/m'),
        'Power::JOULE_PER_SECOND'            => array(1,                 'J/s'),
        'Power::KILOCALORIE_PER_HOUR'        => array(1.163,             'kcal/h'),
        'Power::KILOCALORIE_PER_MINUTE'      => array(69.78,             'kcal/m'),
        'Power::KILOCALORIE_PER_SECOND'      => array(4186.8,            'kcal/s'),
        'Power::KILOGRAM_FORCE_METER_PER_HOUR' => array(array('' => 9.80665, '/' => 3600), 'kgf m/h'),
        'Power::KILOGRAM_FORCE_METER_PER_MINUTE' => array(array('' => 9.80665, '/' => 60), 'kfg m/m'),
        'Power::KILOGRAM_FORCE_METER_PER_SECOND' => array(9.80665,       'kfg m/s'),
        'Power::KILOPOND_METER_PER_HOUR'     => array(array('' => 9.80665, '/' => 3600), 'kp/h'),
        'Power::KILOPOND_METER_PER_MINUTE'   => array(array('' => 9.80665, '/' => 60),   'kp/m'),
        'Power::KILOPOND_METER_PER_SECOND'   => array(9.80665,           'kp/s'),
        'Power::KILOWATT'                    => array(1000,              'kW'),
        'Power::MEGAWATT'                    => array(1000000,           'MW'),
        'Power::MICROWATT'                   => array(0.000001,          'µW'),
        'Power::MILLION_BTU_PER_HOUR'        => array(293071.07,         'mio BTU/h'),
        'Power::MILLIWATT'                   => array(0.001,             'mM'),
        'Power::NANOWATT'                    => array(1.0e-9,            'nN'),
        'Power::NEWTON_METER_PER_HOUR'       => array(array('' => 1, '/' => 3600), 'Nm/h'),
        'Power::NEWTON_METER_PER_MINUTE'     => array(array('' => 1, '/' => 60),   'Nm/m'),
        'Power::NEWTON_METER_PER_SECOND'     => array(1,                 'Nm/s'),
        'Power::PETAWATT'                    => array(1.0e+15,           'PW'),
        'Power::PFERDESTAERKE'               => array(735.49875,         'PS'),
        'Power::PICOWATT'                    => array(1.0e-12,           'pW'),
        'Power::PONCELET'                    => array(980.665,           'p'),
        'Power::POUND_SQUARE_FOOT_PER_CUBIC_SECOND' => array(0.04214011, 'lb ft²/s³'),
        'Power::TERAWATT'                    => array(1.0e+12,           'TW'),
        'Power::TON_OF_REFRIGERATION'        => array(3516.85284,        'RT'),
        'Power::WATT'                        => array(1,                 'W'),
        'Power::YOCTOWATT'                   => array(1.0e-24,           'yW'),
        'Power::YOTTAWATT'                   => array(1.0e+24,           'YW'),
        'Power::ZEPTOWATT'                   => array(1.0e-21,           'zW'),
        'Power::ZETTAWATT'                   => array(1.0e+21,           'ZW')
    );

    private $_Locale;

    /**
     * Zend_Measure_Power provides an locale aware class for
     * conversion and formatting of power values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Power Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Power Type
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
            self::throwException('unknown type of power:' . $type);
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
            self::throwException('unknown type of power:' . $type);
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