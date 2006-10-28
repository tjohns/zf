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
 * @subpackage Zend_Measure_Energy
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Energy extends Zend_Measure_Abstract
{
    // Energy definitions
    const STANDARD = 'Energy::JOULE';

    const ATTOJOULE              = 'Energy::ATTOJOULE';
    const BOARD_OF_TRADE_UNIT    = 'Energy::BOARD_OF_TRADE_UNIT';
    const BTU                    = 'Energy::BTU';
    const BTU_THERMOCHEMICAL     = 'Energy::BTU_TERMOCHEMICAL';
    const CALORIE                = 'Energy::CALORIE';
    const CALORIE_15C            = 'Energy::CALORIE_15C';
    const CALORIE_NUTRITIONAL    = 'Energy::CALORIE_NUTRITIONAL';
    const CALORIE_THERMOCHEMICAL = 'Energy::CALORIE_THERMOCHEMICAL';
    const CELSIUS_HEAT_UNIT      = 'Energy::CELSIUS_HEAT_UNIT';
    const CENTIJOULE             = 'Energy::CENTIJOULE';
    const CHEVAL_VAPEUR_HEURE    = 'Energy::CHEVAL_VAPEUR_HEURE';
    const DECIJOULE              = 'Energy::DECIJOULE';
    const DEKAJOULE              = 'Energy::DEKAJOULE';
    const DEKAWATT_HOUR          = 'Energy::DEKAWATT_HOUR';
    const DEKATHERM              = 'Energy::DEKATHERM';
    const ELECTRONVOLT           = 'Energy::ELECTRONVOLT';
    const ERG                    = 'Energy::ERG';
    const EXAJOULE               = 'Energy::EXAJOULE';
    const EXAWATT_HOUR           = 'Energy::EXAWATT_HOUR';
    const FEMTOJOULE             = 'Energy::FEMTOJOULE';
    const FOOT_POUND             = 'Energy::FOOT_POUND';
    const FOOT_POUNDAL           = 'Energy::FOOT_POUNDAL';
    const GALLON_UK_AUTOMOTIVE   = 'Energy::GALLON_UK_AUTOMOTIVE';
    const GALLON_US_AUTOMOTIVE   = 'Energy::GALLON_US_AUTOMOTIVE';
    const GALLON_UK_AVIATION     = 'Energy::GALLON_UK_AVIATION';
    const GALLON_US_AVIATION     = 'Energy::GALLON_US_AVIATION';
    const GALLON_UK_DIESEL       = 'Energy::GALLON_UK_DIESEL';
    const GALLON_US_DIESEL       = 'Energy::GALLON_US_DIESEL';
    const GALLON_UK_DISTILATE    = 'Energy::GALLON_UK_DISTILATE';
    const GALLON_US_DISTILATE    = 'Energy::GALLON_US_DISTILATE';
    const GALLON_UK_KEROSINE_JET = 'Energy::GALLON_UK_KEROSINE_JET';
    const GALLON_US_KEROSINE_JET = 'Energy::GALLON_US_KEROSINE_JET';
    const GALLON_UK_LPG          = 'Energy::GALLON_UK_LPG';
    const GALLON_US_LPG          = 'Energy::GALLON_US_LPG';
    const GALLON_UK_NAPHTA       = 'Energy::GALLON_UK_NAPHTA';
    const GALLON_US_NAPHTA       = 'Energy::GALLON_US_NAPHTA';
    const GALLON_UK_KEROSENE     = 'Energy::GALLON_UK_KEROSINE';
    const GALLON_US_KEROSENE     = 'Energy::GALLON_US_KEROSINE';
    const GALLON_UK_RESIDUAL     = 'Energy::GALLON_UK_RESIDUAL';
    const GALLON_US_RESIDUAL     = 'Energy::GALLON_US_RESIDUAL';
    const GIGAELECTRONVOLT       = 'Energy::GIGAELECTRONVOLT';
    const GIGACALORIE            = 'Energy::GIGACALORIE';
    const GIGACALORIE_15C        = 'Energy::GIGACALORIE_15C';
    const GIGAJOULE              = 'Energy::GIGAJOULE';
    const GIGAWATT_HOUR          = 'Energy::GIGAWATT_HOUR';
    const GRAM_CALORIE           = 'Energy::GRAM_CALORIE';
    const HARTREE                = 'Energy::HARTREE';
    const HECTOJOULE             = 'Energy::HECTOJOULE';
    const HECTOWATT_HOUR         = 'Energy::HECTOWATT_HOUR';
    const HORSEPOWER_HOUR        = 'Energy::HORSEPOWER_HOUR';
    const HUNDRED_CUBIC_FOOT_GAS = 'Energy::HUNDRED_CUBIC_FOOT_GAS';
    const INCH_OUNCE             = 'Energy::INCH_OUNCE';
    const INCH_POUND             = 'Energy::INCH_POUND';
    const JOULE                  = 'Energy::JOULE';
    const KILOCALORIE_15C        = 'Energy::KILOCALORIE_15C';
    const KILOCALORIE            = 'Energy::KILOCALORIE';
    const KILOCALORIE_THERMOCHEMICAL = 'Energy::KILOCALORIE_THERMOCHEMICAL';
    const KILOELECTRONVOLT       = 'Energy::KILOELECTRONVOLT';
    const KILOGRAM_CALORIE       = 'Energy::KILOGRAM_CALORIE';
    const KILOGRAM_FORCE_METER   = 'Energy::KILOGRAM_FORCE_METER';
    const KILOJOULE              = 'Energy::KILOJOULE';
    const KILOPOND_METER         = 'Energy::KILOPOND_METER';
    const KILOTON                = 'Energy::KILOTON';
    const KILOWATT_HOUR          = 'Energy::KILOWATT_HOUR';
    const LITER_ATMOSPHERE       = 'Energy::LITER_ATMOSPHERE';
    const MEGAELECTRONVOLT       = 'Energy::MEGAELECTRONVOLT';
    const MEGACALORIE            = 'Energy::MEGACALORIE';
    const MEGACALORIE_15C        = 'Energy::MEGACALORIE_15C';
    const MEGAJOULE              = 'Energy::MEGAJOULE';
    const MEGALERG               = 'Energy::MEGALERG';
    const MEGATON                = 'Energy::MEGATON';
    const MEGAWATTHOUR           = 'Energy::MEGAWATTHOUR';
    const METER_KILOGRAM_FORCE   = 'Energy::METER_KILOGRAM_FORCE';
    const MICROJOULE             = 'Energy::MICROJOULE';
    const MILLIJOULE             = 'Energy::MILLIJOULE';
    const MYRIAWATT_HOUR         = 'Energy::MYRIAWATT_HOUR';
    const NANOJOULE              = 'Energy::NANOJOULE';
    const NEWTON_METER           = 'Energy::NEWTON_METER';
    const PETAJOULE              = 'Energy::PETAJOULE';
    const PETAWATTHOUR           = 'Energy::PETAWATTHOUR';
    const PFERDESTAERKENSTUNDE   = 'Energy::PFERDESTAERKENSTUNDE';
    const PICOJOULE              = 'Energy::PICOJOULE';
    const Q_UNIT                 = 'Energy::Q_UNIT';
    const QUAD                   = 'Energy::QUAD';
    const TERAELECTRONVOLT       = 'Energy::TERAELECTRONVOLT';
    const TERAJOULE              = 'Energy::TERAJOULE';
    const TERAWATTHOUR           = 'Energy::TERAWATTHOUR';
    const THERM                  = 'Energy::THERM';
    const THERM_US               = 'Energy::THERM_US';
    const THERMIE                = 'Energy::THERMIE';
    const TON                    = 'Energy::TON';
    const TONNE_COAL             = 'Energy::TONNE_COAL';
    const TONNE_OIL              = 'Energy::TONNE_OIL';
    const WATTHOUR               = 'Energy::WATTHOUR';
    const WATTSECOND             = 'Energy::WATTSECOND';
    const YOCTOJOULE             = 'Energy::YOCTOJOULE';
    const YOTTAJOULE             = 'Energy::YOTTAJOULE';
    const YOTTAWATTHOUR          = 'Energy::YOTTAWATTHOUR';
    const ZEPTOJOULE             = 'Energy::ZEPTOJOULE';
    const ZETTAJOULE             = 'Energy::ZETTAJOULE';
    const ZETTAWATTHOUR          = 'Energy::ZETTAWATTHOUR';

    private static $_UNITS = array(
        'Energy::ATTOJOULE'              => array(1.0e-18,           'aJ'),
        'Energy::BOARD_OF_TRADE_UNIT'    => array(3600000,           'BOTU'),
        'Energy::BTU'                    => array(1055.0559,         'Btu'),
        'Energy::BTU_TERMOCHEMICAL'      => array(1054.3503,         'Btu'),
        'Energy::CALORIE'                => array(4.1868,            'cal'),
        'Energy::CALORIE_15C'            => array(6.1858,            'cal'),
        'Energy::CALORIE_NUTRITIONAL'    => array(4186.8,            'cal'),
        'Energy::CALORIE_THERMOCHEMICAL' => array(4.184,             'cal'),
        'Energy::CELSIUS_HEAT_UNIT'      => array(1899.1005,         'Chu'),
        'Energy::CENTIJOULE'             => array(0.01,              'cJ'),
        'Energy::CHEVAL_VAPEUR_HEURE'    => array(2647795.5,         'cv heure'),
        'Energy::DECIJOULE'              => array(0.1,               'dJ'),
        'Energy::DEKAJOULE'              => array(10,                'daJ'),
        'Energy::DEKAWATT_HOUR'          => array(36000,             'daWh'),
        'Energy::DEKATHERM'              => array(1.055057e+9,       'dathm'),
        'Energy::ELECTRONVOLT'           => array(1.6021773e-19,     'eV'),
        'Energy::ERG'                    => array(0.0000001,         'erg'),
        'Energy::EXAJOULE'               => array(1.0e+18,           'EJ'),
        'Energy::EXAWATT_HOUR'           => array(3.6e+21,           'EWh'),
        'Energy::FEMTOJOULE'             => array(1.0e-15,           'fJ'),
        'Energy::FOOT_POUND'             => array(1.3558179,         'ft lb'),
        'Energy::FOOT_POUNDAL'           => array(0.04214011,        'ft poundal'),
        'Energy::GALLON_UK_AUTOMOTIVE'   => array(158237172,         'gal car gasoline'),
        'Energy::GALLON_US_AUTOMOTIVE'   => array(131760000,         'gal car gasoline'),
        'Energy::GALLON_UK_AVIATION'     => array(158237172,         'gal jet gasoline'),
        'Energy::GALLON_US_AVIATION'     => array(131760000,         'gal jet gasoline'),
        'Energy::GALLON_UK_DIESEL'       => array(175963194,         'gal diesel'),
        'Energy::GALLON_US_DIESEL'       => array(146520000,         'gal diesel'),
        'Energy::GALLON_UK_DISTILATE'    => array(175963194,         'gal destilate fuel'),
        'Energy::GALLON_US_DISTILATE'    => array(146520000,         'gal destilate fuel'),
        'Energy::GALLON_UK_KEROSINE_JET' => array(170775090,         'gal jet kerosine'),
        'Energy::GALLON_US_KEROSINE_JET' => array(142200000,         'gal jet kerosine'),
        'Energy::GALLON_UK_LPG'          => array(121005126.0865275, 'gal lpg'),
        'Energy::GALLON_US_LPG'          => array(100757838.45,      'gal lpg'),
        'Energy::GALLON_UK_NAPHTA'       => array(160831224,         'gal jet fuel'),
        'Energy::GALLON_US_NAPHTA'       => array(133920000,         'gal jet fuel'),
        'Energy::GALLON_UK_KEROSINE'     => array(170775090,         'gal kerosine'),
        'Energy::GALLON_US_KEROSINE'     => array(142200000,         'gal kerosine'),
        'Energy::GALLON_UK_RESIDUAL'     => array(189798138,         'gal residual fuel'),
        'Energy::GALLON_US_RESIDUAL'     => array(158040000,         'gal residual fuel'),
        'Energy::GIGAELECTRONVOLT'       => array(1.6021773e-10,     'GeV'),
        'Energy::GIGACALORIE'            => array(4186800000,        'Gcal'),
        'Energy::GIGACALORIE_15C'        => array(4185800000,        'Gcal'),
        'Energy::GIGAJOULE'              => array(1.0e+9,            'GJ'),
        'Energy::GIGAWATT_HOUR'          => array(3.6e+12,           'GWh'),
        'Energy::GRAM_CALORIE'           => array(4.1858,            'g cal'),
        'Energy::HARTREE'                => array(4.3597482e-18,     'Eh'),
        'Energy::HECTOJOULE'             => array(100,               'hJ'),
        'Energy::HECTOWATT_HOUR'         => array(360000,            'hWh'),
        'Energy::HORSEPOWER_HOUR'        => array(2684519.5,         'hph'),
        'Energy::HUNDRED_CUBIC_FOOT_GAS' => array(108720000,         'hundred ft� gas'),
        'Energy::INCH_OUNCE'             => array(0.0070615518,      'in oc'),
        'Energy::INCH_POUND'             => array(0.112984825,       'in lb'),
        'Energy::JOULE'                  => array(1,                 'J'),
        'Energy::KILOCALORIE_15C'        => array(4185.8,            'kcal'),
        'Energy::KILOCALORIE'            => array(4186,8,            'kcal'),
        'Energy::KILOCALORIE_THERMOCHEMICAL' => array(4184,          'kcal'),
        'Energy::KILOELECTRONVOLT'       => array(1.6021773e-16,     'keV'),
        'Energy::KILOGRAM_CALORIE'       => array(4185.8,            'kg cal'),
        'Energy::KILOGRAM_FORCE_METER'   => array(9.80665,           'kgf m'),
        'Energy::KILOJOULE'              => array(1000,              'kJ'),
        'Energy::KILOPOND_METER'         => array(9.80665,           'kp m'),
        'Energy::KILOTON'                => array(4.184e+12,         'kt'),
        'Energy::KILOWATT_HOUR'          => array(3600000,           'kWh'),
        'Energy::LITER_ATMOSPHERE'       => array(101.325,           'l atm'),
        'Energy::MEGAELECTRONVOLT'       => array(1.6021773e-13,     'MeV'),
        'Energy::MEGACALORIE'            => array(4186800,           'Mcal'),
        'Energy::MEGACALORIE_15C'        => array(4185800,           'Mcal'),
        'Energy::MEGAJOULE'              => array(1000000,           'MJ'),
        'Energy::MEGALERG'               => array(0.1,               'megalerg'),
        'Energy::MEGATON'                => array(4.184e+15,         'Mt'),
        'Energy::MEGAWATTHOUR'           => array(3.6e+9,            'MWh'),
        'Energy::METER_KILOGRAM_FORCE'   => array(9.80665,           'm kgf'),
        'Energy::MICROJOULE'             => array(0.000001,          '�J'),
        'Energy::MILLIJOULE'             => array(0.001,             'mJ'),
        'Energy::MYRIAWATT_HOUR'         => array(3.6e+7,            'myWh'),
        'Energy::NANOJOULE'              => array(1.0e-9,            'nJ'),
        'Energy::NEWTON_METER'           => array(1,                 'Nm'),
        'Energy::PETAJOULE'              => array(1.0e+15,           'PJ'),
        'Energy::PETAWATTHOUR'           => array(3.6e+18,           'PWh'),
        'Energy::PFERDESTAERKENSTUNDE'   => array(2647795.5,         'ps h'),
        'Energy::PICOJOULE'              => array(1.0e-12,           'pJ'),
        'Energy::Q_UNIT'                 => array(1.0550559e+21,     'Q unit'),
        'Energy::QUAD'                   => array(1.0550559e+18,     'quad'),
        'Energy::TERAELECTRONVOLT'       => array(1.6021773e-7,      'TeV'),
        'Energy::TERAJOULE'              => array(1.0e+12,           'TJ'),
        'Energy::TERAWATTHOUR'           => array(3.6e+15,           'TWh'),
        'Energy::THERM'                  => array(1.0550559e+8,      'thm'),
        'Energy::THERM_US'               => array(1.054804e+8,       'thm'),
        'Energy::THERMIE'                => array(4185800,           'th'),
        'Energy::TON'                    => array(4.184e+9,          'T explosive'),
        'Energy::TONNE_COAL'             => array(2.93076e+10,       'T coal'),
        'Energy::TONNE_OIL'              => array(4.1868e+10,        'T oil'),
        'Energy::WATTHOUR'               => array(3600,              'Wh'),
        'Energy::WATTSECOND'             => array(1,                 'Ws'),
        'Energy::YOCTOJOULE'             => array(1.0e-24,           'yJ'),
        'Energy::YOTTAJOULE'             => array(1.0e+24,           'YJ'),
        'Energy::YOTTAWATTHOUR'          => array(3.6e+27,           'YWh'),
        'Energy::ZEPTOJOULE'             => array(1.0e-21,           'zJ'),
        'Energy::ZETTAJOULE'             => array(1.0e+21,           'ZJ'),
        'Energy::ZETTAWATTHOUR'          => array(3.6e+24,           'ZWh')
    );

    private $_Locale;

    /**
     * Zend_Measure_Energy provides an locale aware class for
     * conversion and formatting of energy values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Energy Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Energy Type
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
            self::throwException('unknown type of energy:' . $type);
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
            self::throwException('unknown type of energy:' . $type);
        }

        // Convert to standard value
        $value = parent::getValue();
        $value = $value * (self::$_UNITS[parent::getType()][0]);

        // Convert to expected value
        $value = $value / (self::$_UNITS[$type][0]);
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