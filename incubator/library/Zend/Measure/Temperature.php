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
 * @subpackage Zend_Measure_Temperature
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Temperature extends Zend_Measure_Abstract
{
    // Temperature definitions
    const STANDARD = 'Temperature::KELVIN';

    const CELSIUS    = 'Temperature::CELSIUS';
    const FAHRENHEIT = 'Temperature::FAHRENHEIT';
    const RANKINE    = 'Temperature::RANKINE';
    const REAUMUR    = 'Temperature::REAUMUR';
    const KELVIN     = 'Temperature::KELVIN';

    public static $_UNITS = array(
        'Temperature::CELSIUS'    => array(array('' => 1, '+' => 274.15),'°C'),
        'Temperature::FAHRENHEIT' => array(array('' => 1, '-' => 32, '/' => 1.8, '+' => 273.15),'°F'),
        'Temperature::RANKINE'    => array(array('' => 1, '/' => 1.8),'°R'),
        'Temperature::REAUMUR'    => array(array('' => 1, '*' => 1.25, '+' => 273.15),'°r'),
        'Temperature::KELVIN'     => array(1,'°K')
    );

    private $_Locale;

    /**
     * Zend_Measure_Temperature provides an locale aware class for
     * conversion and formatting of temperature values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Temperature Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Temperature Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of temperature:' . $type);
        }
        
        parent::setValue($value, $type, $locale);
        parent::setType( $type );
    }


    /**
     * Set a new type, and convert the value
     *
     * @param $type new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType( $type )
    {
        if (empty( self::$_UNITS[$type] )) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of temperature:' . $type);
        }

        // Convert to standard value
        $value = parent::getValue();
        if (is_array( self::$_UNITS[parent::getType()][0] )) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ( $key ) {
                    case "/":
                        $value /= $found;
                        break;
                    case "+":
                        $value += $found;
                        break;
                    case "-":
                        $value -= $found;
                        break;
                    default:
                        $value *= $found;
                        break;
                }
            }
        } else {
          $value = $value * ( self::$_UNITS[parent::getType()][0] );
        }

        // Convert to expected value
        if (is_array( self::$_UNITS[$type][0] )) {
            foreach (self::$_UNITS[$type][0] as $key => $found) {
                switch ( $key ) {
                    case "/":
                        $value *= $found;
                        break;
                    case "+":
                        $value -= $found;
                        break;
                    case "-":
                        $value += $found;
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