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
 * @subpackage Zend_Measure_Flow_Mole
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Flow_Mole extends Zend_Measure_Abstract
{
    // Mole definitions
    const STANDARD = 'Flow_Mole::MOLE_PER_SECOND';

    const CENTIMOLE_PER_DAY    = 'Flow_Mole::CENTIMOLE_PER_DAY';
    const CENTIMOLE_PER_HOUR   = 'Flow_Mole::CENTIMOLE_PER_HOUR';
    const CENTIMOLE_PER_MINUTE = 'Flow_Mole::CENTIMOLE_PER_MINUTE';
    const CENTIMOLE_PER_SECOND = 'Flow_Mole::CENTIMOLE_PER_SECOND';
    const MEGAMOLE_PER_DAY     = 'Flow_Mole::MEGAMOLE_PER_DAY';
    const MEGAMOLE_PER_HOUR    = 'Flow_Mole::MEGAMOLE_PER_HOUR';
    const MEGAMOLE_PER_MINUTE  = 'Flow_Mole::MEGAMOLE_PER_MINUTE';
    const MEGAMOLE_PER_SECOND  = 'Flow_Mole::MEGAMOLE_PER_SECOND';
    const MICROMOLE_PER_DAY    = 'Flow_Mole::MICROMOLE_PER_DAY';
    const MICROMOLE_PER_HOUR   = 'Flow_Mole::MICROMOLE_PER_HOUR';
    const MICROMOLE_PER_MINUTE = 'Flow_Mole::MICROMOLE_PER_MINUTE';
    const MICROMOLE_PER_SECOND = 'Flow_Mole::MICROMOLE_PER_SECOND';
    const MILLIMOLE_PER_DAY    = 'Flow_Mole::MILLIMOLE_PER_DAY';
    const MILLIMOLE_PER_HOUR   = 'Flow_Mole::MILLIMOLE_PER_HOUR';
    const MILLIMOLE_PER_MINUTE = 'Flow_Mole::MILLIMOLE_PER_MINUTE';
    const MILLIMOLE_PER_SECOND = 'Flow_Mole::MILLIMOLE_PER_SECOND';
    const MOLE_PER_DAY         = 'Flow_Mole::MOLE_PER_DAY';
    const MOLE_PER_HOUR        = 'Flow_Mole::MOLE_PER_HOUR';
    const MOLE_PER_MINUTE      = 'Flow_Mole::MOLE_PER_MINUTE';
    const MOLE_PER_SECOND      = 'Flow_Mole::MOLE_PER_SECOND';

    private static $_UNITS = array(
        'Flow_Mole::CENTIMOLE_PER_DAY'    => array(array('' => 0.01, '/' => 86400),     'cmol/day'),
        'Flow_Mole::CENTIMOLE_PER_HOUR'   => array(array('' => 0.01, '/' => 3600),      'cmol/h'),
        'Flow_Mole::CENTIMOLE_PER_MINUTE' => array(array('' => 0.01, '/' => 60),        'cmol/m'),
        'Flow_Mole::CENTIMOLE_PER_SECOND' => array(0.01,     'cmol/s'),
        'Flow_Mole::MEGAMOLE_PER_DAY'     => array(array('' => 1000000, '/' => 86400),  'Mmol/day'),
        'Flow_Mole::MEGAMOLE_PER_HOUR'    => array(array('' => 1000000, '/' => 3600),   'Mmol/h'),
        'Flow_Mole::MEGAMOLE_PER_MINUTE'  => array(array('' => 1000000, '/' => 60),     'Mmol/m'),
        'Flow_Mole::MEGAMOLE_PER_SECOND'  => array(1000000,  'Mmol/s'),
        'Flow_Mole::MICROMOLE_PER_DAY'    => array(array('' => 0.000001, '/' => 86400), 'µmol/day'),
        'Flow_Mole::MICROMOLE_PER_HOUR'   => array(array('' => 0.000001, '/' => 3600),  'µmol/h'),
        'Flow_Mole::MICROMOLE_PER_MINUTE' => array(array('' => 0.000001, '/' => 60),    'µmol/m'),
        'Flow_Mole::MICROMOLE_PER_SECOND' => array(0.000001, 'µmol/s'),
        'Flow_Mole::MILLIMOLE_PER_DAY'    => array(array('' => 0.001, '/' => 86400),    'mmol/day'),
        'Flow_Mole::MILLIMOLE_PER_HOUR'   => array(array('' => 0.001, '/' => 3600),     'mmol/h'),
        'Flow_Mole::MILLIMOLE_PER_MINUTE' => array(array('' => 0.001, '/' => 60),       'mmol/m'),
        'Flow_Mole::MILLIMOLE_PER_SECOND' => array(0.001,    'mmol/s'),
        'Flow_Mole::MOLE_PER_DAY'         => array(array('' => 1, '/' => 86400),        'mol/day'),
        'Flow_Mole::MOLE_PER_HOUR'        => array(array('' => 1, '/' => 3600),         'mol/h'),
        'Flow_Mole::MOLE_PER_MINUTE'      => array(array('' => 1, '/' => 60),           'mol/m'),
        'Flow_Mole::MOLE_PER_SECOND'      => array(1,        'mol/s')
    );

    private $_Locale;

    /**
     * Zend_Measure_Flow_Mole provides an locale aware class for
     * conversion and formatting of Mole values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Flow_Mole Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Flow_Mole Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of flow-mole:' . $type);
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
        if (empty(self::$_UNITS[$type])) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of flow-mole:' . $type);
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