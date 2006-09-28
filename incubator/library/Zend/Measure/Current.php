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
 * @subpackage Zend_Measure_Current
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Current extends Zend_Measure_Abstract
{
    // Current definitions
    const STANDARD = 'Current::AMPERE';

    const ABAMPERE             = 'Current::ABAMPERE';
    const AMPERE               = 'Current::AMPERE';
    const BIOT                 = 'Current::BIOT';
    const CENTIAMPERE          = 'Current::CENTIAMPERE';
    const COULOMB_PER_SECOND   = 'Current::COULOMB_PER_SECOND';
    const DECIAMPERE           = 'Current::DECIAMPERE';
    const DEKAAMPERE           = 'Current::DEKAAMPERE';
    const ELECTROMAGNETIC_UNIT = 'Current::ELECTROMAGNATIC_UNIT';
    const ELECTROSTATIC_UNIT   = 'Current::ELECTROSTATIC_UNIT';
    const FRANCLIN_PER_SECOND  = 'Current::FRANCLIN_PER_SECOND';
    const GAUSSIAN             = 'Current::GAUSSIAN';
    const GIGAAMPERE           = 'Current::GIGAAMPERE';
    const GILBERT              = 'Current::GILBERT';
    const HECTOAMPERE          = 'Current::HECTOAMPERE';
    const KILOAMPERE           = 'Current::KILOAMPERE';
    const MEGAAMPERE           = 'Current::MEGAAMPERE';
    const MICROAMPERE          = 'Current::MICROAMPERE';
    const MILLIAMPERE          = 'Current::MILLIAMPERE';
    const NANOAMPERE           = 'Current::NANOAMPERE';
    const PICOAMPERE           = 'Current::PICOAMPERE';
    const SIEMENS_VOLT         = 'Current::SIEMENS_VOLT';
    const STATAMPERE           = 'Current::STATAMPERE';
    const TERAAMPERE           = 'Current::TERAAMPERE';
    const VOLT_PER_OHM         = 'Current::VOLT_PER_OHM';
    const WATT_PER_VOLT        = 'Current::WATT_PER_VOLT';
    const WEBER_PER_HENRY      = 'Current::WEBER_PER_HENRY';

    private static $_UNITS = array(
        'Current::ABAMPERE'             => array(10,           'abampere'),
        'Current::AMPERE'               => array(1,            'A'),
        'Current::BIOT'                 => array(10,           'Bi'),
        'Current::CENTIAMPERE'          => array(0.01,         'cA'),
        'Current::COULOMB_PER_SECOND'   => array(1,            'C/s'),
        'Current::DECIAMPERE'           => array(0.1,          'dA'),
        'Current::DEKAAMPERE'           => array(10,           'daA'),
        'Current::ELECTROMAGNATIC_UNIT' => array(10,           'current emu'),
        'Current::ELECTROSTATIC_UNIT'   => array(3.335641e-10, 'current esu'),
        'Current::FRANCLIN_PER_SECOND'  => array(3.335641e-10, 'Fr/s'),
        'Current::GAUSSIAN'             => array(3.335641e-10, 'G current'),
        'Current::GIGAAMPERE'           => array(1.0e+9,       'GA'),
        'Current::GILBERT'              => array(0.79577472,   'Gi'),
        'Current::HECTOAMPERE'          => array(100,          'hA'),
        'Current::KILOAMPERE'           => array(1000,         'kA'),
        'Current::MEGAAMPERE'           => array(1000000,      'MA') ,
        'Current::MICROAMPERE'          => array(0.000001,     'µA'),
        'Current::MILLIAMPERE'          => array(0.001,        'mA'),
        'Current::NANOAMPERE'           => array(1.0e-9,       'nA'),
        'Current::PICOAMPERE'           => array(1.0e-12,      'pA'),
        'Current::SIEMENS_VOLT'         => array(1,            'SV'),
        'Current::STATAMPERE'           => array(3.335641e-10, 'statampere'),
        'Current::TERAAMPERE'           => array(1.0e+12,      'TA'),
        'Current::VOLT_PER_OHM'         => array(1,            'V/Ohm'),
        'Current::WATT_PER_VOLT'        => array(1,            'W/V'),
        'Current::WEBER_PER_HENRY'      => array(1,            'Wb/H')
    );

    private $_Locale;

    /**
     * Zend_Measure_Current provides an locale aware class for
     * conversion and formatting of current values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Current Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Current Type
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
            self::throwException('unknown type of current:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @param $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type])) {
            self::throwException('unknown type of current:' . $type);
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