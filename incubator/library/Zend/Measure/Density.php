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
 * @subpackage Zend_Measure_Density
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Density extends Zend_Measure_Abstract
{
    // Density definitions
    const STANDARD = 'Density::KILOGRAM_PER_CUBIC_METER';

    const ALUMINIUM                 = 'Density::ALUMINIUM';
    const COPPER                    = 'Density::COPPER';
    const GOLD                      = 'Density::GOLD';
    const GRAIN_PER_CUBIC_FOOT      = 'Density::GRAIN_PER_CUBIC_FOOT';
    const GRAIN_PER_CUBIC_INCH      = 'Density::GRAIN_PER_CUBIC_INCH';
    const GRAIN_PER_CUBIC_YARD      = 'Density::GRAIN_PER_CUBIC_YARD';
    const GRAIN_PER_GALLON          = 'Density::GRAIN_PER_GALLON';
    const GRAIN_PER_GALLON_US       = 'Density::GRAIN_PER_GALLON_US';
    const GRAM_PER_CUBIC_CENTIMETER = 'Density::GRAM_PER_CUBIC_CENTIMETER';
    const GRAM_PER_CUBIC_DECIMETER  = 'Density::GRAM_PER_CUBIC_DECIMETER';
    const GRAM_PER_CUBIC_METER      = 'Density::GRAM_PER_CUBIC_METER';
    const GRAM_PER_LITER            = 'Density::GRAM_PER_LITER';
    const GRAM_PER_MILLILITER       = 'Density::GRAM_PER_MILLILITER';
    const IRON                      = 'Density::IRON';
    const KILOGRAM_PER_CUBIC_CENTIMETER = 'Density::KILOGRAM_PER_CUBIC_CENTIMETER';
    const KILOGRAM_PER_CUBIC_DECIMETER  = 'Density::KILOGRAM_PER_CUBIC_DECIMETER';
    const KILOGRAM_PER_CUBIC_METER  = 'Density::KILOGRAM_PER_CUBIC_METER';
    const KILOGRAM_PER_CUBIC_MILLIMETER = 'Density::KILOGRAM_PER_CUBIC_MILLIMETER';
    const KILOGRAM_PER_LITER        = 'Density::KILOGRAM_PER_LITER';
    const KILOGRAM_PER_MILLILITER   = 'Density::KILOGRAM_PER_MILLILITER';
    const LEAD                      = 'Density::LEAD';
    const MEGAGRAM_PER_CUBIC_CENTIMETER = 'Density::MEGAGRAM_PER_CUBIC_CENTIMETER';
    const MEGAGRAM_PER_CUBIC_DECIMETER  = 'Density::MEGAGRAM_PER_CUBIC_DECIMETER';
    const MEGAGRAM_PER_CUBIC_METER  = 'Density::MEGAGRAM_PER_CUBIC_METER';
    const MEGAGRAM_PER_LITER        = 'Density::MEGAGRAM_PER_LITER';
    const MEGAGRAM_PER_MILLILITER   = 'Density::MEGAGRAM_PER_MILLILITER';
    const MICROGRAM_PER_CUBIC_CENTIMETER = 'Density::MICROGRAM_PER_CUBIC_CENTIMETER';
    const MICROGRAM_PER_CUBIC_DECIMETER  = 'Density::MICROGRAM_PER_CUBIC_DECIMETER';
    const MICROGRAM_PER_CUBIC_METER = 'Density::MICROGRAM_PER_CUBIC_METER';
    const MICROGRAM_PER_LITER       = 'Density::MICROGRAM_PER_LITER';
    const MICROGRAM_PER_MILLILITER  = 'Density::MICROGRAM_PER_MILLILITER';
    const MILLIGRAM_PER_CUBIC_CENTIMETER = 'Density::MILLIGRAM_PER_CUBIC_CENTIMETER';
    const MILLIGRAM_PER_CUBIC_DECIMETER  = 'Density::MILLIGRAM_PER_CUBIC_DECIMETER';
    const MILLIGRAM_PER_CUBIC_METER = 'Density::MILLIGRAM_PER_CUBIC_METER';
    const MILLIGRAM_PER_LITER       = 'Density::MILLIGRAM_PER_LITER';
    const MILLIGRAM_PER_MILLILITER  = 'Density::MILLIGRAM_PER_MILLILITER';
    const OUNCE_PER_CUBIC_FOOT      = 'Density::OUNCE_PER_CUBIC_FOOT';
    const OUNCR_PER_CUBIC_FOOT_TROY = 'Density::OUNCE_PER_CUBIC_FOOT_TROY';
    const OUNCE_PER_CUBIC_INCH      = 'Density::OUNCE_PER_CUBIC_INCH';
    const OUNCE_PER_CUBIC_INCH_TROY = 'Density::OUNCE_PER_CUBIC_INCH_TROY';
    const OUNCE_PER_CUBIC_YARD      = 'Density::OUNCE_PER_CUBIC_YARD';
    const OUNCE_PER_CUBIC_YARD_TROY = 'Density::OUNCE_PER_CUBIC_YARD_TROY';
    const OUNCE_PER_GALLON          = 'Density::OUNCE_PER_GALLON';
    const OUNCE_PER_GALLON_US       = 'Density::OUNCE_PER_GALLON_US';
    const OUNCE_PER_GALLON_TROY     = 'Density::OUNCE_PER_GALLON_TROY';
    const OUNCE_PER_GALLON_US_TROY  = 'Density::OUNCE_PER_GALLON_US_TROY';
    const POUND_PER_CIRCULAR_MIL_FOOT = 'Density::POUND_PER_CIRCULAR_MIL_FOOT';
    const POUND_PER_CUBIC_FOOT      = 'Density::POUND_PER_CUBIC_FOOT';
    const POUND_PER_CUBIC_INCH      = 'Density::POUND_PER_CUBIC_INCH';
    const POUND_PER_CUBIC_YARD      = 'Density::POUND_PER_CUBIC_YARD';
    const POUND_PER_GALLON          = 'Density::POUND_PER_GALLON';
    const POUND_PER_KILOGALLON      = 'Density::POUND_PER_KILOGALLON';
    const POUND_PER_MEGAGALLON      = 'Density::POUND_PER_MEGAGALLON';
    const POUND_PER_GALLON_US       = 'Density::POUND_PER_GALLON_US';
    const POUND_PER_KILOGALLON_US   = 'Density::POUND_PER_KILOGALLON_US';
    const POUND_PER_MEGAGALLON_US   = 'Density::POUND_PER_MEGAGALLON_US';
    const SILVER                    = 'Density::SILVER';
    const SLUG_PER_CUBIC_FOOT       = 'Density::SLUG_PER_CUBIC_FOOT';
    const SLUG_PER_CUBIC_INCH       = 'Density::SLUG_PER_CUBIC_INCH';
    const SLUG_PER_CUBIC_YARD       = 'Density::SLUG_PER_CUBIC_YARD';
    const SLUG_PER_GALLON           = 'Density::SLUG_PER_GALLON';
    const SLUG_PER_GALLON_US        = 'Density::SLUG_PER_GALLON_US';
    const TON_PER_CUBIC_FOOT_LONG   = 'Density::TON_PER_CUBIC_FOOT_LONG';
    const TON_PER_CUBIC_FOOT        = 'Density::TON_PER_CUBIC_FOOT';
    const TON_PER_CUBIC_INCH_LONG   = 'Density::TON_PER_CUBIC_INCH_LONG';
    const TON_PER_CUBIC_INCH        = 'Density::TON_PER_CUBIC_INCH';
    const TON_PER_CUBIC_YARD_LONG   = 'Density::TON_PER_CUBIC_YARD_LONG';
    const TON_PER_CUBIC_YARD        = 'Density::TON_PER_CUBIC_YARD';
    const TON_PER_GALLON_LONG       = 'Density::TON_PER_GALLON_LONG';
    const TON_PER_GALLON_US_LONG    = 'Density::TON_PER_GALLON_US_LONG';
    const TON_PER_GALLON            = 'Density::TON_PER_GALLON';
    const TON_PER_GALLON_US         = 'Density::TON_PER_GALLON_US';
    const TONNE_PER_CUBIC_CENTIMETER= 'Density::TONNE_PER_CUBIC_CENTIMETER';
    const TONNE_PER_CUBIC_DECIMETER = 'Density::TONNE_PER_CUBIC_DECIMETER';
    const TONNE_PER_CUBIC_METER     = 'Density::TONNE_PER_CUBIC_METER';
    const TONNE_PER_LITER           = 'Density::TONNE_PER_LITER';
    const TONNE_PER_MILLILITER      = 'Density::TONNE_PER_MILLILITER';
    const WATER                     = 'Density::WATER';

    private static $_UNITS = array(
        'Density::ALUMINIUM'                 => array(2643,           'aluminium'),
        'Density::COPPER'                    => array(8906,           'copper'),
        'Density::GOLD'                      => array(19300,          'gold'),
        'Density::GRAIN_PER_CUBIC_FOOT'      => array(0.0022883519,   'gr/ft³'),
        'Density::GRAIN_PER_CUBIC_INCH'      => array(3.9542721,      'gr/in³'),
        'Density::GRAIN_PER_CUBIC_YARD'      => array(0.000084753774, 'gr/yd³'),
        'Density::GRAIN_PER_GALLON'          => array(0.014253768,    'gr/gal'),
        'Density::GRAIN_PER_GALLON_US'       => array(0.017118061,    'gr/gal'),
        'Density::GRAM_PER_CUBIC_CENTIMETER' => array(1000,           'g/cm³'),
        'Density::GRAM_PER_CUBIC_DECIMETER'  => array(1,              'g/dm³'),
        'Density::GRAM_PER_CUBIC_METER'      => array(0.001,          'g/m³'),
        'Density::GRAM_PER_LITER'            => array(1,              'g/l'),
        'Density::GRAM_PER_MILLILITER'       => array(1000,           'g/ml'),
        'Density::IRON'                      => array(7658,           'iron'),
        'Density::KILOGRAM_PER_CUBIC_CENTIMETER' => array(1000000,    'kg/cm³'),
        'Density::KILOGRAM_PER_CUBIC_DECIMETER'  => array(1000,       'kg/dm³'),
        'Density::KILOGRAM_PER_CUBIC_METER'  => array(1,              'kg/m³'),
        'Density::KILOGRAM_PER_CUBIC_MILLIMETER' => array(1000000000, 'kg/l'),
        'Density::KILOGRAM_PER_LITER'        => array(1000,           'kg/ml'),
        'Density::KILOGRAM_PER_MILLILITER'   => array(1000000,        'kg/ml'),
        'Density::LEAD'                      => array(11370,          'lead'),
        'Density::MEGAGRAM_PER_CUBIC_CENTIMETER' => array(1.0e+9,     'Mg/cm³'),
        'Density::MEGAGRAM_PER_CUBIC_DECIMETER'  => array(1000000,    'Mg/dm³'),
        'Density::MEGAGRAM_PER_CUBIC_METER'  => array(1000,           'Mg/m³'),
        'Density::MEGAGRAM_PER_LITER'        => array(1000000,        'Mg/l'),
        'Density::MEGAGRAM_PER_MILLILITER'   => array(1.0e+9,         'Mg/ml'),
        'Density::MICROGRAM_PER_CUBIC_CENTIMETER' => array(0.001,     'µg/cm³'),
        'Density::MICROGRAM_PER_CUBIC_DECIMETER'  => array(1.0e-6,    'µg/dm³'),
        'Density::MICROGRAM_PER_CUBIC_METER' => array(1.0e-9,         'µg/m³'),
        'Density::MICROGRAM_PER_LITER'       => array(1.0e-6,         'µg/l'),
        'Density::MICROGRAM_PER_MILLILITER'  => array(0.001,          'µg/ml'),
        'Density::MILLIGRAM_PER_CUBIC_CENTIMETER' => array(1,         'mg/cm³'),
        'Density::MILLIGRAM_PER_CUBIC_DECIMETER'  => array(0.001,     'mg/dm³'),
        'Density::MILLIGRAM_PER_CUBIC_METER' => array(0.000001,       'mg/m³'),
        'Density::MILLIGRAM_PER_LITER'       => array(0.001,          'mg/l'),
        'Density::MILLIGRAM_PER_MILLILITER'  => array(1,              'mg/ml'),
        'Density::OUNCE_PER_CUBIC_FOOT'      => array(1.001154,       'oz/ft³'),
        'Density::OUNCE_PER_CUBIC_FOOT_TROY' => array(1.0984089,      'oz/ft³'),
        'Density::OUNCE_PER_CUBIC_INCH'      => array(1729.994,       'oz/in³'),
        'Density::OUNCE_PER_CUBIC_INCH_TROY' => array(1898.0506,      'oz/in³'),
        'Density::OUNCE_PER_CUBIC_YARD'      => array(0.037079776,    'oz/yd³'),
        'Density::OUNCE_PER_CUBIC_YARD_TROY' => array(0.040681812,    'oz/yd³'),
        'Density::OUNCE_PER_GALLON'          => array(6.2360233,      'oz/gal'),
        'Density::OUNCE_PER_GALLON_US'       => array(7.4891517,      'oz/gal'),
        'Density::OUNCE_PER_GALLON_TROY'     => array(6.8418084,      'oz/gal'),
        'Density::OUNCE_PER_GALLON_US_TROY'  => array(8.2166693,      'oz/gal'),
        'Density::POUND_PER_CIRCULAR_MIL_FOOT' => array(2.9369291,    'lb/cmil ft'),
        'Density::POUND_PER_CUBIC_FOOT'      => array(16.018463,      'lb/in³'),
        'Density::POUND_PER_CUBIC_INCH'      => array(27679.905,      'lb/in³'),
        'Density::POUND_PER_CUBIC_YARD'      => array(0.59327642,     'lb/yd³'),
        'Density::POUND_PER_GALLON'          => array(99.776373,      'lb/gal'),
        'Density::POUND_PER_KILOGALLON'      => array(0.099776373,    'lb/kgal'),
        'Density::POUND_PER_MEGAGALLON'      => array(0.000099776373, 'lb/Mgal'),
        'Density::POUND_PER_GALLON_US'       => array(119.82643,      'lb/gal'),
        'Density::POUND_PER_KILOGALLON_US'   => array(0.11982643,     'lb/kgal'),
        'Density::POUND_PER_MEGAGALLON_US'   => array(0.00011982643,  'lb/Mgal'),
        'Density::SILVER'                    => array(10510,          'silver'),
        'Density::SLUG_PER_CUBIC_FOOT'       => array(515.37882,      'slug/ft³'),
        'Density::SLUG_PER_CUBIC_INCH'       => array(890574.6,       'slug/in³'),
        'Density::SLUG_PER_CUBIC_YARD'       => array(19.088104,      'slug/yd³'),
        'Density::SLUG_PER_GALLON'           => array(3210.2099,      'slug/gal'),
        'Density::SLUG_PER_GALLON_US'        => array(3855.3013,      'slug/gal'),
        'Density::TON_PER_CUBIC_FOOT_LONG'   => array(35881.358,      't/ft³'),
        'Density::TON_PER_CUBIC_FOOT'        => array(32036.927,      't/ft³'),
        'Density::TON_PER_CUBIC_INCH_LONG'   => array(6.2202987e+7,   't/in³'),
        'Density::TON_PER_CUBIC_INCH'        => array(5.5359809e+7,   't/in³'),
        'Density::TON_PER_CUBIC_YARD_LONG'   => array(1328.9392,      't/yd³'),
        'Density::TON_PER_CUBIC_YARD'        => array(1186.5528,      't/yd³'),
        'Density::TON_PER_GALLON_LONG'       => array(223499.07,      't/gal'),
        'Density::TON_PER_GALLON_US_LONG'    => array(268411.2,       't/gal'),
        'Density::TON_PER_GALLON'            => array(199522.75,      't/gal'),
        'Density::TON_PER_GALLON_US'         => array(239652.85,      't/gal'),
        'Density::TONNE_PER_CUBIC_CENTIMETER' => array(1.0e+9,        't/cm³'),
        'Density::TONNE_PER_CUBIC_DECIMETER'  => array(1000000,       't/dm³'),
        'Density::TONNE_PER_CUBIC_METER'     => array(1000,           't/m³'),
        'Density::TONNE_PER_LITER'           => array(1000000,        't/l'),
        'Density::TONNE_PER_MILLILITER'      => array(1.0e+9,         't/ml'),
        'Density::WATER'                     => array(1000,           'water')
    );

    private $_Locale;

    /**
     * Zend_Measure_Density provides an locale aware class for
     * conversion and formatting of density values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Density Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Density Type
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
            self::throwException('unknown type of density:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of density:' . $type);
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