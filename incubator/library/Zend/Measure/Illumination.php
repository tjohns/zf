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
 * @subpackage Zend_Measure_Illumination
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Illumination extends Zend_Measure_Abstract
{
    // Illumination definitions
    const STANDARD = 'Illumination::LUX';

    const FOOTCANDLE              = 'Illumination::FOOTCANDLE';
    const KILOLUX                 = 'Illumination::KILOLUX';
    const LUMEN_PER_SQUARE_CENTIMETER = 'Illumination::LUMEN_PER_SQUARE_CENTIMETER';
    const LUMEN_PER_SQUARE_FOOT   = 'Illumination::LUMEN_PER_SQUARE_FOOT';
    const LUMEN_PER_SQUARE_INCH   = 'Illumination::LUMEN_PER_SQUARE_INCH';
    const LUMEN_PER_SQUARE_METER  = 'Illumination::LUMEN_PER_SQUARE_METER';
    const LUX                     = 'Illumination::LUX';
    const METERCANDLE             = 'Illumination::METERCANDLE';
    const MILLIPHOT               = 'Illumination::MILLIPHOT';
    const NOX                     = 'Illumination::NOX';
    const PHOT                    = 'Illumination::PHOT';

    private static $_UNITS = array(
        'Illumination::FOOTCANDLE'              => array(10.7639104,   'fc'),
        'Illumination::KILOLUX'                 => array(1000,         'klx'),
        'Illumination::LUMEN_PER_SQUARE_CENTIMETER' => array(10000,    'lm/cm²'),
        'Illumination::LUMEN_PER_SQUARE_FOOT'   => array(10.7639104,   'lm/ft²'),
        'Illumination::LUMEN_PER_SQUARE_INCH'   => array(1550.0030976, 'lm/in²'),
        'Illumination::LUMEN_PER_SQUARE_METER'  => array(1,            'lm/m²'),
        'Illumination::LUX'                     => array(1,            'lx'),
        'Illumination::METERCANDLE'             => array(1,            'metercandle'),
        'Illumination::MILLIPHOT'               => array(10,           'mph'),
        'Illumination::NOX'                     => array(0.001,        'nox'),
        'Illumination::PHOT'                    => array(10000,        'ph')
    );

    private $_Locale;

    /**
     * Zend_Measure_Illumination provides an locale aware class for
     * conversion and formatting of Illumination values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Illumination Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type, $locale = false)
    {
        if (empty( $locale )) {
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Illumination Type
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
            self::throwException('unknown type of illumination:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType( $type );
    }


    /**
     * Set a new type, and convert the value
     *
     * @throws Zend_Measure_Exception
     */
    public function setType( $type )
    {
        if (empty( self::$_UNITS[$type] )) {
            self::throwException('unknown type of illumination:' . $type);
        }

        // Convert to standard value
        $value = parent::getValue();
        $value = $value * (self::$_UNITS[parent::getType()][0]);

        // Convert to expected value
        $value = $value / (self::$_UNITS[$type][0]);
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