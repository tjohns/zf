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
 * @subpackage Zend_Measure_Cooking_Volume
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Cooking_Volume extends Zend_Measure_Abstract
{
    // Cooking_Volume definitions
    const STANDARD = 'Cooking_Volume::CUBIC_METER';

    const CAN_2POINT5       = 'Cooking_Volume::CAN_2POINT5';
    const CAN_10            = 'Cooking_Volume::CAN_10';
    const BARREL_WINE       = 'Cooking_Volume::BARREL_WINE';
    const BARREL            = 'Cooking_Volume::BARREL';
    const BARREL_US_DRY     = 'Cooking_Volume::BARREL_US_DRY';
    const BARREL_US_FEDERAL = 'Cooking_Volume::BARREL_US_FEDERAL';
    const BARREL_US         = 'Cooking_Volume::BARREL_US';
    const BUCKET            = 'Cooking_Volume::BUCKET';
    const BUCKET_US         = 'Cooking_Volume::BUCKET_US';
    const BUSHEL            = 'Cooking_Volume::BUSHEL';
    const BUSHEL_US         = 'Cooking_Volume::BUSHEL_US';
    const CENTILITER        = 'Cooking_Volume::CENTILITER';
    const COFFEE_SPOON      = 'Cooking_Volume::COFFEE_SPOON';
    const CUBIC_CENTIMETER  = 'Cooking_Volume::CUBIC_CENTIMETER';
    const CUBIC_DECIMETER   = 'Cooking_Volume::CUBIC_DECIMETER';
    const CUBIC_FOOT        = 'Cooking_Volume::CUBIC_FOOT';
    const CUBIC_INCH        = 'Cooking_Volume::CUBIC_INCH';
    const CUBIC_METER       = 'Cooking_Volume::CUBIC_METER';
    const CUBIC_MICROMETER  = 'Cooking_Volume::CUBIC_MICROMETER';
    const CUBIC_MILLIMETER  = 'Cooking_Volume::CUBIC_MILLIMETER';
    const CUP_CANADA        = 'Cooking_Volume::CUP_CANADA';
    const CUP               = 'Cooking_Volume::CUP';
    const CUP_US            = 'Cooking_Volume::CUP_US';
    const DASH              = 'Cooking_Volume::DASH';
    const DECILITER         = 'Cooking_Volume::DECILITER';
    const DEKALITER         = 'Cooking_Volume::DEKALITER';
    const DEMI              = 'Cooking_Volume::DEMI';
    const DRAM              = 'Cooking_Volume::DRAM';
    const DROP              = 'Cooking_Volume::DROP';
    const FIFTH             = 'Cooking_Volume::FIFTH';
    const GALLON            = 'Cooking_Volume::GALLON';
    const GALLON_US_DRY     = 'Cooking_Volume::GALLON_US_DRY';
    const GALLON_US         = 'Cooking_Volume::GALLON_US';
    const GILL              = 'Cooking_Volume::GILL';
    const GILL_US           = 'Cooking_Volume::GILL_US';
    const HECTOLITER        = 'Cooking_Volume::HECTOLITER';
    const HOGSHEAD          = 'Cooking_Volume::HOGSHEAD';
    const HOGSHEAD_US       = 'Cooking_Volume::HOGSHEAD_US';
    const JIGGER            = 'Cooking_Volume::JIGGER';
    const KILOLITER         = 'Cooking_Volume::KILOLITER';
    const LITER             = 'Cooking_Volume::LITER';
    const MEASURE           = 'Cooking_Volume::MEASURE';
    const MEGALITER         = 'Cooking_Volume::MEGALITER';
    const MICROLITER        = 'Cooking_Volume::MICROLITER';
    const MILLILITER        = 'Cooking_Volume::MILLILITER';
    const MINIM             = 'Cooking_Volume::MINIM';
    const MINIM_US          = 'Cooking_Volume::MINIM_US';
    const OUNCE             = 'Cooking_Volume::OUNCE';
    const OUNCE_US          = 'Cooking_Volume::OUNCE_US';
    const PECK              = 'Cooking_Volume::PECK';
    const PECK_US           = 'Cooking_Volume::PECK_US';
    const PINCH             = 'Cooking_Volume::PINCH';
    const PINT              = 'Cooking_Volume::PINT';
    const PINT_US_DRY       = 'Cooking_Volume::PINT_US_DRY';
    const PINT_US           = 'Cooking_Volume::PINT_US';
    const PIPE              = 'Cooking_Volume::PIPE';
    const PIPE_US           = 'Cooking_Volume::PIPE_US';
    const PONY              = 'Cooking_Volume::PONY';
    const QUART_GERMANY     = 'Cooking_Volume::QUART_GERMANY';
    const QUART_ANCIENT     = 'Cooking_Volume::QUART_ANCIENT';
    const QUART             = 'Cooking_Volume::QUART';
    const QUART_US_DRY      = 'Cooking_Volume::QUART_US_DRY';
    const QUART_US          = 'Cooking_Volume::QUART_US';
    const SHOT              = 'Cooking_Volume::SHOT';
    const TABLESPOON        = 'Cooking_Volume::TABLESPOON';
    const TABLESPOON_UK     = 'Cooking_Volume::TABLESPOON_UK';
    const TABLESPOON_US     = 'Cooking_Volume::TABLESPOON_US';
    const TEASPOON          = 'Cooking_Volume::TEASPOON';
    const TEASPOON_UK       = 'Cooking_Volume::TEASPOON_UK';
    const TEASPOON_US       = 'Cooking_Volume::TEASPOON_US';

    private static $_UNITS = array(
        'Cooking_Volume::CAN_2POINT5'       => array(array('' => 0.0037854118, '/' => 16, '' => 3.5), '2.5th can'),
        'Cooking_Volume::CAN_10'            => array(array('' => 0.0037854118, '*' => 0.75),          '10th can'),
        'Cooking_Volume::BARREL_WINE'       => array(0.143201835,   'bbl'),
        'Cooking_Volume::BARREL'            => array(0.16365924,    'bbl'),
        'Cooking_Volume::BARREL_US_DRY'     => array(array('' => 26.7098656608, '/' => 231), 'bbl'),
        'Cooking_Volume::BARREL_US_FEDERAL' => array(0.1173477658,  'bbl'),
        'Cooking_Volume::BARREL_US'         => array(0.1192404717,  'bbl'),
        'Cooking_Volume::BUCKET'            => array(0.01818436,    'bucket'),
        'Cooking_Volume::BUCKET_US'         => array(0.018927059,   'bucket'),
        'Cooking_Volume::BUSHEL'            => array(0.03636872,    'bu'),
        'Cooking_Volume::BUSHEL_US'         => array(0.03523907,    'bu'),
        'Cooking_Volume::CENTILITER'        => array(0.00001,       'cl'),
        'Cooking_Volume::COFFEE_SPOON'      => array(array('' => 0.0037854118, '/' => 1536), 'coffee spoon'),
        'Cooking_Volume::CUBIC_CENTIMETER'  => array(0.000001,      'cm³'),
        'Cooking_Volume::CUBIC_DECIMETER'   => array(0.001,         'dm³'),
        'Cooking_Volume::CUBIC_FOOT'        => array(array('' => 6.54119159, '/' => 231),   'ft³'),
        'Cooking_Volume::CUBIC_INCH'        => array(array('' => 0.0037854118, '/' => 231), 'in³'),
        'Cooking_Volume::CUBIC_METER'       => array(1,             'm³'),
        'Cooking_Volume::CUBIC_MICROMETER'  => array(1.0e-18,       'µm³'),
        'Cooking_Volume::CUBIC_MILLIMETER'  => array(1.0e-9,        'mm³'),
        'Cooking_Volume::CUP_CANADA'        => array(0.0002273045,  'c'),
        'Cooking_Volume::CUP'               => array(0.00025,       'c'),
        'Cooking_Volume::CUP_US'            => array(array('' => 0.0037854118, '/' => 16),   'c'),
        'Cooking_Volume::DASH'              => array(array('' => 0.0037854118, '/' => 6144), 'ds'),
        'Cooking_Volume::DECILITER'         => array(0.0001,        'dl'),
        'Cooking_Volume::DEKALITER'         => array(0.001,         'dal'),
        'Cooking_Volume::DEMI'              => array(0.00025,       'demi'),
        'Cooking_Volume::DRAM'              => array(array('' => 0.0037854118, '/' => 1024),  'dr'),
        'Cooking_Volume::DROP'              => array(array('' => 0.0037854118, '/' => 73728), 'ggt'),
        'Cooking_Volume::FIFTH'             => array(0.00075708236, 'fifth'),
        'Cooking_Volume::GALLON'            => array(0.00454609,    'gal'),
        'Cooking_Volume::GALLON_US_DRY'     => array(0.0044048838,  'gal'),
        'Cooking_Volume::GALLON_US'         => array(0.0037854118,  'gal'),
        'Cooking_Volume::GILL'              => array(array('' => 0.00454609, '/' => 32),   'gi'),
        'Cooking_Volume::GILL_US'           => array(array('' => 0.0037854118, '/' => 32), 'gi'),
        'Cooking_Volume::HECTOLITER'        => array(0.1,           'hl'),
        'Cooking_Volume::HOGSHEAD'          => array(0.28640367,    'hhd'),
        'Cooking_Volume::HOGSHEAD_US'       => array(0.2384809434,  'hhd'),
        'Cooking_Volume::JIGGER'            => array(array('' => 0.0037854118, '/' => 128, '*' => 1.5), 'jigger'),
        'Cooking_Volume::KILOLITER'         => array(1,             'kl'),
        'Cooking_Volume::LITER'             => array(0.001,         'l'),
        'Cooking_Volume::MEASURE'           => array(0.0077,        'measure'),
        'Cooking_Volume::MEGALITER'         => array(1000,          'Ml'),
        'Cooking_Volume::MICROLITER'        => array(1.0e-9,        'µl'),
        'Cooking_Volume::MILLILITER'        => array(0.000001,      'ml'),
        'Cooking_Volume::MINIM'             => array(array('' => 0.00454609, '/' => 76800),  'min'),
        'Cooking_Volume::MINIM_US'          => array(array('' => 0.0037854118,'/' => 61440), 'min'),
        'Cooking_Volume::OUNCE'             => array(array('' => 0.00454609, '/' => 160),    'oz'),
        'Cooking_Volume::OUNCE_US'          => array(array('' => 0.0037854118, '/' => 128),  'oz'),
        'Cooking_Volume::PECK'              => array(0.00909218,    'pk'),
        'Cooking_Volume::PECK_US'           => array(0.0088097676,  'pk'),
        'Cooking_Volume::PINCH'             => array(array('' => 0.0037854118, '/' => 12288), 'pinch'),
        'Cooking_Volume::PINT'              => array(array('' => 0.00454609, '/' => 8),       'pt'),
        'Cooking_Volume::PINT_US_DRY'       => array(array('' => 0.0044048838, '/' => 8),     'pt'),
        'Cooking_Volume::PINT_US'           => array(array('' => 0.0037854118, '/' => 8),     'pt'),
        'Cooking_Volume::PIPE'              => array(0.49097772,    'pipe'),
        'Cooking_Volume::PIPE_US'           => array(0.4769618868,  'pipe'),
        'Cooking_Volume::PONY'              => array(array('' => 0.0037854118, '/' => 128), 'pony'),
        'Cooking_Volume::QUART_GERMANY'     => array(0.00114504,    'qt'),
        'Cooking_Volume::QUART_ANCIENT'     => array(0.00108,       'qt'),
        'Cooking_Volume::QUART'             => array(array('' => 0.00454609, '/' => 4),     'qt'),
        'Cooking_Volume::QUART_US_DRY'      => array(array('' => 0.0044048838, '/' => 4),   'qt'),
        'Cooking_Volume::QUART_US'          => array(array('' => 0.0037854118, '/' => 4),   'qt'),
        'Cooking_Volume::SHOT'              => array(array('' => 0.0037854118, '/' => 128), 'shot'),
        'Cooking_Volume::TABLESPOON'        => array(0.000015,      'tbsp'),
        'Cooking_Volume::TABLESPOON_UK'     => array(array('' => 0.00454609, '/' => 320),   'tbsp'),
        'Cooking_Volume::TABLESPOON_US'     => array(array('' => 0.0037854118, '/' => 256), 'tbsp'),
        'Cooking_Volume::TEASPOON'          => array(0.000005,      'tsp'),
        'Cooking_Volume::TEASPOON_UK'       => array(array('' => 0.00454609, '/' => 1280),  'tsp'),
        'Cooking_Volume::TEASPOON_US'       => array(array('' => 0.0037854118, '/' => 768), 'tsp')
    );

    private $_Locale;

    /**
     * Zend_Measure_Cooking_Volume provides an locale aware class for
     * conversion and formatting of Cooking_Volume values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Cooking_Volume Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Cooking_Volume Type
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
            self::throwException('unknown type of volume-cooking:' . $type);
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
            self::throwException('unknown type of volume-cooking:' . $type);
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