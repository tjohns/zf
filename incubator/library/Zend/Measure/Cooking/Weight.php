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
 * @subpackage Zend_Measure_Cooking_Weight
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Cooking_Weight extends Zend_Measure_Abstract
{
    // Cooking_Weight definitions
    const STANDARD = 'Cooking_Weight::GRAM';

    const HALF_STICK    = 'Cooking_Weight::HALF_STICK';
    const STICK         = 'Cooking_Weight::STICK';
    const CUP           = 'Cooking_Weight::CUP';
    const GRAM          = 'Cooking_Weight::GRAM';
    const OUNCE         = 'Cooking_Weight::OUNCE';
    const POUND         = 'Cooking_Weight::POUND';
    const TEASPOON      = 'Cooking_Weight::TEASPOON';
    const TEASPOON_US   = 'Cooking_Weight::TEASPOON_US';
    const TABLESPOON    = 'Cooking_Weight::TABLESPOON';
    const TABLESPOON_US = 'Cooking_Weight::TABLESPOON_US';

    private static $_UNITS = array(
        'Cooking_Weight::HALF_STICK'    => array(array('' => 453.59237, '/' => 8),                    'half stk'),
        'Cooking_Weight::STICK'         => array(array('' => 453.59237, '/' => 4),                    'stk'),
        'Cooking_Weight::CUP'           => array(array('' => 453.59237, '/' => 2),                    'c'),
        'Cooking_Weight::GRAM'          => array(1,                                                   'g'),
        'Cooking_Weight::OUNCE'         => array(array('' => 453.59237, '/' => 16),                   'oz'),
        'Cooking_Weight::POUND'         => array(453.59237,                                           'lb'),
        'Cooking_Weight::TEASPOON'      => array(array('' => 1.2503332, '' => 453.59237, '/' => 128), 'tsp'),
        'Cooking_Weight::TEASPOON_US'   => array(array('' => 453.59237, '/' => 96),                   'tsp'),
        'Cooking_Weight::TABLESPOON'    => array(array('' => 1.2503332, '' => 453.59237, '/' => 32),  'tbsp'),
        'Cooking_Weight::TABLESPOON_US' => array(array('' => 453.59237, '/' => 32),                   'tbsp')
    );

    private $_Locale;

    /**
     * Zend_Measure_Cooking_Weight provides an locale aware class for
     * conversion and formatting of Cooking_Weight values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Cooking_Weight Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Cooking_Weight Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of weight-cooking:' . $type);
        }

        parent::setValue($value, $type, $locale);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type])) {
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of weight-cooking:' . $type);
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