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
 * @subpackage Zend_Measure_Capacitance
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Capacitance extends Zend_Measure_Abstract
{
    // Capacitance definitions
    const STANDARD = 'Capacitance::FARAD';

    const ABFARAD              = 'Capacitance::ABFARAD';
    const AMPERE_PER_SECOND_VOLT   = 'Capacitance::AMPERE_PER_SECOND_VOLT';
    const CENTIFARAD           = 'Capacitance::CENTIFARAD';
    const COULOMB_PER_VOLT         = 'Capacitance::COULOMB_PER_VOLT';
    const DECIFARAD            = 'Capacitance::DECIFARAD';
    const DEKAFARAD            = 'Capacitance::DEKAFARAD';
    const ELECTROMAGNETIC_UNIT = 'Capacitance::ELECTROMAGNETIC_UNIT';
    const ELECTROSTATIC_UNIT   = 'Capacitance::ELECTROSTATIC_UNIT';
    const FARAD                = 'Capacitance::FARAD';
    const FARAD_INTERNATIONAL  = 'Capacitance::FARAD_INTERNATIONAL';
    const GAUSSIAN             = 'Capacitance::GAUSSIAN';
    const GIGAFARAD            = 'Capacitance::GIGAFARAD';
    const HECTOFARAD           = 'Capacitance::HECTOFARAD';
    const JAR                  = 'Capacitance::JAR';
    const KILOFARAD            = 'Capacitance::KILOFARAD';
    const MEGAFARAD            = 'Capacitance::MEGAFARAD';
    const MICROFARAD           = 'Capacitance::MICROFARAD';
    const MILLIFARAD           = 'Capacitance::MILLIFARAD';
    const NANOFARAD            = 'Capacitance::NANOFARAD';
    const PICOFARAD            = 'Capacitance::PICOFARAD';
    const PUFF                 = 'Capacitance::PUFF';
    const SECOND_PER_OHM       = 'Capacitance::SECOND_PER_OHM';
    const STATFARAD            = 'Capacitance::STATFARAD';
    const TERAFARAD            = 'Capacitance::TERAFARAD';

    private static $_UNITS = array(
        'Capacitance::ABFARAD'              => array(1.0e+9,      'abfarad'),
        'Capacitance::AMPERE_PER_SECOND_VOLT' => array(1,         'A/sV'),
        'Capacitance::CENTIFARAD'           => array(0.01,        'cF'),
        'Capacitance::COULOMB_PER_VOLT'     => array(1,           'C/V'),
        'Capacitance::DECIFARAD'            => array(0.1,         'dF'),
        'Capacitance::DEKAFARAD'            => array(10,          'daF'),
        'Capacitance::ELECTROMAGNETIC_UNIT' => array(1.0e+9,      'capacity emu'),
        'Capacitance::ELECTROSTATIC_UNIT'   => array(1.11265e-12, 'capacity esu'),
        'Capacitance::FARAD'                => array(1,           'F'),
        'Capacitance::FARAD_INTERNATIONAL'  => array(0.99951,     'F'),
        'Capacitance::GAUSSIAN'             => array(1.11265e-12, 'G'),
        'Capacitance::GIGAFARAD'            => array(1.0e+9,      'GF'),
        'Capacitance::HECTOFARAD'           => array(100,         'hF'),
        'Capacitance::JAR'                  => array(1.11265e-9,  'jar'),
        'Capacitance::KILOFARAD'            => array(1000,        'kF'),
        'Capacitance::MEGAFARAD'            => array(1000000,     'MF'),
        'Capacitance::MICROFARAD'           => array(0.000001,    'µF'),
        'Capacitance::MILLIFARAD'           => array(0.001,       'mF'),
        'Capacitance::NANOFARAD'            => array(1.0e-9,      'nF'),
        'Capacitance::PICOFARAD'            => array(1.0e-12,     'pF'),
        'Capacitance::PUFF'                 => array(1.0e-12,     'pF'),
        'Capacitance::SECOND_PER_OHM'       => array(1,           's/Ohm'),
        'Capacitance::STATFARAD'            => array(1.11265e-12, 'statfarad'),
        'Capacitance::TERAFARAD'            => array(1.0e+12,     'TF')
    );

    private $_Locale;

    /**
     * Zend_Measure_Capacitance provides an locale aware class for
     * conversion and formatting of Capacitance values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Capacitance Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Capacitance Type
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
            parent::throwException('unknown type of capacity:' . $type);
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
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of capacity:' . $type);
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