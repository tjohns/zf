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
 * @subpackage Zend_Measure_Area
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Area extends Zend_Measure_Abstract
{
    // Area definitions
    const STANDARD = 'Area::SQUARE_METER';

    const ACRE               = 'Area::ACRE';
    const ACRE_COMMERCIAL    = 'Area::ACRE_COMMERCIAL';
    const ACRE_SURVEY        = 'Area::ACRE_SURVEY';
    const ACRE_IRELAND       = 'Area::ACRE_IRELAND';
    const ARE                = 'Area::ARE';
    const ARPENT             = 'Area::ARPENT';
    const BARN               = 'Area::BARN';
    const BOVATE             = 'Area::BOVATE';
    const BUNDER             = 'Area::BUNDER';
    const CABALLERIA         = 'Area::CABALLERIA';
    const CABALLERIA_AMERICA = 'Area::CABALLERIA_AMERICA';
    const CABALLERIA_CUBA    = 'Area::CABALLERIA_CUBA';
    const CARREAU            = 'Area::CARREAU';
    const CARUCATE           = 'Area::CARUCATE';
    const CAWNEY             = 'Area::CAWNEY';
    const CENTIARE           = 'Area::CENTIARE';
    const CONG               = 'Area::CONG';
    const COVER              = 'Area::COVER';
    const CUERDA             = 'Area::CUERDA';
    const DEKARE             = 'Area::DEKARE';
    const DESSIATINA         = 'Area::DESSIATINA';
    const DHUR               = 'Area::DHUR';
    const DUNUM              = 'Area::DUNUM';
    const DUNHAM             = 'Area::DUNHAM';
    const FALL_SCOTS         = 'Area::FALL_SCOTS';
    const FALL               = 'Area::FALL';
    const FANEGA             = 'Area::FANEGA';
    const FARTHINGDALE       = 'Area::FARTHINGDALE';
    const HACIENDA           = 'Area::HACIENDA';
    const HECTARE            = 'Area::HECTARE';
    const HIDE               = 'Area::HIDE';
    const HOMESTEAD          = 'Area::HOMESTEAD';
    const HUNDRED            = 'Area::HUNDRED';
    const JERIB              = 'Area::JERIB';
    const JITRO              = 'Area::JITRO';
    const JOCH               = 'Area::JOCH';
    const JUTRO              = 'Area::JUTRO';
    const JO                 = 'Area::JO';
    const KAPPLAND           = 'Area::KAPPLAND';
    const KATTHA             = 'Area::KATTHA';
    const LABOR              = 'Area::LABOR';
    const LEGUA              = 'Area::LEGUA';
    const MANZANA_COSTA_RICA = 'Area::MANZANA_COSTA_RICA';
    const MANZANA            = 'Area::MANZANA';
    const MORGEN             = 'Area::MORGEN';
    const MORGEN_AFRICA      = 'Area::MORGEN_AFRICA';
    const MU                 = 'Area::MU';
    const NGARN              = 'Area::NGARN';
    const NOOK               = 'Area::NOOK';
    const OXGANG             = 'Area::OXGANG';
    const PERCH              = 'Area::PERCH';
    const PERCHE             = 'Area::PERCHE';
    const PING               = 'Area::PING';
    const PYONG              = 'Area::PYONG';
    const RAI                = 'Area::RAI';
    const ROOD               = 'Area::ROOD';
    const SECTION            = 'Area::SECTION';
    const SHED               = 'Area::SHED';
    const SITIO              = 'Area::SITIO';
    const SQUARE             = 'Area::SQUARE';
    const SQUARE_ANGSTROM    = 'Area::SQUARE_ANGSTROM';
    const SQUARE_ASTRONOMICAL_UNIT = 'Area::SQUARE_ASTRONOMICAL_UNIT';
    const SQUARE_ATTOMETER   = 'Area::SQUARE_ATTOMETER';
    const SQUARE_BICRON      = 'Area::SQUARE_BICRON';
    const SQUARE_CENTIMETER  = 'Area::SQUARE_CENTIMETER';
    const SQUARE_CHAIN       = 'Area::SQUARE_CHAIN';
    const SQUARE_CHAIN_ENGINEER      = 'Area::SQUARE_CHAIN_ENGINEER';
    const SQUARE_CITY_BLOCK_US_EAST  = 'Area::SQUARE_CITY_BLOCK_US_EAST';
    const SQUARE_CITY_BLOCK_US_WEST  = 'Area::SQUARE_CITY_BLOCK_US_WEST';
    const SQUARE_CITY_BLOCK_US_SOUTH = 'Area::SQUARE_CITY_BLOCK_US_SOUTH';
    const SQUARE_CUBIT       = 'Area::SQUARE_CUBIT';
    const SQUARE_DECIMETER   = 'Area::SQUARE_DECIMETER';
    const SQUARE_DEKAMETER   = 'Area::SQUARE_DEKAMETER';
    const SQUARE_EXAMETER    = 'Area::SQUARE_EXAMETER';
    const SQUARE_FATHOM      = 'Area::SQUARE_FATHOM';
    const SQUARE_FEMTOMETER  = 'Area::SQUARE_FEMTOMETER';
    const SQUARE_FERMI       = 'Area::SQUARE_FERMI';
    const SQUARE_FOOT        = 'Area::SQUARE_FOOT';
    const SQUARE_FOOT_SURVEY = 'Area::SQUARE_FOOT_SURVEY';
    const SQUARE_FURLONG     = 'Area::SQUARE_FURLONG';
    const SQUARE_GIGAMETER   = 'Area::SQUARE_GIGAMETER';
    const SQUARE_HECTOMETER  = 'Area::SQUARE_HECTOMETER';
    const SQUARE_INCH        = 'Area::SQUARE_INCH';
    const SQUARE_INCH_SURVEY = 'Area::SQUARE_INCH_SURVEY';
    const SQUARE_KILOMETER   = 'Area::SQUARE_KILOMETER';
    const SQUARE_LEAGUE_NAUTIC = 'Area::SQUARE_LEAGUE_NAUTIC';
    const SQUARE_LEAGUE      = 'Area::SQUARE_LEAGUE';
    const SQUARE_LIGHT_YEAR  = 'Area::SQUARE_LIGHT_YEAR';
    const SQUARE_LINK        = 'Area::SQUARE_LINK';
    const SQUARE_LINK_ENGINEER = 'Area::SQUARE_LINK_ENGINEER';
    const SQUARE_MEGAMETER   = 'Area::SQUARE_MEGAMETER';
    const SQUARE_METER       = 'Area::SQUARE_METER';
    const SQUARE_MICROINCH   = 'Area::SQUARE_MICROINCH';
    const SQUARE_MICROMETER  = 'Area::SQUARE_MICROMETER';
    const SQUARE_MICROMICRON = 'Area::SQUARE_MICROMICRON';
    const SQUARE_MICRON      = 'Area::SQUARE_MICRON';
    const SQUARE_MIL         = 'Area::SQUARE_MIL';
    const SQUARE_MILE        = 'Area::SQUARE_MILE';
    const SQUARE_MILE_NAUTIC = 'Area::SQUARE_MILE_NAUTIC';
    const SQUARE_MILE_SURVEY = 'Area::SQUARE_MILE_SURVEY';
    const SQUARE_MILLIMETER  = 'Area::SQUARE_MILLIMETER';
    const SQUARE_MILLIMICRON = 'Area::SQUARE_MILLIMICRON';
    const SQUARE_MYRIAMETER  = 'Area::SQUARE_MYRIAMETER';
    const SQUARE_NANOMETER   = 'Area::SQUARE_NANOMETER';
    const SQUARE_PARIS_FOOT  = 'Area::SQUARE_PARIS_FOOT';
    const SQUARE_PARSEC      = 'Area::SQUARE_PARSEC';
    const SQUARE_PERCH       = 'Area::SQUARE_PERCH';
    const SQUARE_PERCHE      = 'Area::SQUARE_PERCHE';
    const SQUARE_PETAMETER   = 'Area::SQUARE_PETAMETER';
    const SQUARE_PICOMETER   = 'Area::SQUARE_PICOMETER';
    const SQUARE_ROD         = 'Area::SQUARE_ROD';
    const SQUARE_TENTHMETER  = 'Area::SQUARE_TENTHMETER';
    const SQUARE_TERAMETER   = 'Area::SQUARE_TERAMETER';
    const SQUARE_THOU        = 'Area::SQUARE_THOU';
    const SQUARE_VARA        = 'Area::SQUARE_VARA';
    const SQUARE_VARA_TEXAS  = 'Area::SQUARE_VARA_TEXAS';
    const SQUARE_YARD        = 'Area::SQUARE_YARD';
    const SQUARE_YARD_SURVEY = 'Area::SQUARE_YARD_SURVEY';
    const SQUARE_YOCTOMETER  = 'Area::SQUARE_YOCTOMETER';
    const SQUARE_YOTTAMETER  = 'Area::SQUARE_YOTTAMETER';
    const STANG              = 'Area::STANG';
    const STREMMA            = 'Area::STREMMA';
    const TAREA              = 'Area::TAREA';
    const TATAMI             = 'Area::TATAMI';
    const TONDE_LAND         = 'Area::TONDE_LAND';
    const TOWNSHIP           = 'Area::TOWNSHIP';
    const TSUBO              = 'Area::TSUBO';
    const TUNNLAND           = 'Area::TUNNLAND';
    const YARD               = 'Area::YARD';
    const VIRGATE            = 'Area::VIRGATE';

    private static $_UNITS = array(
        'Area::ACRE'               => array(4046.856422,      'A'),
        'Area::ACRE_COMMERCIAL'    => array(3344.50944,       'A'),
        'Area::ACRE_SURVEY'        => array(4046.872627,      'A'),
        'Area::ACRE_IRELAND'       => array(6555,             'A'),
        'Area::ARE'                => array(100,              'a'),
        'Area::ARPENT'             => array(3418.89,          'arpent'),
        'Area::BARN'               => array(1e-28,            'b'),
        'Area::BOVATE'             => array(60000,            'bovate'),
        'Area::BUNDER'             => array(10000,            'bunder'),
        'Area::CABALLERIA'         => array(400000,           'caballeria'),
        'Area::CABALLERIA_AMERICA' => array(450000,           'caballeria'),
        'Area::CABALLERIA_CUBA'    => array(134200,           'caballeria'),
        'Area::CARREAU'            => array(12900,            'carreau'),
        'Area::CARUCATE'           => array(486000,           'carucate'),
        'Area::CAWNEY'             => array(5400,             'cawney'),
        'Area::CENTIARE'           => array(1,                'ca'),
        'Area::CONG'               => array(1000,             'cong'),
        'Area::COVER'              => array(2698,             'cover'),
        'Area::CUERDA'             => array(3930,             'cda'),
        'Area::DEKARE'             => array(1000,             'dekare'),
        'Area::DESSIATINA'         => array(10925,            'dessiantina'),
        'Area::DHUR'               => array(16.929,           'dhur'),
        'Area::DUNUM'              => array(1000,             'dunum'),
        'Area::DUNHAM'             => array(1000,             'dunham'),
        'Area::FALL_SCOTS'         => array(32.15,            'fall'),
        'Area::FALL'               => array(47.03,            'fall'),
        'Area::FANEGA'             => array(6430,             'fanega'),
        'Area::FARTHINGDALE'       => array(1012,             'farthingdale'),
        'Area::HACIENDA'           => array(89600000,         'hacienda'),
        'Area::HECTARE'            => array(10000,            'ha'),
        'Area::HIDE'               => array(486000,           'hide'),
        'Area::HOMESTEAD'          => array(647500,           'homestead'),
        'Area::HUNDRED'            => array(50000000,         'hundred'),
        'Area::JERIB'              => array(2000,             'jerib'),
        'Area::JITRO'              => array(5755,             'jitro'),
        'Area::JOCH'               => array(5755,             'joch'),
        'Area::JUTRO'              => array(5755,             'jutro'),
        'Area::JO'                 => array(1.62,             'jo'),
        'Area::KAPPLAND'           => array(154.26,           'kappland'),
        'Area::KATTHA'             => array(338,              'kattha'),
        'Area::LABOR'              => array(716850,           'labor'),
        'Area::LEGUA'              => array(17920000,         'legua'),
        'Area::MANZANA_COSTA_RICA' => array(6988.96,          'manzana'),
        'Area::MANZANA'            => array(10000,            'manzana'),
        'Area::MORGEN'             => array(2500,             'morgen'),
        'Area::MORGEN_AFRICA'      => array(8567,             'morgen'),
        'Area::MU'                 => array(array('' => 10000, '/' => 15), 'mu'),
        'Area::NGARN'              => array(400,              'ngarn'),
        'Area::NOOK'               => array(80937.128,        'nook'),
        'Area::OXGANG'             => array(60000,            'oxgang'),
        'Area::PERCH'              => array(25.29285264,      'perch'),
        'Area::PERCHE'             => array(34.19,            'perche'),
        'Area::PING'               => array(3.305,            'ping'),
        'Area::PYONG'              => array(3.306,            'pyong'),
        'Area::RAI'                => array(1600,             'rai'),
        'Area::ROOD'               => array(1011.7141,        'rood'),
        'Area::SECTION'            => array(2589998.5,        'sec'),
        'Area::SHED'               => array(10e-52,           'shed'),
        'Area::SITIO'              => array(18000000,         'sitio'),
        'Area::SQUARE'             => array(9.290304,         'sq'),
        'Area::SQUARE_ANGSTROM'    => array(1.0e-20,          'A²'),
        'Area::SQUARE_ASTRONOMICAL_UNIT'   => array(2.2379523e+22, 'AU²'),
        'Area::SQUARE_ATTOMETER'   => array(1.0e-36,          'am²'),
        'Area::SQUARE_BICRON'      => array(1.0e-24,          'µµ²'),
        'Area::SQUARE_CENTIMETER'  => array(0.0001,           'cm²'),
        'Area::SQUARE_CHAIN'       => array(404.68726,        'ch²'),
        'Area::SQUARE_CHAIN_ENGINEER'      => array(929.03412,   'ch²'),
        'Area::SQUARE_CITY_BLOCK_US_EAST'  => array(4.97027584,  'sq block'),
        'Area::SQUARE_CITY_BLOCK_US_WEST'  => array(17.141056,   'sq block'),
        'Area::SQUARE_CITY_BLOCK_US_SOUTH' => array(99.88110336, 'sq block'),
        'Area::SQUARE_CUBIT'       => array(0.20903184,       'sq cubit'),
        'Area::SQUARE_DECIMETER'   => array(0.01,             'dm²'),
        'Area::SQUARE_DEKAMETER'   => array(100,              'dam²'),
        'Area::SQUARE_EXAMETER'    => array(1.0e+36,          'Em²'),
        'Area::SQUARE_FATHOM'      => array(3.3445228,        'fth²'),
        'Area::SQUARE_FEMTOMETER'  => array(1.0e-30,          'fm²'),
        'Area::SQUARE_FERMI'       => array(1.0e-30,          'f²'),
        'Area::SQUARE_FOOT'        => array(0.09290304,       'ft²'),
        'Area::SQUARE_FOOT_SURVEY' => array(0.092903412,      'ft²'),
        'Area::SQUARE_FURLONG'     => array(40468.726,        'fur²'),
        'Area::SQUARE_GIGAMETER'   => array(1.0e+18,          'Gm²'),
        'Area::SQUARE_HECTOMETER'  => array(10000,            'hm²'),
        'Area::SQUARE_INCH'        => array(array('' => 0.09290304,'/' => 144),  'in²'),
        'Area::SQUARE_INCH_SURVEY' => array(array('' => 0.092903412,'/' => 144), 'in²'),
        'Area::SQUARE_KILOMETER'   => array(1000000,          'km²'),
        'Area::SQUARE_LEAGUE_NAUTIC' => array(3.0869136e+07,  'sq league'),
        'Area::SQUARE_LEAGUE'      => array(2.3309986e+07,    'sq league'),
        'Area::SQUARE_LIGHT_YEAR'  => array(8.9505412e+31,    'ly²'),
        'Area::SQUARE_LINK'        => array(0.040468726,      'sq link'),
        'Area::SQUARE_LINK_ENGINEER' => array(0.092903412,    'sq link'),
        'Area::SQUARE_MEGAMETER'   => array(1.0e+12,          'Mm²'),
        'Area::SQUARE_METER'       => array(1,                'm²'),
        'Area::SQUARE_MICROINCH'   => array(array('' => 1.0e-6,'*' => 6.4516e-10), 'µin²'),
        'Area::SQUARE_MICROMETER'  => array(1.0e-12,          'µm²'),
        'Area::SQUARE_MICROMICRON' => array(1.0e-24,          'µµ²'),
        'Area::SQUARE_MICRON'      => array(1.0e-12,          'µ²'),
        'Area::SQUARE_MIL'         => array(6.4516e-10,       'sq mil'),
        'Area::SQUARE_MILE'        => array(array('' => 0.09290304,'*' => 27878400), 'mi²'),
        'Area::SQUARE_MILE_NAUTIC' => array(3429904,          'mi²'),
        'Area::SQUARE_MILE_SURVEY' => array(2589998.5,        'mi²'),
        'Area::SQUARE_MILLIMETER'  => array(0.000001,         'mm²'),
        'Area::SQUARE_MILLIMICRON' => array(1.0e-18,          'mµ²'),
        'Area::SQUARE_MYRIAMETER'  => array(1.0e+8,           'mym²'),
        'Area::SQUARE_NANOMETER'   => array(1.0e-18,          'nm²'),
        'Area::SQUARE_PARIS_FOOT'  => array(0.1055,           'sq paris foot'),
        'Area::SQUARE_PARSEC'      => array(9.5214087e+32,    'pc²'),
        'Area::SQUARE_PERCH'       => array(25.292954,        'sq perch'),
        'Area::SQUARE_PERCHE'      => array(51.072,           'sq perche'),
        'Area::SQUARE_PETAMETER'   => array(1.0e+30,          'Pm²'),
        'Area::SQUARE_PICOMETER'   => array(1.0e-24,          'pm²'),
        'Area::SQUARE_ROD'         => array(array('' => 0.092903412,'*' => 272.25), 'rd²'),
        'Area::SQUARE_TENTHMETER'  => array(1.0e-20,          'sq tenth-meter'),
        'Area::SQUARE_TERAMETER'   => array(1.0e+24,          'Tm²'),
        'Area::SQUARE_THOU'        => array(6.4516e-10,       'sq thou'),
        'Area::SQUARE_VARA'        => array(0.70258205,       'sq vara'),
        'Area::SQUARE_VARA_TEXAS'  => array(0.71684731,       'sq vara'),
        'Area::SQUARE_YARD'        => array(0.83612736,       'yd²'),
        'Area::SQUARE_YARD_SURVEY' => array(0.836130708,      'yd²'),
        'Area::SQUARE_YOCTOMETER'  => array(1.0e-48,          'ym²'),
        'Area::SQUARE_YOTTAMETER'  => array(1.0e+48,          'Ym²'),
        'Area::STANG'              => array(2709,             'stang'),
        'Area::STREMMA'            => array(1000,             'stremma'),
        'Area::TAREA'              => array(628.8,            'tarea'),
        'Area::TATAMI'             => array(1.62,             'tatami'),
        'Area::TONDE_LAND'         => array(5516,             'tonde land'),
        'Area::TOWNSHIP'           => array(93239945.3196288, 'twp'),
        'Area::TSUBO'              => array(3.3058,           'tsubo'),
        'Area::TUNNLAND'           => array(4936.4,           'tunnland'),
        'Area::YARD'               => array(0.83612736,       'yd'),
        'Area::VIRGATE'            => array(120000,           'virgate')
    );

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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Area Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of area:' . $type);
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of area:' . $type);
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