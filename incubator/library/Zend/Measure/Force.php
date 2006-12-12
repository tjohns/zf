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
 * @subpackage Zend_Measure_Force
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Force extends Zend_Measure_Abstract
{
    // Force definitions
    const STANDARD = 'Force::NEWTON';

    const ATTONEWTON      = 'Force::ATTONEWTON';
    const CENTINEWTON     = 'Force::CENTINEWTON';
    const DECIGRAM_FORCE  = 'Force::DECIGRAM_FORCE';
    const DECINEWTON      = 'Force::DECINEWTON';
    const DEKAGRAM_FORCE  = 'Force::DEKAGRAM_FORCE';
    const DEKANEWTON      = 'Force::DEKANEWTON';
    const DYNE            = 'Force::DYNE';
    const EXANEWTON       = 'Force::EXANEWTON';
    const FEMTONEWTON     = 'Force::FEMTONEWTON';
    const GIGANEWTON      = 'Force::GIGANEWTON';
    const GRAM_FORCE      = 'Force::GRAM_FORCE';
    const HECTONEWTON     = 'Force::HECTONEWTON';
    const JOULE_PER_METER = 'Force::JOULE_PER_METER';
    const KILOGRAM_FORCE  = 'Force::KILOGRAM_FORCE';
    const KILONEWTON      = 'Force::KILONEWTON';
    const KILOPOND        = 'Force::KILOPOND';
    const KIP             = 'Force::KIP';
    const MEGANEWTON      = 'Force::MEGANEWTON';
    const MEGAPOND        = 'Force::MEGAPOND';
    const MICRONEWTON     = 'Force::MICRONEWTON';
    const MILLINEWTON     = 'Force::MILLINEWTON';
    const NANONEWTON      = 'Force::NANONEWTON';
    const NEWTON          = 'Force::NEWTON';
    const OUNCE_FORCE     = 'Force::OUNCE_FORCE';
    const PETANEWTON      = 'Force::PETANEWTON';
    const PICONEWTON      = 'Force::PICONEWTON';
    const POND            = 'Force::POND';
    const POUND_FORCE     = 'Force::POUND_FORCE';
    const POUNDAL         = 'Force::POUNDAL';
    const STHENE          = 'Force::STHENE';
    const TERANEWTON      = 'Force::TERANEWTON';
    const TON_FORCE_LONG  = 'Force::TON_FORCE_LONG';
    const TON_FORCE       = 'Force::TON_FORCE';
    const TON_FORCE_SHORT = 'Force::TON_FORCE_SHORT';
    const YOCTONEWTON     = 'Force::YOCTONEWTON';
    const YOTTANEWTON     = 'Force::YOTTANEWTON';
    const ZEPTONEWTON     = 'Force::ZEPTONEWTON';
    const ZETTANEWTON = 'Force::ZETTANEWTON';

    private static $_UNITS = array(
        'Force::ATTONEWTON'      => array(1.0e-18,     'aN'),
        'Force::CENTINEWTON'     => array(0.01,        'cN'),
        'Force::DECIGRAM_FORCE'  => array(0.000980665, 'dgf'),
        'Force::DECINEWTON'      => array(0.1,         'dN'),
        'Force::DEKAGRAM_FORCE'  => array(0.0980665,   'dagf'),
        'Force::DEKANEWTON'      => array(10,          'daN'),
        'Force::DYNE'            => array(0.00001,     'dyn'),
        'Force::EXANEWTON'       => array(1.0e+18,     'EN'),
        'Force::FEMTONEWTON'     => array(1.0e-15,     'fN'),
        'Force::GIGANEWTON'      => array(1.0e+9,      'GN'),
        'Force::GRAM_FORCE'      => array(0.00980665,  'gf'),
        'Force::HECTONEWTON'     => array(100,         'hN'),
        'Force::JOULE_PER_METER' => array(1,           'J/m'),
        'Force::KILOGRAM_FORCE'  => array(9.80665,     'kgf'),
        'Force::KILONEWTON'      => array(1000,        'kN'),
        'Force::KILOPOND'        => array(9.80665,     'kp'),
        'Force::KIP'             => array(4448.2216,   'kip'),
        'Force::MEGANEWTON'      => array(1000000,     'Mp'),
        'Force::MEGAPOND'        => array(9806.65,     'MN'),
        'Force::MICRONEWTON'     => array(0.000001,    'µN'),
        'Force::MILLINEWTON'     => array(0.001,       'mN'),
        'Force::NANONEWTON'      => array(0.000000001, 'nN'),
        'Force::NEWTON'          => array(1,           'N'),
        'Force::OUNCE_FORCE'     => array(0.27801385,  'ozf'),
        'Force::PETANEWTON'      => array(1.0e+15,     'PN'),
        'Force::PICONEWTON'      => array(1.0e-12,     'pN'),
        'Force::POND'            => array(0.00980665,  'pond'),
        'Force::POUND_FORCE'     => array(4.4482216,   'lbf'),
        'Force::POUNDAL'         => array(0.13825495,  'pdl'),
        'Force::STHENE'          => array(1000,        'sn'),
        'Force::TERANEWTON'      => array(1.0e+12,     'TN'),
        'Force::TON_FORCE_LONG'  => array(9964.016384, 'tnf'),
        'Force::TON_FORCE'       => array(9806.65,     'tnf'),
        'Force::TON_FORCE_SHORT' => array(8896.4432,   'tnf'),
        'Force::YOCTONEWTON'     => array(1.0e-24,     'yN'),
        'Force::YOTTANEWTON'     => array(1.0e+24,     'YN'),
        'Force::ZEPTONEWTON'     => array(1.0e-21,     'zN'),
        'Force::ZETTANEWTON'     => array(1.0e+21,     'ZN')
    );

    private $_Locale;

    /**
     * Zend_Measure_Force provides an locale aware class for
     * conversion and formatting of force values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Force Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Force Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        try {
            $value = Zend_Locale_Format::getNumber($value, $locale);
        } catch(Exception $e) {
            throw Zend::exception('Zend_Measure_Exception', $e->getMessage());
        }

        if (empty(self::$_UNITS[$type])) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of force:' . $type);
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of force:' . $type);
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