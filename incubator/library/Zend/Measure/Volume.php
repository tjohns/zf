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
 * @subpackage Zend_Measure_Volume
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Volume extends Zend_Measure_Abstract
{
    // Volume definitions
    const STANDARD = 'Volume::CUBIC_METER';

    const ACRE_FOOT           = 'Volume::ACRE_FOOT';
    const ACRE_FOOT_SURVEY    = 'Volume::ACRE_FOOT_SURVEY';
    const ACRE_INCH           = 'Volume::ACRE_INCH';
    const BARREL_WINE         = 'Volume::BARREL_WINE';
    const BARREL              = 'Volume::BARREL';
    const BARREL_US_DRY       = 'Volume::BARREL_US_DRY';
    const BARREL_US_FEDERAL   = 'Volume::BARREL_US_FEDERAL';
    const BARREL_US           = 'Volume::BARREL_US';
    const BARREL_US_PETROLEUM = 'Volume::BARREL_US_PETROLEUM';
    const BOARD_FOOT          = 'Volume::BOARD_FOOT';
    const BUCKET              = 'Volume::BUCKET';
    const BUCKET_US           = 'Volume::BUCKET_US';
    const BUSHEL              = 'Volume::BUSHEL';
    const BUSHEL_US           = 'Volume::BUSHEL_US';
    const CENTILTER           = 'Volume::CENTILITER';
    const CORD                = 'Volume::CORD';
    const CORD_FOOT           = 'Volume::CORD_FOOT';
    const CUBIC_CENTIMETER    = 'Volume::CUBIC_CENTIMETER';
    const CUBIC_CUBIT         = 'Volume::CUBIC_CUBIT';
    const CUBIC_DECIMETER     = 'Volume::CUBIC_DECIMETER';
    const CUBIC_DEKAMETER     = 'Volume::CUBIC_DEKAMETER';
    const CUBIC_FOOT          = 'Volume::CUBIC_FOOT';
    const CUBIC_INCH          = 'Volume::CUBIC_INCH';
    const CUBIC_KILOMETER     = 'Volume::CUBIC_KILOMETER';
    const CUBIC_METER         = 'Volume::CUBIC_METER';
    const CUBIC_MILE          = 'Volume::CUBIC_MILE';
    const CUBIC_MICROMETER    = 'Volume::CUBIC_MICROMETER';
    const CUBIC_MILLIMETER    = 'Volume::CUBIC_MILLIMETER';
    const CUBIC_YARD          = 'Volume::CUBIC_YARD';
    const CUP_CANADA          = 'Volume::CUP_CANADA';
    const CUP                 = 'Volume::CUP';
    const CUP_US              = 'Volume::CUP_US';
    const DECILITER           = 'Volume::DECILITER';
    const DEKALITER           = 'Volume::DEKALITER';
    const DRAM                = 'Volume::DRAM';
    const DRUM_US             = 'Volume::DRUM_US';
    const DRUM                = 'Volume::DRUM';
    const FIFTH               = 'Volume::FIFTH';
    const GALLON              = 'Volume::GALLON';
    const GALLON_US_DRY       = 'Volume::GALLON_US_DRY';
    const GALLON_US           = 'Volume::GALLON_US';
    const GILL                = 'Volume::GILL';
    const GILL_US             = 'Volume::GILL_US';
    const HECTARE_METER       = 'Volume::HECTARE_METER';
    const HECTOLITER          = 'Volume::HECTOLITER';
    const HOGSHEAD            = 'Volume::HOGSHEAD';
    const HOGSHEAD_US         = 'Volume::HOGSHEAD_US';
    const JIGGER              = 'Volume::JIGGER';
    const KILOLITER           = 'Volume::KILOLITER';
    const LITER               = 'Volume::LITER';
    const MEASURE             = 'Volume::MEASURE';
    const MEGALITER           = 'Volume::MEGALITER';
    const MICROLITER          = 'Volume::MICROLITER';
    const MILLILITER          = 'Volume::MILLILITER';
    const MINIM               = 'Volume::MINIM';
    const MINIM_US            = 'Volume::MINIM_US';
    const OUNCE               = 'Volume::OUNCE';
    const OUNCE_US            = 'Volume::OUNCE_US';
    const PECK                = 'Volume::PECK';
    const PECK_US             = 'Volume::PECK_US';
    const PINT                = 'Volume::PINT';
    const PINT_US_DRY         = 'Volume::PINT_US_DRY';
    const PINT_US             = 'Volume::PINT_US';
    const PIPE                = 'Volume::PIPE';
    const PIPE_US             = 'Volume::PIPE_US';
    const PONY                = 'Volume::PONY';
    const QUART_GERMANY       = 'Volume::QUART_GERMANY';
    const QUART_ANCIENT       = 'Volume::QUART_ANCIENT';
    const QUART               = 'Volume::QUART';
    const QUART_US_DRY        = 'Volume::QUART_US_DRY';
    const QUART_US            = 'Volume::QUART_US';
    const QUART_UK            = 'Volume::QUART_UK';
    const SHOT                = 'Volume::SHOT';
    const STERE               = 'Volume::STERE';
    const TABLESPOON          = 'Volume::TABLESPOON';
    const TABLESPOON_UK       = 'Volume::TABLESPOON_UK';
    const TABLESPOON_US       = 'Volume::TABLESPOON_US';
    const TEASPOON            = 'Volume::TEASPOON';
    const TEASPOON_UK         = 'Volume::TEASPOON_UK';
    const TEASPOON_US         = 'Volume::TEASPOON_US';
    const YARD                = 'Volume::YARD';

    private static $_UNITS = array(
        'Volume::ACRE_FOOT'           => array(1233.48185532, 'ac ft'),
        'Volume::ACRE_FOOT_SURVEY'    => array(1233.489,      'ac ft'),
        'Volume::ACRE_INCH'           => array(102.79015461,  'ac in'),
        'Volume::BARREL_WINE'         => array(0.143201835,   'bbl'),
        'Volume::BARREL'              => array(0.16365924,    'bbl'),
        'Volume::BARREL_US_DRY'       => array(array('' => 26.7098656608, '/' => 231), 'bbl'),
        'Volume::BARREL_US_FEDERAL'   => array(0.1173477658,  'bbl'),
        'Volume::BARREL_US'           => array(0.1192404717,  'bbl'),
        'Volume::BARREL_US_PETROLEUM' => array(0.1589872956,  'bbl'),
        'Volume::BOARD_FOOT'          => array(array('' => 6.5411915904, '/' => 2772), 'board foot'),
        'Volume::BUCKET'              => array(0.01818436,    'bucket'),
        'Volume::BUCKET_US'           => array(0.018927059,   'bucket'),
        'Volume::BUSHEL'              => array(0.03636872,    'bu'),
        'Volume::BUSHEL_US'           => array(0.03523907,    'bu'),
        'Volume::CENTILITER'          => array(0.00001,       'cl'),
        'Volume::CORD'                => array(3.624556416,   'cd'),
        'Volume::CORD_FOOT'           => array(0.453069552,   'cd ft'),
        'Volume::CUBIC_CENTIMETER'    => array(0.000001,      'cm³'),
        'Volume::CUBIC_CUBIT'         => array(0.144,         'cubit³'),
        'Volume::CUBIC_DECIMETER'     => array(0.001,         'dm³'),
        'Volume::CUBIC_DEKAMETER'     => array(1000,          'dam³'),
        'Volume::CUBIC_FOOT'          => array(array('' => 6.54119159, '/' => 231),   'ft³'),
        'Volume::CUBIC_INCH'          => array(array('' => 0.0037854118, '/' => 231), 'in³'),
        'Volume::CUBIC_KILOMETER'     => array(1.0e+9,        'km³'),
        'Volume::CUBIC_METER'         => array(1,             'm³'),
        'Volume::CUBIC_MILE'          => array(array('' => 0.0037854118, '/' => 231, '*' => 75271680, '*' => 3379200), 'mi³'),
        'Volume::CUBIC_MICROMETER'    => array(1.0e-18,       'µm³'),
        'Volume::CUBIC_MILLIMETER'    => array(1.0e-9,        'mm³'),
        'Volume::CUBIC_YARD'          => array(array('' => 0.0037854118, '/' => 231, '*' => 46656), 'yd³'),
        'Volume::CUP_CANADA'          => array(0.0002273045,  'c'),
        'Volume::CUP'                 => array(0.00025,       'c'),
        'Volume::CUP_US'              => array(array('' => 0.0037854118, '/' => 16), 'c'),
        'Volume::DECILITER'           => array(0.0001,        'dl'),
        'Volume::DEKALITER'           => array(0.001,         'dal'),
        'Volume::DRAM'                => array(array('' => 0.0037854118, '/' => 1024), 'dr'),
        'Volume::DRUM_US'             => array(0.208197649,   'drum'),
        'Volume::DRUM'                => array(0.2,           'drum'),
        'Volume::FIFTH'               => array(0.00075708236, 'fifth'),
        'Volume::GALLON'              => array(0.00454609,    'gal'),
        'Volume::GALLON_US_DRY'       => array(0.0044048838,  'gal'),
        'Volume::GALLON_US'           => array(0.0037854118,  'gal'),
        'Volume::GILL'                => array(array('' => 0.00454609, '/' => 32),   'gi'),
        'Volume::GILL_US'             => array(array('' => 0.0037854118, '/' => 32), 'gi'),
        'Volume::HECTARE_METER'       => array(10000,         'ha m'),
        'Volume::HECTOLITER'          => array(0.1,           'hl'),
        'Volume::HOGSHEAD'            => array(0.28640367,    'hhd'),
        'Volume::HOGSHEAD_US'         => array(0.2384809434,  'hhd'),
        'Volume::JIGGER'              => array(array('' => 0.0037854118, '/' => 128, '*' => 1.5), 'jigger'),
        'Volume::KILOLITER'           => array(1,             'kl'),
        'Volume::LITER'               => array(0.001,         'l'),
        'Volume::MEASURE'             => array(0.0077,        'measure'),
        'Volume::MEGALITER'           => array(1000,          'Ml'),
        'Volume::MICROLITER'          => array(1.0e-9,        'µl'),
        'Volume::MILLILITER'          => array(0.000001,      'ml'),
        'Volume::MINIM'               => array(array('' => 0.00454609, '/' => 76800),  'min'),
        'Volume::MINIM_US'            => array(array('' => 0.0037854118,'/' => 61440), 'min'),
        'Volume::OUNCE'               => array(array('' => 0.00454609, '/' => 160),    'oz'),
        'Volume::OUNCE_US'            => array(array('' => 0.0037854118, '/' => 128),  'oz'),
        'Volume::PECK'                => array(0.00909218,    'pk'),
        'Volume::PECK_US'             => array(0.0088097676,  'pk'),
        'Volume::PINT'                => array(array('' => 0.00454609, '/' => 8),   'pt'),
        'Volume::PINT_US_DRY'         => array(array('' => 0.0044048838, '/' => 8), 'pt'),
        'Volume::PINT_US'             => array(array('' => 0.0037854118, '/' => 8), 'pt'),
        'Volume::PIPE'                => array(0.49097772,    'pipe'),
        'Volume::PIPE_US'             => array(0.4769618868,  'pipe'),
        'Volume::PONY'                => array(array('' => 0.0037854118, '/' => 128), 'pony'),
        'Volume::QUART_GERMANY'       => array(0.00114504,    'qt'),
        'Volume::QUART_ANCIENT'       => array(0.00108,       'qt'),
        'Volume::QUART'               => array(array('' => 0.00454609, '/' => 4),   'qt'),
        'Volume::QUART_US_DRY'        => array(array('' => 0.0044048838, '/' => 4), 'qt'),
        'Volume::QUART_US'            => array(array('' => 0.0037854118, '/' => 4), 'qt'),
        'Volume::QUART_UK'            => array(0.29094976,    'qt'),
        'Volume::SHOT'                => array(array('' => 0.0037854118, '/' => 128), 'shot'),
        'Volume::STERE'               => array(1,             'st'),
        'Volume::TABLESPOON'          => array(0.000015,      'tbsp'),
        'Volume::TABLESPOON_UK'       => array(array('' => 0.00454609, '/' => 320),   'tbsp'),
        'Volume::TABLESPOON_US'       => array(array('' => 0.0037854118, '/' => 256), 'tbsp'),
        'Volume::TEASPOON'            => array(0.000005,      'tsp'),
        'Volume::TEASPOON_UK'         => array(array('' => 0.00454609, '/' => 1280),    'tsp'),
        'Volume::TEASPOON_US'         => array(array('' => 0.0037854118, '/' => 768),   'tsp'),
        'Volume::YARD'                => array(array('' => 176.6121729408, '/' => 231), 'yd')
    );

    private $_Locale;

    /**
     * Zend_Measure_Volume provides an locale aware class for
     * conversion and formatting of volume values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Volume Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Volume Type
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
            self::throwException('unknown type of volume:' . $type);
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
        if (empty( self::$_UNITS[$type] )) {
            self::throwException('unknown type of volume:' . $type);
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