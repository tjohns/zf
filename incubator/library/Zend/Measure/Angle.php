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
 * @subpackage Zend_Measure_Angle
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Angle extends Zend_Measure_Abstract
{
    // Angle definitions
    const STANDARD = 'Angle::RADIAN';

    const RADIAN      = 'Angle::RADIAN';
    const MIL         = 'Angle::MIL';
    const GRAD        = 'Angle::GRAD';
    const DEGREE      = 'Angle::DEGREE';
    const MINUTE      = 'Angle::MINUTE';
    const SECOND      = 'Angle::SECOND';
    const POINT       = 'Angle::POINT';
    const CIRCLE_16   = 'Angle::CIRCLE_16';
    const CIRCLE_10   = 'Angle::CIRCLE_10';
    const CIRCLE_8    = 'Angle::CIRCLE_8';
    const CIRCLE_6    = 'Angle::CIRCLE_6';
    const CIRCLE_4    = 'Angle::CIRCLE_4';
    const CIRCLE_2    = 'Angle::CIRCLE_2';
    const FULL_CIRCLE = 'Angle::FULL_CIRCLE';

    private static $_UNITS = array(
        'Angle::RADIAN'      => array(1,'rad'),
        'Angle::MIL'         => array(array('' => M_PI,'/' => 3200),   'mil'),
        'Angle::GRAD'        => array(array('' => M_PI,'/' => 200),    'gr'),
        'Angle::DEGREE'      => array(array('' => M_PI,'/' => 180),    '°'),
        'Angle::MINUTE'      => array(array('' => M_PI,'/' => 10800),  "'"),
        'Angle::SECOND'      => array(array('' => M_PI,'/' => 648000), '"'),
        'Angle::POINT'       => array(array('' => M_PI,'/' => 16),     'pt'),
        'Angle::CIRCLE_16'   => array(array('' => M_PI,'/' => 8),      'per 16 circle'),
        'Angle::CIRCLE_10'   => array(array('' => M_PI,'/' => 5),      'per 10 circle'),
        'Angle::CIRCLE_8'    => array(array('' => M_PI,'/' => 4),      'per 8 circle'),
        'Angle::CIRCLE_6'    => array(array('' => M_PI,'/' => 3),      'per 6 circle'),
        'Angle::CIRCLE_4'    => array(array('' => M_PI,'/' => 2),      'per 4 circle'),
        'Angle::CIRCLE_2'    => array(M_PI,                            'per 2 circle'),
        'Angle::FULL_CIRCLE' => array(array('' => M_PI,'*' => 2),      'cir')
    );

    private $_Locale;

    /**
     * Zend_Measure_Angle provides an locale aware class for
     * conversion and formatting of angle values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Angle Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Angle Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of angle:' . $type);
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of angle:' . $type);
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