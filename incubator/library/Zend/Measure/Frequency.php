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
 * @subpackage Zend_Measure_Frequency
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Frequency extends Zend_Measure_Abstract
{
    // Frequency definitions
    const STANDARD = 'Frequency::HERTZ';

    const ONE_PER_SECOND        = 'Frequency::ONE_PER_SECOND';
    const CYCLE_PER_SECOND      = 'Frequency::CYCLE_PER_SECOND';
    const DEGREE_PER_HOUR       = 'Frequency::DEGREE_PER_HOUR';
    const DEGREE_PER_MINUTE     = 'Frequency::DEGREE_PER_MINUTE';
    const DEGREE_PER_SECOND     = 'Frequency::DEGREE_PER_SECOND';
    const GIGAHERTZ             = 'Frequency::GIGAHERTZ';
    const HERTZ                 = 'Frequency::HERTZ';
    const KILOHERTZ             = 'Frequency::KILOHERTZ';
    const MEGAHERTZ             = 'Frequency::MEGAHERTZ';
    const MILLIHERTZ            = 'Frequency::MILLIHERTZ';
    const RADIAN_PER_HOUR       = 'Frequency::RADIAN_PER_HOUR';
    const RADIAN_PER_MINUTE     = 'Frequency::RADIAN_PER_MINUTE';
    const RADIAN_PER_SECOND     = 'Frequency::RADIAN_PER_SECOND';
    const REVOLUTION_PER_HOUR   = 'Frequency::REVOLUTION_PER_HOUR';
    const REVOLUTION_PER_MINUTE = 'Frequency::REVOLUTION_PER_MINUTE';
    const REVOLUTION_PER_SECOND = 'Frequency::REVOLUTION_PER_SECOND';
    const RPM                   = 'Frequency::RPM';
    const TERRAHERTZ            = 'Frequency::TERRAHERTZ';

    private static $_UNITS = array(
        'Frequency::ONE_PER_SECOND'        => array(1,             '1/s'),
        'Frequency::CYCLE_PER_SECOND'      => array(1,             'cps'),
        'Frequency::DEGREE_PER_HOUR'       => array(array('' => 1, '/' => 1296000), '°/h'),
        'Frequency::DEGREE_PER_MINUTE'     => array(array('' => 1, '/' => 21600),   '°/m'),
        'Frequency::DEGREE_PER_SECOND'     => array(array('' => 1, '/' => 360),     '°/s'),
        'Frequency::GIGAHERTZ'             => array(1000000000,    'GHz'),
        'Frequency::HERTZ'                 => array(1,             'Hz'),
        'Frequency::KILOHERTZ'             => array(1000,          'kHz'),
        'Frequency::MEGAHERTZ'             => array(1000000,       'MHz'),
        'Frequency::MILLIHERTZ'            => array(0.001,         'mHz'),
        'Frequency::RADIAN_PER_HOUR'       => array(array('' => 1, '/' => 22619.467), 'rad/h'),
        'Frequency::RADIAN_PER_MINUTE'     => array(array('' => 1, '/' => 376.99112), 'rad/m'),
        'Frequency::RADIAN_PER_SECOND'     => array(array('' => 1, '/' => 6.2831853), 'rad/s'),
        'Frequency::REVOLUTION_PER_HOUR'   => array(array('' => 1, '/' => 3600), 'rph'),
        'Frequency::REVOLUTION_PER_MINUTE' => array(array('' => 1, '/' => 60),   'rpm'),
        'Frequency::REVOLUTION_PER_SECOND' => array(1,             'rps'),
        'Frequency::RPM'                   => array(array('' => 1, '/' => 60), 'rpm'),
        'Frequency::TERRAHERTZ'            => array(1000000000000, 'THz')
    );

    private $_Locale;

    /**
     * Zend_Measure_Frequency provides an locale aware class for
     * conversion and formatting of Frequency values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Frequency Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Frequency Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of frequency:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType( $type );
    }


    /**
     * Set a new type, and convert the value
     *
     * @param  $type new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType( $type )
    {
        if (empty(self::$_UNITS[$type])) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of frequency:' . $type);
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