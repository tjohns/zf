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
 * @subpackage Zend_Measure_Flow_Volume
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Flow_Volume extends Zend_Measure_Abstract
{
    // Flow_Volume definitions
    const STANDARD = 'Flow_Volume::CUBIC_METER_PER_SECOND';

    const ACRE_FOOT_PER_DAY           = 'Flow_Volume::ACRE_FOOT_PER_DAY';
    const ACRE_FOOT_PER_HOUR          = 'Flow_Volume::ACRE_FOOT_PER_HOUR';
    const ACRE_FOOT_PER_MINUTE        = 'Flow_Volume::ACRE_FOOT_PER_MINUTE';
    const ACRE_FOOT_PER_SECOND        = 'Flow_Volume::ACRE_FOOT_PER_SECOND';
    const ACRE_FOOT_SURVEY_PER_DAY    = 'Flow_Volume::ACRE_FOOT_SURVEY_PER_DAY';
    const ACRE_FOOT_SURVEY_PER_HOUR   = 'Flow_Volume::ACRE_FOOT_SURVEY_PER_HOUR';
    const ACRE_FOOT_SURVEY_PER_MINUTE = 'Flow_Volume::ACRE_FOOT_SURVEY_PER_MINUTE';
    const ACRE_FOOT_SURVEY_PER_SECOND = 'Flow_Volume::ACRE_FOOT_SURVEY_PER_SECOND';
    const ACRE_INCH_PER_DAY           = 'Flow_Volume::ACRE_INCH_PER_DAY';
    const ACRE_INCH_PER_HOUR          = 'Flow_Volume::ACRE_INCH_PER_HOUR';
    const ACRE_INCH_PER_MINUTE        = 'Flow_Volume::ACRE_INCH_PER_MINUTE';
    const ACRE_INCH_PER_SECOND        = 'Flow_Volume::ACRE_INCH_PER_SECOND';
    const ACRE_INCH_SURVEY_PER_DAY    = 'Flow_Volume::ACRE_INCH_SURVEY_PER_DAY';
    const ACRE_INCH_SURVEY_PER_HOUR   = 'Flow_Volume::ACRE_INCH_SURVEY_PER_HOUR';
    const ACRE_INCH_SURVEY_PER_MINUTE = 'Flow_Volume::ACRE_INCH_SURVEY_PER_MINUTE';
    const ACRE_INCH_SURVEY_PER_SECOND = 'Flow_Volume::ACRE_INCH_SURVEY_PER_SECOND';
    const BARREL_PETROLEUM_PER_DAY    = 'Flow_Volume::BARREL_PETROLEUM_PER_DAY';
    const BARREL_PETROLEUM_PER_HOUR   = 'Flow_Volume::BARREL_PETROLEUM_PER_HOUR';
    const BARREL_PETROLEUM_PER_MINUTE = 'Flow_Volume::BARREL_PETROLEUM_PER_MINUTE';
    const BARREL_PETROLEUM_PER_SECOND = 'Flow_Volume::BARREL_PETROLEUM_PER_SECOND';
    const BARREL_PER_DAY              = 'Flow_Volume::BARREL_PER_DAY';
    const BARREL_PER_HOUR             = 'Flow_Volume::BARREL_PER_HOUR';
    const BARREL_PER_MINUTE           = 'Flow_Volume::BARREL_PER_MINUTE';
    const BARREL_PER_SECOND           = 'Flow_Volume::BARREL_PER_SECOND';
    const BARREL_US_PER_DAY           = 'Flow_Volume::BARREL_US_PER_DAY';
    const BARREL_US_PER_HOUR          = 'Flow_Volume::BARREL_US_PER_HOUR';
    const BARREL_US_PER_MINUTE        = 'Flow_Volume::BARREL_US_PER_MINUTE';
    const BARREL_US_PER_SECOND        = 'Flow_Volume::BARREL_US_PER_SECOND';
    const BARREL_WINE_PER_DAY         = 'Flow_Volume::BARREL_WINE_PER_DAY';
    const BARREL_WINE_PER_HOUR        = 'Flow_Volume::BARREL_WINE_PER_HOUR';
    const BARREL_WINE_PER_MINUTE      = 'Flow_Volume::BARREL_WINE_PER_MINUTE';
    const BARREL_WINE_PER_SECOND      = 'Flow_Volume::BARREL_WINE_PER_SECOND';
    const BARREL_BEER_PER_DAY         = 'Flow_Volume::BARREL_BEER_PER_DAY';
    const BARREL_BEER_PER_HOUR        = 'Flow_Volume::BARREL_BEER_PER_HOUR';
    const BARREL_BEER_PER_MINUTE      = 'Flow_Volume::BARREL_BEER_PER_MINUTE';
    const BARREL_BEER_PER_SECOND      = 'Flow_Volume::BARREL_BEER_PER_SECOND';
    const BILLION_CUBIC_FOOT_PER_DAY  = 'Flow_Volume::BILLION_CUBIC_FOOT_PER_DAY';
    const BILLION_CUBIC_FOOT_PER_HOUR = 'Flow_Volume::BILLION_CUBIC_FOOT_PER_HOUR';
    const BILLION_CUBIC_FOOT_PER_MINUTE = 'Flow_Volume::BILLION_CUBIC_FOOT_PER_MINUTE';
    const BILLION_CUBIC_FOOT_PER_SECOND = 'Flow_Volume::BILLION_CUBIC_FOOT_PER_SECOND';
    const CENTILITER_PER_DAY          = 'Flow_Volume::CENTILITER_PER_DAY';
    const CENTILITER_PER_HOUR         = 'Flow_Volume::CENTILITER_PER_HOUR';
    const CENTILITER_PER_MINUTE       = 'Flow_Volume::CENTILITER_PER_MINUTE';
    const CENTILITER_PER_SECOND       = 'Flow_Volume::CENTILITER_PER_SECOND';
    const CUBEM_PER_DAY               = 'Flow_Volume::CUBEM_PER_DAY';
    const CUBEM_PER_HOUR              = 'Flow_Volume::CUBEM_PER_HOUR';
    const CUBEM_PER_MINUTE            = 'Flow_Volume::CUBEM_PER_MINUTE';
    const CUBEM_PER_SECOND            = 'Flow_Volume::CUBEM_PER_SECOND';
    const CUBIC_CENTIMETER_PER_DAY    = 'Flow_Volume::CUBIC_CENTIMETER_PER_DAY';
    const CUBIC_CENTIMETER_PER_HOUR   = 'Flow_Volume::CUBIC_CENTIMETER_PER_HOUR';
    const CUBIC_CENTIMETER_PER_MINUTE = 'Flow_Volume::CUBIC_CENTIMETER_PER_MINUTE';
    const CUBIC_CENTIMETER_PER_SECOND = 'Flow_Volume::CUBIC_CENTIMETER_PER_SECOND';
    const CUBIC_DECIMETER_PER_DAY     = 'Flow_Volume::CUBIC_DECIMETER_PER_DAY';
    const CUBIC_DECIMETER_PER_HOUR    = 'Flow_Volume::CUBIC_DECIMETER_PER_HOUR';
    const CUBIC_DECIMETER_PER_MINUTE  = 'Flow_Volume::CUBIC_DECIMETER_PER_MINUTE';
    const CUBIC_DECIMETER_PER_SECOND  = 'Flow_Volume::CUBIC_DECIMETER_PER_SECOND';
    const CUBIC_DEKAMETER_PER_DAY     = 'Flow_Volume::CUBIC_DEKAMETER_PER_DAY';
    const CUBIC_DEKAMETER_PER_HOUR    = 'Flow_Volume::CUBIC_DEKAMETER_PER_HOUR';
    const CUBIC_DEKAMETER_PER_MINUTE  = 'Flow_Volume::CUBIC_DEKAMETER_PER_MINUTE';
    const CUBIC_DEKAMETER_PER_SECOND  = 'Flow_Volume::CUBIC_DEKAMETER_PER_SECOND';
    const CUBIC_FOOT_PER_DAY          = 'Flow_Volume::CUBIC_FOOT_PER_DAY';
    const CUBIC_FOOT_PER_HOUR         = 'Flow_Volume::CUBIC_FOOT_PER_HOUR';
    const CUBIC_FOOT_PER_MINUTE       = 'Flow_Volume::CUBIC_FOOT_PER_MINUTE';
    const CUBIC_FOOT_PER_SECOND       = 'Flow_Volume::CUBIC_FOOT_PER_SECOND';
    const CUBIC_INCH_PER_DAY          = 'Flow_Volume::CUBIC_INCH_PER_DAY';
    const CUBIC_INCH_PER_HOUR         = 'Flow_Volume::CUBIC_INCH_PER_HOUR';
    const CUBIC_INCH_PER_MINUTE       = 'Flow_Volume::CUBIC_INCH_PER_MINUTE';
    const CUBIC_INCH_PER_SECOND       = 'Flow_Volume::CUBIC_INCH_PER_SECOND';
    const CUBIC_KILOMETER_PER_DAY     = 'Flow_Volume::CUBIC_KILOMETER_PER_DAY';
    const CUBIC_KILOMETER_PER_HOUR    = 'Flow_Volume::CUBIC_KILOMETER_PER_HOUR';
    const CUBIC_KILOMETER_PER_MINUTE  = 'Flow_Volume::CUBIC_KILOMETER_PER_MINUTE';
    const CUBIC_KILOMETER_PER_SECOND  = 'Flow_Volume::CUBIC_KILOMETER_PER_SECOND';
    const CUBIC_METER_PER_DAY         = 'Flow_Volume::CUBIC_METER_PER_DAY';
    const CUBIC_METER_PER_HOUR        = 'Flow_Volume::CUBIC_METER_PER_HOUR';
    const CUBIC_METER_PER_MINUTE      = 'Flow_Volume::CUBIC_METER_PER_MINUTE';
    const CUBIC_METER_PER_SECOND      = 'Flow_Volume::CUBIC_METER_PER_SECOND';
    const CUBIC_MILE_PER_DAY          = 'Flow_Volume::CUBIC_MILE_PER_DAY';
    const CUBIC_MILE_PER_HOUR         = 'Flow_Volume::CUBIC_MILE_PER_HOUR';
    const CUBIC_MILE_PER_MINUTE       = 'Flow_Volume::CUBIC_MILE_PER_MINUTE';
    const CUBIC_MILE_PER_SECOND       = 'Flow_Volume::CUBIC_MILE_PER_SECOND';
    const CUBIC_MILLIMETER_PER_DAY    = 'Flow_Volume::CUBIC_MILLIMETER_PER_DAY';
    const CUBIC_MILLIMETER_PER_HOUR   = 'Flow_Volume::CUBIC_MILLIMETER_PER_HOUR';
    const CUBIC_MILLIMETER_PER_MINUTE = 'Flow_Volume::CUBIC_MILLIMETER_PER_MINUTE';
    const CUBIC_MILLIMETER_PER_SECOND = 'Flow_Volume::CUBIC_MILLIMETER_PER_SECOND';
    const CUBIC_YARD_PER_DAY          = 'Flow_Volume::CUBIC_YARD_PER_DAY';
    const CUBIC_YARD_PER_HOUR         = 'Flow_Volume::CUBIC_YARD_PER_HOUR';
    const CUBIC_YARD_PER_MINUTE       = 'Flow_Volume::CUBIC_YARD_PER_MINUTE';
    const CUBIC_YARD_PER_SECOND       = 'Flow_Volume::CUBIC_YARD_PER_SECOND';
    const CUSEC                       = 'Flow_Volume::CUSEC';
    const DECILITER_PER_DAY           = 'Flow_Volume::DECILITER_PER_DAY';
    const DECILITER_PER_HOUR          = 'Flow_Volume::DECILITER_PER_HOUR';
    const DECILITER_PER_MINUTE        = 'Flow_Volume::DECILITER_PER_MINUTE';
    const DECILITER_PER_SECOND        = 'Flow_Volume::DECILITER_PER_SECOND';
    const DEKALITER_PER_DAY           = 'Flow_Volume::DEKALITER_PER_DAY';
    const DEKALITER_PER_HOUR          = 'Flow_Volume::DEKALITER_PER_HOUR';
    const DEKALITER_PER_MINUTE        = 'Flow_Volume::DEKALITER_PER_MINUTE';
    const DEKALITER_PER_SECOND        = 'Flow_Volume::DEKALITER_PER_SECOND';
    const GALLON_PER_DAY              = 'Flow_Volume::GALLON_PER_DAY';
    const GALLON_PER_HOUR             = 'Flow_Volume::GALLON_PER_HOUR';
    const GALLON_PER_MINUTE           = 'Flow_Volume::GALLON_PER_MINUTE';
    const GALLON_PER_SECOND           = 'Flow_Volume::GALLON_PER_SECOND';
    const GALLON_US_PER_DAY           = 'Flow_Volume::GALLON_US_PER_DAY';
    const GALLON_US_PER_HOUR          = 'Flow_Volume::GALLON_US_PER_HOUR';
    const GALLON_US_PER_MINUTE        = 'Flow_Volume::GALLON_US_PER_MINUTE';
    const GALLON_US_PER_SECOND        = 'Flow_Volume::GALLON_US_PER_SECOND';
    const HECTARE_METER_PER_DAY       = 'Flow_Volume::HECTARE_METER_PER_DAY';
    const HECTARE_METER_PER_HOUR      = 'Flow_Volume::HECTARE_METER_PER_HOUR';
    const HECTARE_METER_PER_MINUTE    = 'Flow_Volume::HECTARE_METER_PER_MINUTE';
    const HECTARE_METER_PER_SECOND    = 'Flow_Volume::HECTARE_METER_PER_SECOND';
    const HECTOLITER_PER_DAY          = 'Flow_Volume::HECTOLITER_PER_DAY';
    const HECTOLITER_PER_HOUR         = 'Flow_Volume::HECTOLITER_PER_HOUR';
    const HECTOLITER_PER_MINUTE       = 'Flow_Volume::HECTOLITER_PER_MINUTE';
    const HECTOLITER_PER_SECOND       = 'Flow_Volume::HECTOLITER_PER_SECOND';
    const KILOLITER_PER_DAY           = 'Flow_Volume::KILOLITER_PER_DAY';
    const KILOLITER_PER_HOUR          = 'Flow_Volume::KILOLITER_PER_HOUR';
    const KILOLITER_PER_MINUTE        = 'Flow_Volume::KILOLITER_PER_MINUTE';
    const KILOLITER_PER_SECOND        = 'Flow_Volume::KILOLITER_PER_SECOND';
    const LAMBDA_PER_DAY              = 'Flow_Volume::LAMBDA_PER_DAY';
    const LAMBDA_PER_HOUR             = 'Flow_Volume::LAMBDA_PER_HOUR';
    const LAMBDA_PER_MINUTE           = 'Flow_Volume::LAMBDA_PER_MINUTE';
    const LAMBDA_PER_SECOND           = 'Flow_Volume::LAMBDA_PER_SECOND';
    const LITER_PER_DAY               = 'Flow_Volume::LITER_PER_DAY';
    const LITER_PER_HOUR              = 'Flow_Volume::LITER_PER_HOUR';
    const LITER_PER_MINUTE            = 'Flow_Volume::LITER_PER_MINUTE';
    const LITER_PER_SECOND            = 'Flow_Volume::LITER_PER_SECOND';
    const MILLILITER_PER_DAY          = 'Flow_Volume::MILLILITER_PER_DAY';
    const MILLILITER_PER_HOUR         = 'Flow_Volume::MILLILITER_PER_HOUR';
    const MILLILITER_PER_MINUTE       = 'Flow_Volume::MILLILITER_PER_MINUTE';
    const MILLILITER_PER_SECOND       = 'Flow_Volume::MILLILITER_PER_SECOND';
    const MILLION_ACRE_FOOT_PER_DAY   = 'Flow_Volume::MILLION_ACRE_FOOT_PER_DAY';
    const MILLION_ACRE_FOOT_PER_HOUR  = 'Flow_Volume::MILLION_ACRE_FOOT_PER_HOUR';
    const MILLION_ACRE_FOOT_PER_MINUTE = 'Flow_Volume::MILLION_ACRE_FOOT_PER_MINUTE';
    const MILLION_ACRE_FOOT_PER_SECOND = 'Flow_Volume::MILLION_ACRE_FOOT_PER_SECOND';
    const MILLION_CUBIC_FOOT_PER_DAY  = 'Flow_Volume::MILLION_CUBIC_FOOT_PER_DAY';
    const MILLION_CUBIC_FOOT_PER_HOUR = 'Flow_Volume::MILLION_CUBIC_FOOT_PER_HOUR';
    const MILLION_CUBIC_FOOT_PER_MINUTE = 'Flow_Volume::MILLION_CUBIC_FOOT_PER_MINUTE';
    const MILLION_CUBIC_FOOT_PER_SECOND = 'Flow_Volume::MILLION_CUBIC_FOOT_PER_SECOND';
    const MILLION_GALLON_PER_DAY      = 'Flow_Volume::MILLION_GALLON_PER_DAY';
    const MILLION_GALLON_PER_HOUR     = 'Flow_Volume::MILLION_GALLON_PER_HOUR';
    const MILLION_GALLON_PER_MINUTE   = 'Flow_Volume::MILLION_GALLON_PER_MINUTE';
    const MILLION_GALLON_PER_SECOND   = 'Flow_Volume::MILLION_GALLON_PER_SECOND';
    const MILLION_GALLON_US_PER_DAY   = 'Flow_Volume::MILLION_GALLON_US_PER_DAY';
    const MILLION_GALLON_US_PER_HOUR  = 'Flow_Volume::MILLION_GALLON_US_PER_HOUR';
    const MILLION_GALLON_US_PER_MINUTE = 'Flow_Volume::MILLION_GALLON_US_PER_MINUTE';
    const MILLION_GALLON_US_PER_SECOND = 'Flow_Volume::MILLION_GALLON_US_PER_SECOND';
    const MINERS_INCH_AZ              = 'Flow_Volume::MINERS_INCH_AZ';
    const MINERS_INCH_CA              = 'Flow_Volume::MINERS_INCH_CA';
    const MINERS_INCH_OR              = 'Flow_Volume::MINERS_INCH_OR';
    const MINERS_INCH_CO              = 'Flow_Volume::MINERS_INCH_CO';
    const MINERS_INCH_ID              = 'Flow_Volume::MINERS_INCH_ID';
    const MINERS_INCH_WA              = 'Flow_Volume::MINERS_INCH_WA';
    const MINERS_INCH_NM              = 'Flow_Volume::MINERS_INCH_NM';
    const OUNCE_PER_DAY               = 'Flow_Volume::OUNCE_PER_DAY';
    const OUNCE_PER_HOUR              = 'Flow_Volume::OUNCE_PER_HOUR';
    const OUNCE_PER_MINUTE            = 'Flow_Volume::OUNCE_PER_MINUTE';
    const OUNCE_PER_SECOND            = 'Flow_Volume::OUNCE_PER_SECOND';
    const OUNCE_US_PER_DAY            = 'Flow_Volume::OUNCE_US_PER_DAY';
    const OUNCE_US_PER_HOUR           = 'Flow_Volume::OUNCE_US_PER_HOUR';
    const OUNCE_US_PER_MINUTE         = 'Flow_Volume::OUNCE_US_PER_MINUTE';
    const OUNCE_US_PER_SECOND         = 'Flow_Volume::OUNCE_US_PER_SECOND';
    const PETROGRAD_STANDARD_PER_DAY  = 'Flow_Volume::PETROGRAD_STANDARD_PER_DAY';
    const PETROGRAD_STANDARD_PER_HOUR = 'Flow_Volume::PETROGRAD_STANDARD_PER_HOUR';
    const PETROGRAD_STANDARD_PER_MINUTE = 'Flow_Volume::PETROGRAD_STANDARD_PER_MINUTE';
    const PETROGRAD_STANDARD_PER_SECOND = 'Flow_Volume::PETROGRAD_STANDARD_PER_SECOND';
    const STERE_PER_DAY               = 'Flow_Volume::STERE_PER_DAY';
    const STERE_PER_HOUR              = 'Flow_Volume::STERE_PER_HOUR';
    const STERE_PER_MINUTE            = 'Flow_Volume::STERE_PER_MINUTE';
    const STERE_PER_SECOND            = 'Flow_Volume::STERE_PER_SECOND';
    const THOUSAND_CUBIC_FOOT_PER_DAY = 'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_DAY';
    const THOUSAND_CUBIC_FOOT_PER_HOUR   = 'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_HOUR';
    const THOUSAND_CUBIC_FOOT_PER_MINUTE = 'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_MINUTE';
    const THOUSAND_CUBIC_FOOT_PER_SECOND = 'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_SECOND';
    const TRILLION_CUBIC_FOOT_PER_DAY    = 'Flow_Volume::TRILLION_CUBIC_FOOT_PER_DAY';
    const TRILLION_CUBIC_FOOT_PER_HOUR   = 'Flow_Volume::TRILLION_CUBIC_FOOT_PER_HOUR';
    const TRILLION_CUBIC_FOOT_PER_MINUTE = 'Flow_Volume::TRILLION_CUBIC_FOOT_PER_MINUTE';
    const TRILLION_CUBIC_FOOT_PER_SECOND = 'Flow_Volume::TRILLION_CUBIC_FOOT_PER_';

    private static $_UNITS = array(
        'Flow_Volume::ACRE_FOOT_PER_DAY'           => array(array('' => 1233.48184, '/' => 86400),      'ac ft/day'),
        'Flow_Volume::ACRE_FOOT_PER_HOUR'          => array(array('' => 1233.48184, '/' => 3600),       'ac ft/h'),
        'Flow_Volume::ACRE_FOOT_PER_MINUTE'        => array(array('' => 1233.48184, '/' => 60),         'ac ft/m'),
        'Flow_Volume::ACRE_FOOT_PER_SECOND'        => array(1233.48184,                                 'ac ft/s'),
        'Flow_Volume::ACRE_FOOT_SURVEY_PER_DAY'    => array(array('' => 1233.48924, '/' => 86400),      'ac ft/day'),
        'Flow_Volume::ACRE_FOOT_SURVEY_PER_HOUR'   => array(array('' => 1233.48924, '/' => 3600),       'ac ft/h'),
        'Flow_Volume::ACRE_FOOT_SURVEY_PER_MINUTE' => array(array('' => 1233.48924, '/' => 60),         'ac ft/m'),
        'Flow_Volume::ACRE_FOOT_SURVEY_PER_SECOND' => array(1233.48924,                                 'ac ft/s'),
        'Flow_Volume::ACRE_INCH_PER_DAY'           => array(array('' => 1233.48184, '/' => 1036800),    'ac in/day'),
        'Flow_Volume::ACRE_INCH_PER_HOUR'          => array(array('' => 1233.48184, '/' => 43200),      'ac in/h'),
        'Flow_Volume::ACRE_INCH_PER_MINUTE'        => array(array('' => 1233.48184, '/' => 720),        'ac in/m'),
        'Flow_Volume::ACRE_INCH_PER_SECOND'        => array(array('' => 1233.48184, '/' => 12),         'ac in/s'),
        'Flow_Volume::ACRE_INCH_SURVEY_PER_DAY'    => array(array('' => 1233.48924, '/' => 1036800),    'ac in/day'),
        'Flow_Volume::ACRE_INCH_SURVEY_PER_HOUR'   => array(array('' => 1233.48924, '/' => 43200),      'ac in/h'),
        'Flow_Volume::ACRE_INCH_SURVEY_PER_MINUTE' => array(array('' => 1233.48924, '/' => 720),        'ac in /m'),
        'Flow_Volume::ACRE_INCH_SURVEY_PER_SECOND' => array(array('' => 1233.48924, '/' => 12),         'ac in/s'),
        'Flow_Volume::BARREL_PETROLEUM_PER_DAY'    => array(array('' => 0.1589872956, '/' => 86400),    'bbl/day'),
        'Flow_Volume::BARREL_PETROLEUM_PER_HOUR'   => array(array('' => 0.1589872956, '/' => 3600),     'bbl/h'),
        'Flow_Volume::BARREL_PETROLEUM_PER_MINUTE' => array(array('' => 0.1589872956, '/' => 60),       'bbl/m'),
        'Flow_Volume::BARREL_PETROLEUM_PER_SECOND' => array(0.1589872956,                               'bbl/s'),
        'Flow_Volume::BARREL_PER_DAY'              => array(array('' => 0.16365924, '/' => 86400),      'bbl/day'),
        'Flow_Volume::BARREL_PER_HOUR'             => array(array('' => 0.16365924, '/' => 3600),       'bbl/h'),
        'Flow_Volume::BARREL_PER_MINUTE'           => array(array('' => 0.16365924, '/' => 60),         'bbl/m'),
        'Flow_Volume::BARREL_PER_SECOND'           => array(0.16365924,                                 'bbl/s'),
        'Flow_Volume::BARREL_US_PER_DAY'           => array(array('' => 0.1192404717, '/' => 86400),    'bbl/day'),
        'Flow_Volume::BARREL_US_PER_HOUR'          => array(array('' => 0.1192404717, '/' => 3600),     'bbl/h'),
        'Flow_Volume::BARREL_US_PER_MINUTE'        => array(array('' => 0.1192404717, '/' => 60),       'bbl/m'),
        'Flow_Volume::BARREL_US_PER_SECOND'        => array(0.1192404717,                               'bbl/s'),
        'Flow_Volume::BARREL_WINE_PER_DAY'         => array(array('' => 0.1173477658, '/' => 86400),    'bbl/day'),
        'Flow_Volume::BARREL_WINE_PER_HOUR'        => array(array('' => 0.1173477658, '/' => 3600),     'bbl/h'),
        'Flow_Volume::BARREL_WINE_PER_MINUTE'      => array(array('' => 0.1173477658, '/' => 60),       'bbl/m'),
        'Flow_Volume::BARREL_WINE_PER_SECOND'      => array(0.1173477658,                               'bbl/s'),
        'Flow_Volume::BARREL_BEER_PER_DAY'         => array(array('' => 0.1173477658, '/' => 86400),    'bbl/day'),
        'Flow_Volume::BARREL_BEER_PER_HOUR'        => array(array('' => 0.1173477658, '/' => 3600),     'bbl/h'),
        'Flow_Volume::BARREL_BEER_PER_MINUTE'      => array(array('' => 0.1173477658, '/' => 60),       'bbl/m'),
        'Flow_Volume::BARREL_BEER_PER_SECOND'      => array(0.1173477658,                               'bbl/s'),
        'Flow_Volume::BILLION_CUBIC_FOOT_PER_DAY'  => array(array('' => 28316847, '/' => 86400),        'bn ft³/day'),
        'Flow_Volume::BILLION_CUBIC_FOOT_PER_HOUR' => array(array('' => 28316847, '/' => 3600),         'bn ft³/h'),
        'Flow_Volume::BILLION_CUBIC_FOOT_PER_MINUTE' => array(array('' => 28316847, '/' => 60),         'bn ft³/m'),
        'Flow_Volume::BILLION_CUBIC_FOOT_PER_SECOND' => array(28316847,                                 'bn ft³/s'),
        'Flow_Volume::CENTILITER_PER_DAY'          => array(array('' => 0.00001, '/' => 86400),         'cl/day'),
        'Flow_Volume::CENTILITER_PER_HOUR'         => array(array('' => 0.00001, '/' => 3600),          'cl/h'),
        'Flow_Volume::CENTILITER_PER_MINUTE'       => array(array('' => 0.00001, '/' => 60),            'cl/m'),
        'Flow_Volume::CENTILITER_PER_SECOND'       => array(0.00001,                                    'cl/s'),
        'Flow_Volume::CUBEM_PER_DAY'               => array(array('' => 4168181830, '/' => 86400),      'cubem/day'),
        'Flow_Volume::CUBEM_PER_HOUR'              => array(array('' => 4168181830, '/' => 3600),       'cubem/h'),
        'Flow_Volume::CUBEM_PER_MINUTE'            => array(array('' => 4168181830, '/' => 60),         'cubem/m'),
        'Flow_Volume::CUBEM_PER_SECOND'            => array(4168181830,                                 'cubem/s'),
        'Flow_Volume::CUBIC_CENTIMETER_PER_DAY'    => array(array('' => 0.000001, '/' => 86400),        'cm³/day'),
        'Flow_Volume::CUBIC_CENTIMETER_PER_HOUR'   => array(array('' => 0.000001, '/' => 3600),         'cm³/h'),
        'Flow_Volume::CUBIC_CENTIMETER_PER_MINUTE' => array(array('' => 0.000001, '/' => 60),           'cm³/m'),
        'Flow_Volume::CUBIC_CENTIMETER_PER_SECOND' => array(0.000001,                                   'cm³/s'),
        'Flow_Volume::CUBIC_DECIMETER_PER_DAY'     => array(array('' => 0.001, '/' => 86400),           'dm³/day'),
        'Flow_Volume::CUBIC_DECIMETER_PER_HOUR'    => array(array('' => 0.001, '/' => 3600),            'dm³/h'),
        'Flow_Volume::CUBIC_DECIMETER_PER_MINUTE'  => array(array('' => 0.001, '/' => 60),              'dm³/m'),
        'Flow_Volume::CUBIC_DECIMETER_PER_SECOND'  => array(0.001,                                      'dm³/s'),
        'Flow_Volume::CUBIC_DEKAMETER_PER_DAY'     => array(array('' => 1000, '/' => 86400),            'dam³/day'),
        'Flow_Volume::CUBIC_DEKAMETER_PER_HOUR'    => array(array('' => 1000, '/' => 3600),             'dam³/h'),
        'Flow_Volume::CUBIC_DEKAMETER_PER_MINUTE'  => array(array('' => 1000, '/' => 60),               'dam³/m'),
        'Flow_Volume::CUBIC_DEKAMETER_PER_SECOND'  => array(1000,                                       'dam³/s'),
        'Flow_Volume::CUBIC_FOOT_PER_DAY'          => array(array('' => 0.028316847, '/' => 86400),     'ft³/day'),
        'Flow_Volume::CUBIC_FOOT_PER_HOUR'         => array(array('' => 0.028316847, '/' => 3600),      'ft³/h'),
        'Flow_Volume::CUBIC_FOOT_PER_MINUTE'       => array(array('' => 0.028316847, '/' => 60),        'ft³/m'),
        'Flow_Volume::CUBIC_FOOT_PER_SECOND'       => array(0.028316847,                                'ft³/s'),
        'Flow_Volume::CUBIC_INCH_PER_DAY'          => array(array('' => 0.028316847, '/' => 149299200), 'in³/day'),
        'Flow_Volume::CUBIC_INCH_PER_HOUR'         => array(array('' => 0.028316847, '/' => 6220800),   'in³/h'),
        'Flow_Volume::CUBIC_INCH_PER_MINUTE'       => array(array('' => 0.028316847, '/' => 103680),    'in³/m'),
        'Flow_Volume::CUBIC_INCH_PER_SECOND'       => array(0.028316847,                                'in³/s'),
        'Flow_Volume::CUBIC_KILOMETER_PER_DAY'     => array(array('' => 1000000000, '/' => 86400),      'km³/day'),
        'Flow_Volume::CUBIC_KILOMETER_PER_HOUR'    => array(array('' => 1000000000, '/' => 3600),       'km³/h'),
        'Flow_Volume::CUBIC_KILOMETER_PER_MINUTE'  => array(array('' => 1000000000, '/' => 60),         'km³/m'),
        'Flow_Volume::CUBIC_KILOMETER_PER_SECOND'  => array(1000000000,                                 'km³/s'),
        'Flow_Volume::CUBIC_METER_PER_DAY'         => array(array('' => 1, '/' => 86400),               'm³/day'),
        'Flow_Volume::CUBIC_METER_PER_HOUR'        => array(array('' => 1, '/' => 3600),                'm³/h'),
        'Flow_Volume::CUBIC_METER_PER_MINUTE'      => array(array('' => 1, '/' => 60),                  'm³/m'),
        'Flow_Volume::CUBIC_METER_PER_SECOND'      => array(1,                                          'm³/s'),
        'Flow_Volume::CUBIC_MILE_PER_DAY'          => array(array('' => 4168181830, '/' => 86400),      'mi³/day'),
        'Flow_Volume::CUBIC_MILE_PER_HOUR'         => array(array('' => 4168181830, '/' => 3600),       'mi³/h'),
        'Flow_Volume::CUBIC_MILE_PER_MINUTE'       => array(array('' => 4168181830, '/' => 60),         'mi³/m'),
        'Flow_Volume::CUBIC_MILE_PER_SECOND'       => array(4168181830,                                 'mi³/s'),
        'Flow_Volume::CUBIC_MILLIMETER_PER_DAY'    => array(array('' => 0.000000001, '/' => 86400),     'mm³/day'),
        'Flow_Volume::CUBIC_MILLIMETER_PER_HOUR'   => array(array('' => 0.000000001, '/' => 3600),      'mm³/h'),
        'Flow_Volume::CUBIC_MILLIMETER_PER_MINUTE' => array(array('' => 0.000000001, '/' => 60),        'mm³/m'),
        'Flow_Volume::CUBIC_MILLIMETER_PER_SECOND' => array(0.000000001,                                'mm³/s'),
        'Flow_Volume::CUBIC_YARD_PER_DAY'          => array(array('' => 0.764554869, '/' => 86400),     'yd³/day'),
        'Flow_Volume::CUBIC_YARD_PER_HOUR'         => array(array('' => 0.764554869, '/' => 3600),      'yd³/h'),
        'Flow_Volume::CUBIC_YARD_PER_MINUTE'       => array(array('' => 0.764554869, '/' => 60),        'yd³/m'),
        'Flow_Volume::CUBIC_YARD_PER_SECOND'       => array(0.764554869,                                'yd³/s'),
        'Flow_Volume::CUSEC'                       => array(0.028316847,                                'cusec'),
        'Flow_Volume::DECILITER_PER_DAY'           => array(array('' => 0.0001, '/' => 86400),          'dl/day'),
        'Flow_Volume::DECILITER_PER_HOUR'          => array(array('' => 0.0001, '/' => 3600),           'dl/h'),
        'Flow_Volume::DECILITER_PER_MINUTE'        => array(array('' => 0.0001, '/' => 60),             'dl/m'),
        'Flow_Volume::DECILITER_PER_SECOND'        => array(0.0001,                                     'dl/s'),
        'Flow_Volume::DEKALITER_PER_DAY'           => array(array('' => 0.01, '/' => 86400),            'dal/day'),
        'Flow_Volume::DEKALITER_PER_HOUR'          => array(array('' => 0.01, '/' => 3600),             'dal/h'),
        'Flow_Volume::DEKALITER_PER_MINUTE'        => array(array('' => 0.01, '/' => 60),               'dal/m'),
        'Flow_Volume::DEKALITER_PER_SECOND'        => array(0.01,                                       'dal/s'),
        'Flow_Volume::GALLON_PER_DAY'              => array(array('' => 0.00454609, '/' => 86400),      'gal/day'),
        'Flow_Volume::GALLON_PER_HOUR'             => array(array('' => 0.00454609, '/' => 3600),       'gal/h'),
        'Flow_Volume::GALLON_PER_MINUTE'           => array(array('' => 0.00454609, '/' => 60),         'gal/m'),
        'Flow_Volume::GALLON_PER_SECOND'           => array(0.00454609,                                 'gal/s'),
        'Flow_Volume::GALLON_US_PER_DAY'           => array(array('' => 0.0037854118, '/' => 86400),    'gal/day'),
        'Flow_Volume::GALLON_US_PER_HOUR'          => array(array('' => 0.0037854118, '/' => 3600),     'gal/h'),
        'Flow_Volume::GALLON_US_PER_MINUTE'        => array(array('' => 0.0037854118, '/' => 60),       'gal/m'),
        'Flow_Volume::GALLON_US_PER_SECOND'        => array(0.0037854118,                               'gal/s'),
        'Flow_Volume::HECTARE_METER_PER_DAY'       => array(array('' => 10000, '/' => 86400),           'ha m/day'),
        'Flow_Volume::HECTARE_METER_PER_HOUR'      => array(array('' => 10000, '/' => 3600),            'ha m/h'),
        'Flow_Volume::HECTARE_METER_PER_MINUTE'    => array(array('' => 10000, '/' => 60),              'ha m/m'),
        'Flow_Volume::HECTARE_METER_PER_SECOND'    => array(10000,                                      'ha m/s'),
        'Flow_Volume::HECTOLITER_PER_DAY'          => array(array('' => 0.1, '/' => 86400),             'hl/day'),
        'Flow_Volume::HECTOLITER_PER_HOUR'         => array(array('' => 0.1, '/' => 3600),              'hl/h'),
        'Flow_Volume::HECTOLITER_PER_MINUTE'       => array(array('' => 0.1, '/' => 60),                'hl/m'),
        'Flow_Volume::HECTOLITER_PER_SECOND'       => array(0.1,                                        'hl/s'),
        'Flow_Volume::KILOLITER_PER_DAY'           => array(array('' => 1, '/' => 86400),               'kl/day'),
        'Flow_Volume::KILOLITER_PER_HOUR'          => array(array('' => 1, '/' => 3600),                'kl/h'),
        'Flow_Volume::KILOLITER_PER_MINUTE'        => array(array('' => 1, '/' => 60),                  'kl/m'),
        'Flow_Volume::KILOLITER_PER_SECOND'        => array(1,                                          'kl/s'),
        'Flow_Volume::LAMBDA_PER_DAY'              => array(array('' => 0.000000001, '/' => 86400),     'λ/day'),
        'Flow_Volume::LAMBDA_PER_HOUR'             => array(array('' => 0.000000001, '/' => 3600),      'λ/h'),
        'Flow_Volume::LAMBDA_PER_MINUTE'           => array(array('' => 0.000000001, '/' => 60),        'λ/m'),
        'Flow_Volume::LAMBDA_PER_SECOND'           => array(0.000000001,                                'λ/s'),
        'Flow_Volume::LITER_PER_DAY'               => array(array('' => 0.001, '/' => 86400),           'l/day'),
        'Flow_Volume::LITER_PER_HOUR'              => array(array('' => 0.001, '/' => 3600),            'l/h'),
        'Flow_Volume::LITER_PER_MINUTE'            => array(array('' => 0.001, '/' => 60),              'l/m'),
        'Flow_Volume::LITER_PER_SECOND'            => array(0.001,                                      'l/s'),
        'Flow_Volume::MILLILITER_PER_DAY'          => array(array('' => 0.000001, '/' => 86400),        'ml/day'),
        'Flow_Volume::MILLILITER_PER_HOUR'         => array(array('' => 0.000001, '/' => 3600),         'ml/h'),
        'Flow_Volume::MILLILITER_PER_MINUTE'       => array(array('' => 0.000001, '/' => 60),           'ml/m'),
        'Flow_Volume::MILLILITER_PER_SECOND'       => array(0.000001,                                   'ml/s'),
        'Flow_Volume::MILLION_ACRE_FOOT_PER_DAY'   => array(array('' => 1233481840, '/' => 86400),      'million ac ft/day'),
        'Flow_Volume::MILLION_ACRE_FOOT_PER_HOUR'  => array(array('' => 1233481840, '/' => 3600),       'million ac ft/h'),
        'Flow_Volume::MILLION_ACRE_FOOT_PER_MINUTE'  => array(array('' => 1233481840, '/' => 60),       'million ac ft/m'),
        'Flow_Volume::MILLION_ACRE_FOOT_PER_SECOND'  => array(1233481840,                               'million ac ft/s'),
        'Flow_Volume::MILLION_CUBIC_FOOT_PER_DAY'    => array(array('' => 28316.847, '/' => 86400),     'million ft³/day'),
        'Flow_Volume::MILLION_CUBIC_FOOT_PER_HOUR'   => array(array('' => 28316.847, '/' => 3600),      'million ft³/h'),
        'Flow_Volume::MILLION_CUBIC_FOOT_PER_MINUTE' => array(array('' => 28316.847, '/' => 60),        'million ft³/m'),
        'Flow_Volume::MILLION_CUBIC_FOOT_PER_SECOND' => array(28316.847,                                'million ft³/s'),
        'Flow_Volume::MILLION_GALLON_PER_DAY'      => array(array('' => 4546.09, '/' => 86400),         'million gal/day'),
        'Flow_Volume::MILLION_GALLON_PER_HOUR'     => array(array('' => 4546.09, '/' => 3600),          'million gal/h'),
        'Flow_Volume::MILLION_GALLON_PER_MINUTE'   => array(array('' => 4546.09, '/' => 60),            'million gal/m'),
        'Flow_Volume::MILLION_GALLON_PER_SECOND'   => array(4546.09,                                    'million gal/s'),
        'Flow_Volume::MILLION_GALLON_US_PER_DAY'   => array(array('' => 3785.4118, '/' => 86400),       'million gal/day'),
        'Flow_Volume::MILLION_GALLON_US_PER_HOUR'  => array(array('' => 3785.4118, '/' => 3600),        'million gal/h'),
        'Flow_Volume::MILLION_GALLON_US_PER_MINUTE'=> array(array('' => 3785.4118, '/' => 60),          'million gal/m'),
        'Flow_Volume::MILLION_GALLON_US_PER_SECOND'=> array(3785.4118,                                  'million gal/s'),
        'Flow_Volume::MINERS_INCH_AZ'              => array(array('' => 0.0424752705, '/' => 60),       "miner's inch"),
        'Flow_Volume::MINERS_INCH_CA'              => array(array('' => 0.0424752705, '/' => 60),       "miner's inch"),
        'Flow_Volume::MINERS_INCH_OR'              => array(array('' => 0.0424752705, '/' => 60),       "miner's inch"),
        'Flow_Volume::MINERS_INCH_CO'              => array(array('' => 0.0442450734375, '/' => 60),    "miner's inch"),
        'Flow_Volume::MINERS_INCH_ID'              => array(array('' => 0.0340687062, '/' => 60),       "miner's inch"),
        'Flow_Volume::MINERS_INCH_WA'              => array(array('' => 0.0340687062, '/' => 60),       "miner's inch"),
        'Flow_Volume::MINERS_INCH_NM'              => array(array('' => 0.0340687062, '/' => 60),       "miner's inch"),
        'Flow_Volume::OUNCE_PER_DAY'               => array(array('' => 0.00454609, '/' => 13824000),   'oz/day'),
        'Flow_Volume::OUNCE_PER_HOUR'              => array(array('' => 0.00454609, '/' => 576000),     'oz/h'),
        'Flow_Volume::OUNCE_PER_MINUTE'            => array(array('' => 0.00454609, '/' => 9600),       'oz/m'),
        'Flow_Volume::OUNCE_PER_SECOND'            => array(array('' => 0.00454609, '/' => 160),        'oz/s'),
        'Flow_Volume::OUNCE_US_PER_DAY'            => array(array('' => 0.0037854118, '/' => 11059200), 'oz/day'),
        'Flow_Volume::OUNCE_US_PER_HOUR'           => array(array('' => 0.0037854118, '/' => 460800),   'oz/h'),
        'Flow_Volume::OUNCE_US_PER_MINUTE'         => array(array('' => 0.0037854118, '/' => 7680),     'oz/m'),
        'Flow_Volume::OUNCE_US_PER_SECOND'         => array(array('' => 0.0037854118, '/' => 128),      'oz/s'),
        'Flow_Volume::PETROGRAD_STANDARD_PER_DAY'  => array(array('' => 4.672279755, '/' => 86400),     'petrograd standard/day'),
        'Flow_Volume::PETROGRAD_STANDARD_PER_HOUR' => array(array('' => 4.672279755, '/' => 3600),      'petrograd standard/h'),
        'Flow_Volume::PETROGRAD_STANDARD_PER_MINUTE' => array(array('' => 4.672279755, '/' => 60),      'petrograd standard/m'),
        'Flow_Volume::PETROGRAD_STANDARD_PER_SECOND' => array(4.672279755,                              'petrograd standard/s'),
        'Flow_Volume::STERE_PER_DAY'               => array(array('' => 1, '/' => 86400),               'st/day'),
        'Flow_Volume::STERE_PER_HOUR'              => array(array('' => 1, '/' => 3600),                'st/h'),
        'Flow_Volume::STERE_PER_MINUTE'            => array(array('' => 1, '/' => 60),                  'st/m'),
        'Flow_Volume::STERE_PER_SECOND'            => array(1,                                          'st/s'),
        'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_DAY' => array(array('' => 28.316847, '/' => 86400),       'thousand ft³/day'),
        'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_HOUR'   => array(array('' => 28.316847, '/' => 3600),     'thousand ft³/h'),
        'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_MINUTE' => array(array('' => 28.316847, '/' => 60),       'thousand ft³/m'),
        'Flow_Volume::THOUSAND_CUBIC_FOOT_PER_SECOND' => array(28.316847,                               'thousand ft³/s'),
        'Flow_Volume::TRILLION_CUBIC_FOOT_PER_DAY'    => array(array('' => 28316847000, '/' => 86400),  'trillion ft³/day'),
        'Flow_Volume::TRILLION_CUBIC_FOOT_PER_HOUR'   => array(array('' => 28316847000, '/' => 3600),   'trillion ft³/h'),
        'Flow_Volume::TRILLION_CUBIC_FOOT_PER_MINUTE' => array(array('' => 28316847000, '/' => 60),     'trillion ft³/m'),
        'Flow_Volume::TRILLION_CUBIC_FOOT_PER_'       => array(28316847000,                             'trillion ft³/s')
    );

    private $_Locale;

    /**
     * Zend_Measure_Flow_Volume provides an locale aware class for
     * conversion and formatting of flow-volume values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Flow_Volume Type
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
     * @param  $type   type   - OPTIONAL a Zend_Measure_Flow_Volume Type
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of flow-volume:' . $type);
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
            throw Zend::exception('Zend_Measure_Exception', 'unknown type of flow-volume:' . $type);
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