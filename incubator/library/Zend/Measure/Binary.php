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
 * @subpackage Zend_Measure_Binary
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 */
class Zend_Measure_Binary extends Zend_Measure_Abstract
{
    // Binary definitions
    const STANDARD = 'Binary::BYTE';

    const BIT              = 'Binary::BIT';
    const CRUMB            = 'Binary::CRUMB';
    const NIBBLE           = 'Binary::NIBBLE';
    const BYTE             = 'Binary::BYTE';
    const KILOBYTE         = 'Binary::KILOBYTE';
    const KIBIBYTE         = 'Binary::KIBIBYTE';
    const KILO_BINARY_BYTE = 'Binary::KILO_BINARY_BYTE';
    const KILOBYTE_SI      = 'Binary::KILOBYTE_SI';
    const MEGABYTE         = 'Binary::MEGABYTE';
    const MEBIBYTE         = 'Binary::MEBIBYTE';
    const MEGA_BINARY_BYTE = 'Binary::MEGA_BINARY_BYTE';
    const MEGABYTE_SI      = 'Binary::MEGABYTE_SI';
    const GIGABYTE         = 'Binary::GIGABYTE';
    const GIBIBYTE         = 'Binary::GIBIBYTE';
    const GIGA_BINARY_BYTE = 'Binary::GIGA_BINARY_BYTE';
    const GIGABYTE_SI      = 'Binary::GIGABYTE_SI';
    const TERABYTE         = 'Binary::TERABYTE';
    const TEBIBYTE         = 'Binary::TEBIBYTE';
    const TERA_BINARY_BYTE = 'Binary::TERA_BINARY_BYTE';
    const TERABYTE_SI      = 'Binary::TERABYTE_SI';
    const PETABYTE         = 'Binary::PETABYTE';
    const PEBIBYTE         = 'Binary::PEBIBYTE';
    const PETA_BINARY_BYTE = 'Binary::PETA_BINARY_BYTE';
    const PETABYTE_SI      = 'Binary::PETABYTE_SI';
    const EXABYTE          = 'Binary::EXABYTE';
    const EXBIBYTE         = 'Binary::EXBIBYTE';
    const EXA_BINARY_BYTE  = 'Binary::EXA_BINARY_BYTE';
    const EXABYTE_SI       = 'Binary::EXABYTE_SI';
    const ZETTABYTE        = 'Binary::ZETTABYTE';
    const ZEBIBYTE         = 'Binary::ZEBIBYTE';
    const ZETTA_BINARY_BYTE= 'Binary::ZETTA_BINARY_BYTE';
    const ZETTABYTE_SI     = 'Binary::ZETTABYTE_SI';
    const YOTTABYTE        = 'Binary::YOTTABYTE';
    const YOBIBYTE         = 'Binary::YOBIBYTE';
    const YOTTA_BINARY_BYTE= 'Binary::YOTTA_BINARY_BYTE';
    const YOTTABYTE_SI     = 'Binary::YOTTABYTE_SI';

    private static $_UNITS = array(
        'Binary::BIT'              => array('0.125',                     'b'),
        'Binary::CRUMB'            => array('0.25',                      'crumb'),
        'Binary::NIBBLE'           => array('0.5',                       'nibble'),
        'Binary::BYTE'             => array('1',                         'B'),
        'Binary::KILOBYTE'         => array('1024',                      'kB'),
        'Binary::KIBIBYTE'         => array('1024',                      'KiB'),
        'Binary::KILO_BINARY_BYTE' => array('1024',                      'KiB'),
        'Binary::KILOBYTE_SI'      => array('1000',                      'kB.'),
        'Binary::MEGABYTE'         => array('1048576',                   'MB'),
        'Binary::MEBIBYTE'         => array('1048576',                   'MiB'),
        'Binary::MEGA_BINARY_BYTE' => array('1048576',                   'MiB'),
        'Binary::MEGABYTE_SI'      => array('1000000',                   'MB.'),
        'Binary::GIGABYTE'         => array('1073741824',                'GB'),
        'Binary::GIBIBYTE'         => array('1073741824',                'GiB'),
        'Binary::GIGA_BINARY_BYTE' => array('1073741824',                'GiB'),
        'Binary::GIGABYTE_SI'      => array('1000000000',                'GB.'),
        'Binary::TERABYTE'         => array('1099511627776',             'TB'),
        'Binary::TEBIBYTE'         => array('1099511627776',             'TiB'),
        'Binary::TERA_BINARY_BYTE' => array('1099511627776',             'TiB'),
        'Binary::TERABYTE_SI'      => array('1000000000000',             'TB.'),
        'Binary::PETABYTE'         => array('1125899906842624',          'PB'),
        'Binary::PEBIBYTE'         => array('1125899906842624',          'PiB'),
        'Binary::PETA_BINARY_BYTE' => array('1125899906842624',          'PiB'),
        'Binary::PETABYTE_SI'      => array('1000000000000000',          'PB.'),
        'Binary::EXABYTE'          => array('1152921504606846976',       'EB'),
        'Binary::EXBIBYTE'         => array('1152921504606846976',       'EiB'),
        'Binary::EXA_BINARY_BYTE'  => array('1152921504606846976',       'EiB'),
        'Binary::EXABYTE_SI'       => array('1000000000000000000',       'EB.'),
        'Binary::ZETTABYTE'        => array('1180591620717411303424',    'ZB'),
        'Binary::ZEBIBYTE'         => array('1180591620717411303424',    'ZiB'),
        'Binary::ZETTA_BINARY_BYTE'=> array('1180591620717411303424',    'ZiB'),
        'Binary::ZETTABYTE_SI'     => array('1000000000000000000000',    'ZB.'),
        'Binary::YOTTABYTE'        => array('1208925819614629174706176', 'YB'),
        'Binary::YOBIBYTE'         => array('1208925819614629174706176', 'YiB'),
        'Binary::YOTTA_BINARY_BYTE'=> array('1208925819614629174706176', 'YiB'),
        'Binary::YOTTABYTE_SI'     => array('1000000000000000000000000', 'YB.')
    );

    private $_Locale;

    /**
     * Zend_Measure_Binary provides an locale aware class for
     * conversion and formatting of Binary values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Binary Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Binary Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale = false)
    {
        if (empty( $locale )) {
            $locale = $this->_Locale;
        }

        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty( self::$_UNITS[$type] )) {
            self::throwException('unknown type of binary:' . $type);
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
            self::throwException('unknown type of binary:' . $type);
        }

        // Convert to standard value
        $value = parent::getValue();
        $value = bcmul($value, self::$_UNITS[parent::getType()][0], 25);

        // Convert to expected value
        $value = bcdiv($value, self::$_UNITS[$type][0]);
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