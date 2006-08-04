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
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Format.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Capacity
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Capacity extends Zend_Measure_Abstract
{
    // Capacity definitions
    const STANDARD = 'Capacity::FARAD';

    const ABFARAD              = 'Capacity::ABFARAD';
    const AMPERE_SECOND_VOLT   = 'Capacity::AMPERE_SECOND_VOLT';
    const CENTIFARAD           = 'Capacity::CENTIFARAD';
    const COULOMB_VOLT         = 'Capacity::COULOMB_VOLT';
    const DECIFARAD            = 'Capacity::DECIFARAD';
    const DEKAFARAD            = 'Capacity::DEKAFARAD';
    const ELECTROMAGNETIC_UNIT = 'Capacity::ELECTROMAGNETIC_UNIT';
    const ELECTROSTATIC_UNIT   = 'Capacity::ELECTROSTATIC_UNIT';
    const FARAD                = 'Capacity::FARAD';
    const FARAD_INTERNATIONAL  = 'Capacity::FARAD_INTERNATIONAL';
    const GAUSSIAN             = 'Capacity::GAUSSIAN';
    const GIGAFARAD            = 'Capacity::GIGAFARAD';
    const HECTOFARAD           = 'Capacity::HECTOFARAD';
    const JAR                  = 'Capacity::JAR';
    const KILOFARAD            = 'Capacity::KILOFARAD';
    const MEGAFARAD            = 'Capacity::MEGAFARAD';
    const MICROFARAD           = 'Capacity::MICROFARAD';
    const MILLIFARAD           = 'Capacity::MILLIFARAD';
    const NANOFARAD            = 'Capacity::NANOFARAD';
    const PICOFARAD            = 'Capacity::PICOFARAD';
    const PUFF                 = 'Capacity::PUFF';
    const SECOND_OHM           = 'Capacity::SECOND_OHM';
    const STATFARAD            = 'Capacity::STATFARAD';
    const TERAFARAD            = 'Capacity::TERAFARAD';

    private static $_UNITS = array(
        'Capacity::ABFARAD'              => array(1.0e+9,'abfarad'),
        'Capacity::AMPERE_SECOND_VOLT'   => array(1,'A/sV'),
        'Capacity::CENTIFARAD'           => array(0.01,'cF'),
        'Capacity::COULOMB_VOLT'         => array(1,'C/V'),
        'Capacity::DECIFARAD'            => array(0.1,'dF'),
        'Capacity::DEKAFARAD'            => array(10,'daF'),
        'Capacity::ELECTROMAGNETIC_UNIT' => array(1.0e+9,'capacity emu'),
        'Capacity::ELECTROSTATIC_UNIT'   => array(1.11265e-12,'capacity esu'),
        'Capacity::FARAD'                => array(1,'F'),
        'Capacity::FARAD_INTERNATIONAL'  => array(0.99951,'F'),
        'Capacity::GAUSSIAN'             => array(1.11265e-12,'G'),
        'Capacity::GIGAFARAD'            => array(1.0e+9,'GF'),
        'Capacity::HECTOFARAD'           => array(100,'hF'),
        'Capacity::JAR'                  => array(1.11265e-9,'jar'),
        'Capacity::KILOFARAD'            => array(1000,'kF'),
        'Capacity::MEGAFARAD'            => array(1000000,'MF'),
        'Capacity::MICROFARAD'           => array(0.000001,'µF'),
        'Capacity::MILLIFARAD'           => array(0.001,'mF'),
        'Capacity::NANOFARAD'            => array(1.0e-9,'nF'),
        'Capacity::PICOFARAD'            => array(1.0e-12,'pF'),
        'Capacity::PUFF'                 => array(1.0e-12,'pF'),
        'Capacity::SECOND_OHM'           => array(1,'s/Ohm'),
        'Capacity::STATFARAD'            => array(1.11265e-12,'statfarad'),
        'Capacity::TERAFARAD'            => array(1.0e+12,'TF')
    );

    /**
     * Zend_Measure_Capacity provides an locale aware class for
     * conversion and formatting of capacity values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Capacity Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type, $locale)
    {
        $this->setValue($value, $type, $locale);
    }


    /**
     * Compare if the value and type is equal
     *
     * @return boolean
     */
    public function equals( Zend_Measure_Capacity $object )
    {
        if ($object->toString() == $this->toString())
            return true;
        return false;
    }


    /**
     * Set a new value
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Capacity Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type, $locale)
    {
        $value = Zend_Locale_Format::getNumber($value, $locale);
        if (empty(self::$_UNITS[$type]))
            parent::throwException('unknown type of capacity:'.$type);
        parent::setValue($value);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type]))
            self::throwException('unknown type of capacity:'.$type);

        // Convert to standard value
        $value = parent::getValue();
        if (is_array(self::$_UNITS[parent::getType()][0])) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value /= $found;
                        break;
                    case "*":
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
                        $value /= $found;
                        break;
                    case "*":
                        $value *= $found;
                        break;
                    default:
                        $value *= $found;
                        break;
                }
            }
        } else {
            $value = $value * (self::$_UNITS[$type][0]);
        }
        parent::setValue($value);
        parent::setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return parent::getValue().' '.self::$_UNITS[parent::getType()][1];
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
}