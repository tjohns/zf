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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement needed classes
 */
require_once 'Zend/Measure/Exception.php';
require_once 'Zend/Measure/Abstract.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Force
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Force extends Zend_Measure_Abstract
{
    // Force definitions
    const STANDARD = 'NEWTON';

    const ATTONEWTON      = 'ATTONEWTON';
    const CENTINEWTON     = 'CENTINEWTON';
    const DECIGRAM_FORCE  = 'DECIGRAM_FORCE';
    const DECINEWTON      = 'DECINEWTON';
    const DEKAGRAM_FORCE  = 'DEKAGRAM_FORCE';
    const DEKANEWTON      = 'DEKANEWTON';
    const DYNE            = 'DYNE';
    const EXANEWTON       = 'EXANEWTON';
    const FEMTONEWTON     = 'FEMTONEWTON';
    const GIGANEWTON      = 'GIGANEWTON';
    const GRAM_FORCE      = 'GRAM_FORCE';
    const HECTONEWTON     = 'HECTONEWTON';
    const JOULE_PER_METER = 'JOULE_PER_METER';
    const KILOGRAM_FORCE  = 'KILOGRAM_FORCE';
    const KILONEWTON      = 'KILONEWTON';
    const KILOPOND        = 'KILOPOND';
    const KIP             = 'KIP';
    const MEGANEWTON      = 'MEGANEWTON';
    const MEGAPOND        = 'MEGAPOND';
    const MICRONEWTON     = 'MICRONEWTON';
    const MILLINEWTON     = 'MILLINEWTON';
    const NANONEWTON      = 'NANONEWTON';
    const NEWTON          = 'NEWTON';
    const OUNCE_FORCE     = 'OUNCE_FORCE';
    const PETANEWTON      = 'PETANEWTON';
    const PICONEWTON      = 'PICONEWTON';
    const POND            = 'POND';
    const POUND_FORCE     = 'POUND_FORCE';
    const POUNDAL         = 'POUNDAL';
    const STHENE          = 'STHENE';
    const TERANEWTON      = 'TERANEWTON';
    const TON_FORCE_LONG  = 'TON_FORCE_LONG';
    const TON_FORCE       = 'TON_FORCE';
    const TON_FORCE_SHORT = 'TON_FORCE_SHORT';
    const YOCTONEWTON     = 'YOCTONEWTON';
    const YOTTANEWTON     = 'YOTTANEWTON';
    const ZEPTONEWTON     = 'ZEPTONEWTON';
    const ZETTANEWTON = 'ZETTANEWTON';

    private static $_UNITS = array(
        'ATTONEWTON'      => array(1.0e-18,     'aN'),
        'CENTINEWTON'     => array(0.01,        'cN'),
        'DECIGRAM_FORCE'  => array(0.000980665, 'dgf'),
        'DECINEWTON'      => array(0.1,         'dN'),
        'DEKAGRAM_FORCE'  => array(0.0980665,   'dagf'),
        'DEKANEWTON'      => array(10,          'daN'),
        'DYNE'            => array(0.00001,     'dyn'),
        'EXANEWTON'       => array(1.0e+18,     'EN'),
        'FEMTONEWTON'     => array(1.0e-15,     'fN'),
        'GIGANEWTON'      => array(1.0e+9,      'GN'),
        'GRAM_FORCE'      => array(0.00980665,  'gf'),
        'HECTONEWTON'     => array(100,         'hN'),
        'JOULE_PER_METER' => array(1,           'J/m'),
        'KILOGRAM_FORCE'  => array(9.80665,     'kgf'),
        'KILONEWTON'      => array(1000,        'kN'),
        'KILOPOND'        => array(9.80665,     'kp'),
        'KIP'             => array(4448.2216,   'kip'),
        'MEGANEWTON'      => array(1000000,     'Mp'),
        'MEGAPOND'        => array(9806.65,     'MN'),
        'MICRONEWTON'     => array(0.000001,    'µN'),
        'MILLINEWTON'     => array(0.001,       'mN'),
        'NANONEWTON'      => array(0.000000001, 'nN'),
        'NEWTON'          => array(1,           'N'),
        'OUNCE_FORCE'     => array(0.27801385,  'ozf'),
        'PETANEWTON'      => array(1.0e+15,     'PN'),
        'PICONEWTON'      => array(1.0e-12,     'pN'),
        'POND'            => array(0.00980665,  'pond'),
        'POUND_FORCE'     => array(4.4482216,   'lbf'),
        'POUNDAL'         => array(0.13825495,  'pdl'),
        'STHENE'          => array(1000,        'sn'),
        'TERANEWTON'      => array(1.0e+12,     'TN'),
        'TON_FORCE_LONG'  => array(9964.016384, 'tnf'),
        'TON_FORCE'       => array(9806.65,     'tnf'),
        'TON_FORCE_SHORT' => array(8896.4432,   'tnf'),
        'YOCTONEWTON'     => array(1.0e-24,     'yN'),
        'YOTTANEWTON'     => array(1.0e+24,     'YN'),
        'ZEPTONEWTON'     => array(1.0e-21,     'zN'),
        'ZETTANEWTON'     => array(1.0e+21,     'ZN')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Force provides an locale aware class for
     * conversion and formatting of force values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Force Type
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing numbers
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type = null, $locale = null)
    {
        $this->setValue($value, $type, $locale);
    }


    /**
     * Compare if the value and type is equal
     *
     * @param  Zend_Measure_Force  $object  Force object to compare
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
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Force Type
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing numbers
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type = null, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        if (!$locale = Zend_Locale::isLocale($locale, true)) {
            throw new Zend_Measure_Exception("language ($locale) is a unknown language");
        }

        if ($type === null) {
            $type = self::STANDARD;
        }

        try {
            $value = Zend_Locale_Format::getNumber($value, $locale);
        } catch(Exception $e) {
            throw new Zend_Measure_Exception($e->getMessage());
        }

        if (empty(self::$_UNITS[$type])) {
            throw new Zend_Measure_Exception("type ($type) is a unknown force");
        }

        parent::setValue($value, $type, $locale);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @param  string  $type  New type to set
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type])) {
            throw new Zend_Measure_Exception("type ($type) is a unknown force");
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
