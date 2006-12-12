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
 * Include needed Measure classes
 */
require_once 'Zend.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure
{

    const ACCELERATION   = 'Zend_Measure::ACCELERATION';
    const ANGLE          = 'Zend_Measure::ANGLE';
    const AREA           = 'Zend_Measure::AREA';
    const BINARY         = 'Zend_Measure::BINARY';
    const CAPACITANCE    = 'Zend_Measure::CAPACITANCE';
    const COOKING_VOLUME = 'Zend_Measure::COOKING_VOLUME';
    const COOKING_WEIGHT = 'Zend_Measure::COOKING_WEIGHT';
    const CURRENT        = 'Zend_Measure::CURRENT';
    const DENSITY        = 'Zend_Measure::DENSITY';
    const ENERGY         = 'Zend_Measure::ENERGY';
    const FORCE          = 'Zend_Measure::FORCE';
    const FLOW_MASS      = 'Zend_Measure::FLOW_MASS';
    const FLOW_MOLE      = 'Zend_Measure::FLOW_MOLE';
    const FLOW_VOLUME    = 'Zend_Measure::FLOW_VOLUME';
    const FREQUENCY      = 'Zend_Measure::FREQUENCY';
    const ILLUMINATION   = 'Zend_Measure::ILLUMINATION';
    const LENGTH         = 'Zend_Measure::LENGTH';
    const LIGHTNESS      = 'Zend_Measure::LIGHTNESS';
    const NUMBER         = 'Zend_Measure::NUMBER';
    const POWER          = 'Zend_Measure::POWER';
    const PRESSURE       = 'Zend_Measure::PRESSURE';
    const SPEED          = 'Zend_Measure::SPEED';
    const TEMPERATURE    = 'Zend_Measure::TEMPERATURE';
    const TORQUE         = 'Zend_Measure::TORQUE';
    const VISCOSITY_DYNAMIC   = 'Zend_Measure::VISCOSITY_DYNAMIC';
    const VISCOSITY_KINEMATIC = 'Zend_Measure::VISCOSITY_KINEMATIC';
    const VOLUME         = 'Zend_Measure::VOLUME';
    const WEIGHT         = 'Zend_Measure::WEIGHT';

    private static $_UNIT = array(
        'Zend_Measure::ACCELERATION'   => array('Acceleration' =>   'METER_PER_SQUARE_SECOND'),
        'Zend_Measure::ANGLE'          => array('Angle' =>          'RADIAN'),
        'Zend_Measure::AREA'           => array('Area' =>           'SQUARE_METER'),
        'Zend_Measure::BINARY'         => array('Binary' =>         'BYTE'),
        'Zend_Measure::CAPACITANCE'    => array('Capacitance' =>    'FARAD'),
        'Zend_Measure::COOKING_VOLUME' => array('Cooking_Volume' => 'CUBIC_METER'),
        'Zend_Measure::COOKING_WEIGHT' => array('Cooking_Weight' => 'GRAM'),
        'Zend_Measure::CURRENT'        => array('Current' =>        'AMPERE'),
        'Zend_Measure::DENSITY'        => array('Density' =>        'KILOGRAM_PER_CUBIC_METER'),
        'Zend_Measure::ENERGY'         => array('Energy' =>         'JOULE'),
        'Zend_Measure::FORCE'          => array('Force' =>          'NEWTON'),
        'Zend_Measure::FLOW_MASS'      => array('Flow_Mass' =>      'KILOGRAM_PER_SECOND'),
        'Zend_Measure::FLOW_MOLE'      => array('Flow_Mole' =>      'MOLE_PER_SECOND'),
        'Zend_Measure::FLOW_VOLUME'    => array('Flow_Volume' =>    'CUBIC_METER_PER_SECOND'),
        'Zend_Measure::FREQUENCY'      => array('Frequency' =>      'HERTZ'),
        'Zend_Measure::ILLUMINATION'   => array('Illumination' =>   'LUX'),
        'Zend_Measure::LENGTH'         => array('Length' =>         'METER'),
        'Zend_Measure::LIGHTNESS'      => array('Lightness' =>      'CANDELA_PER_SQUARE_METER'),
        'Zend_Measure::NUMBER'         => array('Number' =>         'DECIMAL'),
        'Zend_Measure::POWER'          => array('Power' =>          'WATT'),
        'Zend_Measure::PRESSURE'       => array('Pressure' =>       'NEWTON_PER_SQUARE_METER'),
        'Zend_Measure::SPEED'          => array('Speed' =>          'METER_PER_SECOND'),
        'Zend_Measure::TEMPERATURE'    => array('Temperature' =>    'KELVIN'),
        'Zend_Measure::TORQUE'         => array('Torque' =>         'NEWTON_METER'),
        'Zend_Measure::VISCOSITY_DYNAMIC'   => array('Viscosity_Dynamic' =>   'KILOGRAM_PER_METER_SECOND'),
        'Zend_Measure::VISCOSITY_KINEMATIC' => array('Viscosity_Kinematic' => 'SQUARE_METER_PER_SECOND'),
        'Zend_Measure::VOLUME'         => array('Volume' =>         'CUBIC_METER'),
        'Zend_Measure::WEIGHT'         => array('Weight' =>         'KILOGRAM')
    );

    private $_Measurement;
    private $_Locale;


    /**
     * Zend_Measure_Area provides an locale aware class for
     * conversion and formatting of area values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Area Type
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

        $library = substr($type, 0, strpos($type, '::'));
        $sublib  = substr($type, strpos($type, '::') + 2);

        if ($library == 'Zend_Measure')
        {
            if (!empty(self::$_UNIT[$type])) {
                $library = $library . '_' . key(self::$_UNIT[$type]);
            } else {
                throw Zend::exception('Zend_Measure_Exception', 'unknown measurement type ' . $type);
            }

            $sublib = key(self::$_UNIT[$type]) . '::' . current(self::$_UNIT[$type]);
            if (!empty($sublib)) {
                $sublib = key(self::$_UNIT[$type]) . '::' . current(self::$_UNIT[$type]);
            }
            Zend::loadClass($library);

        } else {
            $sublib = $library . '::' . $sublib;
            $library = 'Zend_Measure_' . $library;
        }

        $this->_Measurement = new $library($value, $sublib, $this->_Locale);
    }


    /**
     * Serialize
     */
    public function serialize()
    {
        return serialize($this);
    }


    /**
     * Compare if the value and type is equal
     *
     * @param $object  object to compare equality
     * @return boolean
     */
    public function equals($object)
    {
        return $this->_Measurement->equals($object);
    }


    /**
     * Returns the internal value
     *
     * @return value  mixed
     */
    public function getValue()
    {
        return $this->_Measurement->getValue();
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

        $library = substr($type, 0, strpos($type, '::'));

        if ($library == 'Zend_Measure')
        {
            $library = $library . '_' . key(self::$_UNIT[$type]);
            $type = key(self::$_UNIT[$type]) . '::' . current(self::$_UNIT[$type]);
        }

        $this->_Measurement->setValue($value, $type, $locale);
    }


    /**
     * Returns the original type
     *
     * @return type mixed
     */
    public function getType()
    {
        return $this->_Measurement->getType();
    }


    /**
     * Set a new type, and convert the value
     *
     * @param $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        $library = substr($type, 0, strpos($type, '::'));

        if ($library == 'Zend_Measure')
        {
            $library = $library . '_' . key(self::$_UNIT[$type]);
            $type = key(self::$_UNIT[$type]) . '::' . current(self::$_UNIT[$type]);
        }

        $this->_Measurement->setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return $this->_Measurement->__toString();
    }


    /**
     * Returns a string representation
     * Alias for toString()
     */
    public function __toString()
    {
        return $this->toString();
    }


    /**
     * Alias function for setType returning the converted unit
     *
     * @param $type  type
     * @return
     */
    public function convertTo($type)
    {
        $this->setType($type);
        return $this->toString();
    }


    /**
     * Adds an unit to another one
     *
     * @param $object  object of same unit type
     * @return object
     */
    public function add($object)
    {
        $object->setType($this->getType());
        $value  = $this->getValue() + $object->getValue();
        return new Zend_Measure($value, $this->getType(), $this->_Locale);
    }


    /**
     * Substracts an unit from another one
     *
     * @param $object  object of same unit type
     * @return object
     */
    public function sub($object)
    {
        $object->setType($this->getType());
        $value  = $this->getValue() - $object->getValue();
        return new Zend_Measure($value, $this->getType(), $this->_Locale);
    }


    /**
     * Compares two units
     *
     * @param $object  object of same unit type
     * @return object
     */
    public function compare($object)
    {
        $object->setType($this->getType());
        $value  = $this->getValue() - $object->getValue();

        return $value;
    }


    /**
     * Returns a list of all types
     *
     * @return array
     */
    public function getAllTypes()
    {
        foreach(self::$_UNIT as $temp) {
          $types[] = key($temp);
        }

        return $types;
    }


    /**
     * Returns a list of all types from a unit
     *
     * @return array
     */
    public function getTypeList()
    {
        $values = $this->_Measurement->getConversionList();
        return $values;
    }
}