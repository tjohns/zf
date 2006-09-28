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
 * @subpackage Zend_Measure_Pressure
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Pressure extends Zend_Measure_Abstract
{
    // Pressure definitions
    const STANDARD = 'Pressure::NEWTON_PER_SQUARE_METER';

    const ATMOSPHERE            = 'Pressure::ATMOSPHERE';
    const ATMOSPHERE_TECHNICAL  = 'Pressure::ATMOSPHERE_TECHNICAL';
    const ATTOBAR               = 'Pressure::ATTOBAR';
    const ATTOPASCAL            = 'Pressure::ATTOPASCAL';
    const BAR                   = 'Pressure::BAR';
    const BARAD                 = 'Pressure::BARAD';
    const BARYE                 = 'Pressure::BARYE';
    const CENTIBAR              = 'Pressure::CENTIBAR';
    const CENTIHG               = 'Pressure::CENTIHG';
    const CENTIMETER_MERCURY_0C = 'Pressure::CENTIMETER_MERCURY_0C';
    const CENTIMETER_WATER_4C   = 'Pressure::CENTIMETER_WATER_4C';
    const CENTIPASCAL           = 'Pressure::CENTIPASCAL';
    const CENTITORR             = 'Pressure::CENTITORR';
    const DECIBAR               = 'Pressure::DECIBAR';
    const DECIPASCAL            = 'Pressure::DECIPASCAL';
    const DECITORR              = 'Pressure::DECITORR';
    const DEKABAR               = 'Pressure::DEKABAR';
    const DEKAPASCAL            = 'Pressure::DEKAPASCAL';
    const DYNE_PER_SQUARE_CENTIMETER = 'Pressure::DYNE_PER_SQUARE_CENTIMETER';
    const EXABAR                = 'Pressure::EXABAR';
    const EXAPASCAL             = 'Pressure::EXAPASCAL';
    const FEMTOBAR              = 'Pressure::FEMTOBAR';
    const FEMTOPASCAL           = 'Pressure::FEMTOPASCAL';
    const FOOT_AIR_0C           = 'Pressure::FOOT_AIR_0C';
    const FOOT_AIR_15C          = 'Pressure::FOOT_AIR_15C';
    const FOOT_HEAD             = 'Pressure::FOOT_HEAD';
    const FOOT_MERCURY_0C       = 'Pressure::FOOT_MERCURY_0C';
    const FOOT_WATER_4C         = 'Pressure::FOOT_WATER_4C';
    const GIGABAR               = 'Pressure::GIGABAR';
    const GIGAPASCAL            = 'Pressure::GIGAPASCAL';
    const GRAM_FORCE_SQUARE_CENTIMETER = 'Pressure::GRAM_FORCE_SQUARE_CENTIMETER';
    const HECTOBAR              = 'Pressure::HECTOBAR';
    const HECTOPASCAL           = 'Pressure::HECTOPASCAL';
    const INCH_AIR_0C           = 'Pressure::INCH_AIR_0C';
    const INCH_AIR_15C          = 'Pressure::INCH_AIR_15C';
    const INCH_MERCURY_0C       = 'Pressure::INCH_MERCURY_0C';
    const INCH_WATER_4C         = 'Pressure::INCH_WATER_4C';
    const KILOBAR               = 'Pressure::KILOBAR';
    const KILOGRAM_FORCE_PER_SQUARE_CENTIMETER = 'Pressure::KILOGRAM_FORCE_PER_SQUARE_CENTIMETER';
    const KILOGRAM_FORCE_PER_SQUARE_METER      = 'Pressure::KILOGRAM_FORCE_PER_SQUARE_METER';
    const KILOGRAM_FORCE_PER_SQUARE_MILLIMETER = 'Pressure::KILOGRAM_FORCE_PER_SQUARE_MILLIMETER';
    const KILONEWTON_PER_SQUARE_METER          = 'Pressure::KILONEWTON_PER_SQUARE_METER';
    const KILOPASCAL            = 'Pressure::KILOPASCAL';
    const KILOPOND_PER_SQUARE_CENTIMETER       = 'Pressure::KILOPOND_PER_SQUARE_CENTIMETER';
    const KILOPOND_PER_SQUARE_METER            = 'Pressure::KILOPOND_PER_SQUARE_METER';
    const KILOPOND_PER_SQUARE_MILLIMETER       = 'Pressure::KILOPOND_PER_SQUARE_MILLIMETER';
    const KIP_PER_SQUARE_FOOT   = 'Pressure::KIP_PER_SQUARE_FOOT';
    const KIP_PER_SQUARE_INCH   = 'Pressure::KIP_PER_SQUARE_INCH';
    const MEGABAR               = 'Pressure::MEGABAR';
    const MEGANEWTON_PER_SQUARE_METER = 'Pressure::MEGANEWTON_PER_SQUARE_METER';
    const MEGAPASCAL            = 'Pressure::MEGAPASCAL';
    const METER_AIR_0C          = 'Pressure::METER_AIR_0C';
    const METER_AIR_15C         = 'Pressure::METER_AIR_15C';
    const METER_HEAD            = 'Pressure::METER_HEAD';
    const MICROBAR              = 'Pressure::MICROBAR';
    const MICROMETER_MERCURY_0C = 'Pressure::MICROMETER_MERCURY_0C';
    const MICROMETER_WATER_4C   = 'Pressure::MICROMETER_WATER_4C';
    const MICRON_MERCURY_0C     = 'Pressure::MICRON_MERCURY_0C';
    const MICROPASCAL           = 'Pressure::MICROPASCAL';
    const MILLIBAR              = 'Pressure::MILLIBAR';
    const MILLIHG               = 'Pressure::MILLIHG';
    const MILLIMETER_MERCURY_0C = 'Pressure::MILLIMETER_MERCURY_0C';
    const MILLIMETER_WATER_4C   = 'Pressure::MILLIMETER_WATER_4C';
    const MILLIPASCAL           = 'Pressure::MILLIPASCAL';
    const MILLITORR             = 'Pressure::MILLITORR';
    const NANOBAR               = 'Pressure::NANOBAR';
    const NANOPASCAL            = 'Pressure::NANOPASCAL';
    const NEWTON_PER_SQUARE_METER   = 'Pressure::NEWTON_PER_SQUARE_METER';
    const NEWTON_PER_SQUARE_MILLIMETER = 'Pressure::NEWTON_PER_SQUARE_MILLIMETER';
    const OUNCE_PER_SQUARE_INCH = 'Pressure::OUNCE_PER_SQUARE_INCH';
    const PASCAL                = 'Pressure::PASCAL';
    const PETABAR               = 'Pressure::PETABAR';
    const PETAPASCAL            = 'Pressure::PETAPASCAL';
    const PICOBAR               = 'Pressure::PICOBAR';
    const PICOPASCAL            = 'Pressure::PICOPASCAL';
    const PIEZE                 = 'Pressure::PIEZE';
    const POUND_PER_SQUARE_FOOT = 'Pressure::POUND_PER_SQUARE_FOOT';
    const POUND_PER_SQUARE_INCH = 'Pressure::POUND_PER_SQUARE_INCH';
    const POUNDAL_PER_SQUARE_FOOT   = 'Pressure::POUNDAL_PER_SQUARE_FOOT';
    const STHENE_PER_SQUARE_METER   = 'Pressure::STHENE_PER_SQUARE_METER';
    const TECHNICAL_ATMOSPHERE  = 'Pressure::TECHNICAL_ATMOSPHERE';
    const TERABAR               = 'Pressure::TERABAR';
    const TERAPASCAL            = 'Pressure::TERAPASCAL';
    const TON_PER_SQUARE_FOOT   = 'Pressure::TON_PER_SQUARE_FOOT';
    const TON_PER_SQUARE_FOOT_SHORT = 'Pressure::TON_PER_SQUARE_FOOT_SHORT';
    const TON_PER_SQUARE_INCH   = 'Pressure::TON_PER_SQUARE_INCH';
    const TON_PER_SQUARE_INCH_SHORT = 'Pressure::TON_PER_SQUARE_INCH_SHORT';
    const TON_PER_SQUARE_METER  = 'Pressure::TON_PER_SQUARE_METER';
    const TORR                  = 'Pressure::TORR';
    const WATER_COLUMN_CENTIMETER = 'Pressure::WATER_COLUMN_CENTIMETER';
    const WATER_COLUMN_INCH       = 'Pressure::WATER_COLUMN_INCH';
    const WATER_COLUMN_MILLIMETER = 'Pressure::WATER_COLUMN_MILLIMETER';
    const YOCTOBAR              = 'Pressure::YOCTOBAR';
    const YOCTOPASCAL           = 'Pressure::YOCTOPASCAL';
    const YOTTABAR              = 'Pressure::YOTTABAR';
    const YOTTAPASCAL           = 'Pressure::YOTTAPASCAL';
    const ZEPTOBAR              = 'Pressure::ZEPTOBAR';
    const ZEPTOPASCAL           = 'Pressure::ZEPTOPASCAL';
    const ZETTABAR              = 'Pressure::ZETTABAR';
    const ZETTAPASCAL           = 'Pressure::ZETTAPASCAL';

    private static $_UNITS = array(
        'Pressure::ATMOSPHERE'            => array(101325.01, 'atm'),
        'Pressure::ATMOSPHERE_TECHNICAL'  => array(98066.5,   'atm'),
        'Pressure::ATTOBAR'               => array(1.0e-13,   'ab'),
        'Pressure::ATTOPASCAL'            => array(1.0e-18,   'aPa'),
        'Pressure::BAR'                   => array(100000,    'b'),
        'Pressure::BARAD'                 => array(0.1,       'barad'),
        'Pressure::BARYE'                 => array(0.1,       'ba'),
        'Pressure::CENTIBAR'              => array(1000,      'cb'),
        'Pressure::CENTIHG'               => array(1333.2239, 'cHg'),
        'Pressure::CENTIMETER_MERCURY_0C' => array(1333.2239, 'cm mercury (0°C)'),
        'Pressure::CENTIMETER_WATER_4C'   => array(98.0665,   'cm water (4°C)'),
        'Pressure::CENTIPASCAL'           => array(0.01,      'cPa'),
        'Pressure::CENTITORR'             => array(1.3332237, 'cTorr'),
        'Pressure::DECIBAR'               => array(10000,     'db'),
        'Pressure::DECIPASCAL'            => array(0.1,       'dPa'),
        'Pressure::DECITORR'              => array(13.332237, 'dTorr'),
        'Pressure::DEKABAR'               => array(1000000,   'dab'),
        'Pressure::DEKAPASCAL'            => array(10,        'daPa'),
        'Pressure::DYNE_PER_SQUARE_CENTIMETER' => array(0.1,  'dyn/cm²'),
        'Pressure::EXABAR'                => array(1.0e+23,   'Eb'),
        'Pressure::EXAPASCAL'             => array(1.0e+18,   'EPa'),
        'Pressure::FEMTOBAR'              => array(1.0e-10,   'fb'),
        'Pressure::FEMTOPASCAL'           => array(1.0e-15,   'fPa'),
        'Pressure::FOOT_AIR_0C'           => array(3.8640888, 'ft air (0°C)'),
        'Pressure::FOOT_AIR_15C'          => array(3.6622931, 'ft air (15°C)'),
        'Pressure::FOOT_HEAD'             => array(2989.0669, 'ft head'),
        'Pressure::FOOT_MERCURY_0C'       => array(40636.664, 'ft mercury (0°C)'),
        'Pressure::FOOT_WATER_4C'         => array(2989.0669, 'ft water (4°C)'),
        'Pressure::GIGABAR'               => array(1.0e+14,   'Gb'),
        'Pressure::GIGAPASCAL'            => array(1.0e+9,    'GPa'),
        'Pressure::GRAM_FORCE_SQUARE_CENTIMETER' => array(98.0665, 'gf'),
        'Pressure::HECTOBAR'              => array(1.0e+7,    'hb'),
        'Pressure::HECTOPASCAL'           => array(100,       'hPa'),
        'Pressure::INCH_AIR_0C'           => array(array('' => 3.8640888, '/' => 12), 'in air (0°C)'),
        'Pressure::INCH_AIR_15C'          => array(array('' => 3.6622931, '/' => 12), 'in air (15°C)'),
        'Pressure::INCH_MERCURY_0C'       => array(array('' => 40636.664, '/' => 12), 'in mercury (0°C)'),
        'Pressure::INCH_WATER_4C'         => array(array('' => 2989.0669, '/' => 12), 'in water (4°C)'),
        'Pressure::KILOBAR'               => array(1.0e+8,    'kb'),
        'Pressure::KILOGRAM_FORCE_PER_SQUARE_CENTIMETER' => array(98066.5, 'kgf/cm²'),
        'Pressure::KILOGRAM_FORCE_PER_SQUARE_METER'      => array(9.80665, 'kgf/m²'),
        'Pressure::KILOGRAM_FORCE_PER_SQUARE_MILLIMETER' => array(9806650, 'kgf/mm²'),
        'Pressure::KILONEWTON_PER_SQUARE_METER'          => array(1000,    'kN/m²'),
        'Pressure::KILOPASCAL'            => array(1000,      'kPa'),
        'Pressure::KILOPOND_PER_SQUARE_CENTIMETER' => array(98066.5, 'kp/cm²'),
        'Pressure::KILOPOND_PER_SQUARE_METER'      => array(9.80665, 'kp/m²'),
        'Pressure::KILOPOND_PER_SQUARE_MILLIMETER' => array(9806650, 'kp/mm²'),
        'Pressure::KIP_PER_SQUARE_FOOT'   => array(array('' => 430.92233, '/' => 0.009),   'kip/ft²'),
        'Pressure::KIP_PER_SQUARE_INCH'   => array(array('' => 62052.81552, '/' => 0.009), 'kip/in²'),
        'Pressure::MEGABAR'               => array(1.0e+11,    'Mb'),
        'Pressure::MEGANEWTON_PER_SQUARE_METER' => array(1000000, 'MN/m²'),
        'Pressure::MEGAPASCAL'            => array(1000000,    'MPa'),
        'Pressure::METER_AIR_0C'          => array(12.677457,  'm air (0°C)'),
        'Pressure::METER_AIR_15C'         => array(12.015397,  'm air (15°C)'),
        'Pressure::METER_HEAD'            => array(9804.139432, 'm head'),
        'Pressure::MICROBAR'              => array(0.1,        'µb'),
        'Pressure::MICROMETER_MERCURY_0C' => array(0.13332239, 'µm mercury (0°C)'),
        'Pressure::MICROMETER_WATER_4C'   => array(0.00980665, 'µm water (4°C)'),
        'Pressure::MICRON_MERCURY_0C'     => array(0.13332239, 'µ mercury (0°C)'),
        'Pressure::MICROPASCAL'           => array(0.000001,   'µPa'),
        'Pressure::MILLIBAR'              => array(100,        'mb'),
        'Pressure::MILLIHG'               => array(133.32239,  'mHg'),
        'Pressure::MILLIMETER_MERCURY_0C' => array(133.32239,  'mm mercury (0°C)'),
        'Pressure::MILLIMETER_WATER_4C'   => array(9.80665,    'mm water (0°C)'),
        'Pressure::MILLIPASCAL'           => array(0.001,      'mPa'),
        'Pressure::MILLITORR'             => array(0.13332237, 'mTorr'),
        'Pressure::NANOBAR'               => array(0.0001,     'nb'),
        'Pressure::NANOPASCAL'            => array(1.0e-9,     'nPa'),
        'Pressure::NEWTON_PER_SQUARE_METER'      => array(1,   'N/m²'),
        'Pressure::NEWTON_PER_SQUARE_MILLIMETER' => array(1000000,   'N/mm²'),
        'Pressure::OUNCE_PER_SQUARE_INCH'        => array(430.92233, 'oz/in²'),
        'Pressure::PASCAL'                => array(1,          'Pa'),
        'Pressure::PETABAR'               => array(1.0e+20,    'Pb'),
        'Pressure::PETAPASCAL'            => array(1.0e+15,    'PPa'),
        'Pressure::PICOBAR'               => array(0.0000001,  'pb'),
        'Pressure::PICOPASCAL'            => array(1.0e-12,    'pPa'),
        'Pressure::PIEZE'                 => array(1000,       'pz'),
        'Pressure::POUND_PER_SQUARE_FOOT' => array(array('' => 430.92233, '/' => 9), 'lb/ft²'),
        'Pressure::POUND_PER_SQUARE_INCH' => array(6894.75728, 'lb/in²'),
        'Pressure::POUNDAL_PER_SQUARE_FOOT' => array(1.4881639, 'pdl/ft²'),
        'Pressure::STHENE_PER_SQUARE_METER' => array(1000,     'sn/m²'),
        'Pressure::TECHNICAL_ATMOSPHERE'  => array(98066.5,    'at'),
        'Pressure::TERABAR'               => array(1.0e+17,    'Tb'),
        'Pressure::TERAPASCAL'            => array(1.0e+12,    'TPa'),
        'Pressure::TON_PER_SQUARE_FOOT'   => array(array('' => 120658.2524, '/' => 1.125),      't/ft²'),
        'Pressure::TON_PER_SQUARE_FOOT_SHORT' => array(array('' => 430.92233, '/' => 0.0045),   't/ft²'),
        'Pressure::TON_PER_SQUARE_INCH'   => array(array('' => 17374788.3456, '/' => 1.125),    't/in²'),
        'Pressure::TON_PER_SQUARE_INCH_SHORT' => array(array('' => 62052.81552, '/' => 0.0045), 't/in²'),
        'Pressure::TON_PER_SQUARE_METER'  => array(9806.65,    't/m²'),
        'Pressure::TORR'                  => array(133.32237,  'Torr'),
        'Pressure::WATER_COLUMN_CENTIMETER' => array(98.0665,  'WC (cm)'),
        'Pressure::WATER_COLUMN_INCH'       => array(array('' => 2989.0669, '/' => 12), 'WC (in)'),
        'Pressure::WATER_COLUMN_MILLIMETER' => array(9.80665,  'WC (mm)'),
        'Pressure::YOCTOBAR'              => array(1.0e-19,    'yb'),
        'Pressure::YOCTOPASCAL'           => array(1.0e-24,    'yPa'),
        'Pressure::YOTTABAR'              => array(1.0e+29,    'Yb'),
        'Pressure::YOTTAPASCAL'           => array(1.0e+24,    'YPa'),
        'Pressure::ZEPTOBAR'              => array(1.0e-16,    'zb'),
        'Pressure::ZEPTOPASCAL'           => array(1.0e-21,    'zPa'),
        'Pressure::ZETTABAR'              => array(1.0e+26,    'Zb'),
        'Pressure::ZETTAPASCAL'           => array(1.0e+21,    'ZPa')
    );

    private $_Locale;

    /**
     * Zend_Measure_Pressure provides an locale aware class for
     * conversion and formatting of pressure values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Pressure Type
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
     * @param  $object  object to compare equality
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Pressure Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty( self::$_UNITS[$type] )) {
            self::throwException('unknown type of pressure:' . $type);
        }
        
        parent::setValue($value, $type, $locale);
        parent::setType( $type );
    }


    /**
     * Set a new type, and convert the value
     *
     * @param  $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType( $type )
    {
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of pressure:' . $type);
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