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
 * @subpackage Zend_Measure_Torque
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Torque extends Zend_Measure_Abstract
{
    // Torque definitions
    const STANDARD = 'Torque::NEWTON_METER';

    const DYNE_CENTIMETER     = 'Torque::DYNE_CENTIMETER';
    const GRAM_CENTIMETER     = 'Torque::GRAM_CENTIMETER';
    const KILOGRAM_CENTIMETER = 'Torque::KILOGRAM_CENTIMETER';
    const KILOGRAM_METER      = 'Torque::KILOGRAM_METER';
    const KILONEWTON_METER    = 'Torque::KILONEWTON_METER';
    const KILOPOND_METER      = 'Torque::KILOPOND_METER';
    const MEGANEWTON_METER    = 'Torque::MEGANEWTON_METER';
    const MICRONEWTON_METER   = 'Torque::MICRONEWTON_METER';
    const MILLINEWTON_METER   = 'Torque::MILLINEWTON_METER';
    const NEWTON_CENTIMETER   = 'Torque::NEWTON_CENTIMETER';
    const NEWTON_METER        = 'Torque::NEWTON_METER';
    const OUNCE_FOOT          = 'Torque::OUNCE_FOOT';
    const OUNCE_INCH          = 'Torque::OUNCE_INCH';
    const POUND_FOOT          = 'Torque::POUND_FOOT';
    const POUNDAL_FOOT        = 'Torque::POUNDAL_FOOT';
    const POUND_INCH          = 'Torque::POUND_INCH';

    private static $_UNITS = array(
        'Torque::DYNE_CENTIMETER'     => array(0.0000001,          'dyncm'),
        'Torque::GRAM_CENTIMETER'     => array(0.0000980665,       'gcm'),
        'Torque::KILOGRAM_CENTIMETER' => array(0.0980665,          'kgcm'),
        'Torque::KILOGRAM_METER'      => array(9.80665,            'kgm'),
        'Torque::KILONEWTON_METER'    => array(1000,               'kNm'),
        'Torque::KILOPOND_METER'      => array(9.80665,            'kpm'),
        'Torque::MEGANEWTON_METER'    => array(1000000,            'MNm'),
        'Torque::MICRONEWTON_METER'   => array(0.000001,           'µNm'),
        'Torque::MILLINEWTON_METER'   => array(0.001,              'mNm'),
        'Torque::NEWTON_CENTIMETER'   => array(0.01,               'Ncm'),
        'Torque::NEWTON_METER'        => array(1,                  'Nm'),
        'Torque::OUNCE_FOOT'          => array(0.084738622,        'ozft'),
        'Torque::OUNCE_INCH'          => array(array('' => 0.084738622, '/' => 12), 'ozin'),
        'Torque::POUND_FOOT'          => array(array('' => 0.084738622, '*' => 16), 'lbft'),
        'Torque::POUNDAL_FOOT'        => array(0.0421401099752144, 'plft'),
        'Torque::POUND_INCH'          => array(array('' => 0.084738622, '/' => 12, '*' => 16), 'lbin')
    );

    private $_Locale;

    /**
     * Zend_Measure_Torque provides an locale aware class for
     * conversion and formatting of Torque values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Torque Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Torque Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of torque:' . $type);
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
        if (empty( self::$_UNITS[$type] )) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of torque:' . $type);
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