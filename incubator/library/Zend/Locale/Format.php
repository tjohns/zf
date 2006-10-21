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
 * @package    Zend_Locale
 * @subpackage Format
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Format
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale_Format
{
    /**
     * Returns the first found number from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456.1234
     * '+23,3452.123' = 233452.123
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+'
     * 
     * @param $input  - string
     * @param $locale - OPTIONAL locale 
     * @return string
     */
    public static function getNumber($input, $locale = false)
    {
        if (!is_string($input))
            return $input;

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.
                      $symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0]))
            self::throwException('No value in '.$input.' found');
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-")
            $found = strtr($found,$symbols['minus'],'-');
        $found = str_replace($symbols['group'],"",$found);

        if ($symbols['decimal'] != '.')
            $found = str_replace($symbols['decimal'],".",$found);

        return $found;
    }


    /**
     * Returns a locale formatted number
     * 
     * @param $value  - number to localize
     * @param $locale - OPTIONAL locale
     * @return string - locale formatted number
     * 
     * @todo UTF8 not handled properly... waiting for Zend_Locale_UTF8
     */
    public static function toNumber($value, $locale = false)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale, 'decimalnumberformat');
        $format = $format['default'];
        iconv_set_encoding('internal_encoding', 'UTF-8');
        
        // seperate negative format pattern when avaiable 
        if (iconv_strpos($format, ';') !== false) {
            if (bccomp($value, 0) < 0) {
                $format = iconv_substr($format, iconv_strpos($format, ';') + 1);
            } else {
                $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
            }
        }
        
        // set negative sign
        if (bccomp($value, 0) < 0) {
            if (iconv_strpos($format, '-') === false) {
                $format = $symbols['minus'] . $format;
            } else {
                $format = str_replace('-', $symbols['minus'], $format);
            }
        }
        
        // get number parts
        if (iconv_strpos($value, '.') !== false) {
            $precision = iconv_substr($value, iconv_strpos($value, '.') + 1);
        } else {
            $precision = '';
        }

        // get fraction and format lengths
        bcscale(iconv_strlen($precision));
        $prec = bcsub($value, bcsub($value, '0', 0));
        if (iconv_strpos($prec, '-') !== false) {
            $prec = iconv_substr($prec, 1);
        }
        $number = bcsub($value, 0, 0);
        if (iconv_strpos($number, '-') !== false) {
            $number = substr($number, 1);
        }
        $group  = iconv_strrpos($format, ',');
        $group2 = iconv_strpos ($format, ',');
        $point  = iconv_strpos ($format, '.');

        // Add fraction
        if ($prec == 0) {
            $format = iconv_substr($format, 0, $point) . iconv_substr($format, iconv_strrpos($format, '#') + 1);
        } else {
            $format = iconv_substr($format, 0, $point) . $symbols['decimal'] . iconv_substr($prec, 2).
                      iconv_substr($format, iconv_strrpos($format, '#') + 1);
        }
        
        // Add seperation
        if ($group == 0) {

            // no seperation
            $format = $number . iconv_substr($format, $point);

        } else if ($group == $group2) {

            // only 1 seperation
            $seperation = ($point - $group - 1);
            for ($x = iconv_strlen($number); $x > $group2; $x -= $seperation) {
                 $number = iconv_substr($number, 0, $x - $seperation) . $symbols['group']
                         . iconv_substr($number, $x - $seperation);
            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
            
        } else {

            // 2 seperations
            if (iconv_strlen($number) > ($point - $group - 1)) { 
                $seperation = ($point - $group - 1);
                $number = iconv_substr($number, 0, iconv_strlen($number) - $seperation) . $symbols['group']
                        . iconv_substr($number, iconv_strlen($number) - $seperation);

                if ((iconv_strlen($number) - 1) > ($point - $group)) {
                    $seperation2 = ($group - $group2 - 1);
                    
                    for ($x = iconv_strlen($number) - $seperation2 - 2; $x > $seperation2; $x -= $seperation2) {
                         $number = iconv_substr($number, 0, $x - $seperation2) . $symbols['group']
                                 . iconv_substr($number, $x - $seperation2);
                    }
                }

            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);

        }
        return (string) $format;        
    }


    /**
     * Returns if a number was found
     * 
     * @param  $input  - localized number string
     * @param  $locale - OPTIONAL locale
     * @return boolean
     */
    public static function isNumber($input, $locale = false)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.$symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $found);

        if (!isset($found[0]))
            return false;
        return true;
    }


    /**
     * Returns the first found float from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456.1234
     * '+23,3452.123' = 233452.123
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+'
     * 
     * @param $input     - string
     * @param $locale    - OPTIONAL locale 
     * @param $precision - OPTIONAL precision of float value
     * @return float
     */
    public static function getFloat($input, $precision = false, $locale = false)
    {
        if (!is_string($input)) {
            return $input;
        }

        if (!is_int($precision) and ($locale == false)) {
            $locale    = $precision;
            $precision = false;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');

        // Parse input locale aware
        $regex = '/(' . $symbols['minus'] . '){0,1}(\d+(\\' . $symbols['group']
               . '){0,1})*(\\' . $symbols['decimal'] . '){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0])) {
            self::throwException('No value in ' . $input . ' found');
        }

        // Float or Integer ?
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-") {
            $found = strtr($found, $symbols['minus'], '-');
        }
        $found = str_replace($symbols['group'], '', $found);

        // Do precision
        if (strpos($found, $symbols['decimal']) !== false) {
            if ($symbols['decimal'] != '.') {
                $found = str_replace($symbols['decimal'], ".", $found);
            }

            $pre = substr($found, strpos($found, '.') + 1);
            if ($precision == false) {
                $precision = strlen($pre);
            }

            if (strlen($pre) >= $precision) {
                $found = substr($found, 0, strlen($found) - strlen($pre) + $precision);
            }
        }
        
        return floatval($found);
    }


    /**
     * Returns a locale formatted integer number
     * 
     * @param $value  - number to localize
     * @param $locale - OPTIONAL locale
     * @return string - locale formatted number
     * 
     * @todo UTF8 not handled properly... waiting for Zend_Locale_UTF8
     */
    public static function toFloat($value, $precision, $locale = false)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale, 'decimalnumberformat');
        $format = $format['default'];

        // seperate negative format pattern when avaiable 
        if (strpos($format, ';') !== false) {
            if (bccomp($value, 0) < 0) {
                $format = substr($format, strpos($format, ';') + 1);
            } else {
                $format = substr($format, 0, strpos($format, ';'));
            }
        }

        // set negative sign
        if (bccomp($value, 0) < 0) {
            if (strpos($format, '-') === false) {
                $format = $symbols['minus'] . $format;
            } else {
                $format = strtr($format, '-', $symbols['minus']);
            }
        }

        // get number parts
        if ($precision < 0) {
            if (strpos($value, '.') !== false) {
                $precision = substr($value, strpos($value, '.') + 1);
            } else {
                $precision = '';
            }
        }

        // get fraction and format lengths
        bcscale(strlen($precision));
        $prec = bcsub($value, bcsub($value, '0', 0));
        if (strpos($prec, '-') !== false) {
            $prec = substr($prec, 1);
        }
        $number = bcsub($value, 0, 0);
        if (strpos($number, '-') !== false) {
            $number = substr($number, 1);
        }
        $group  = strrpos($format, ',');
        $group2 = strpos ($format, ',');
        $point  = strpos ($format, '.');

        // Add fraction
        if ($prec == 0) {
            $format = substr($format, 0, $point) . substr($format, strrpos($format, '#') + 1);
        } else {
            $format = substr($format, 0, $point) . $symbols['decimal'] . substr($prec, 2).
                      substr($format, strrpos($format, '#') + 1);
        }

        // Add seperation
        if ($group == 0) {

            // no seperation
            $format = $number . substr($format, $point);

        } else if ($group == $group2) {

            // only 1 seperation
            $seperation = ($point - $group - 1);
            for ($x = (strlen($number) - $seperation); $x > ($group2 - 2); $x -= $seperation) {
                 $number = substr($number, 0, $x) . $symbols['group']
                         . substr($number, $x);
            }
            $format = substr($format, 0, strpos($format, '#')) . $number . substr($format, $point);

        } else {

            // 2 seperations
            if (strlen($number) > ($point - $group - 1)) { 
                $seperation = ($point - $group - 1);
                $number = substr($number, 0, strlen($number) - $seperation) . $symbols['group']
                        . substr($number, strlen($number) - $seperation);

                if ((strlen($number) - 1) > ($point - $group)) {
                    $seperation2 = ($group - $group2 - 1);
                    for ($x = (strlen($number) - $seperation - $seperation2 - 1); $x > ($group2 - 2); $x -= $seperation2) {
                         $number = substr($number, 0, $x) . $symbols['group']
                                 . substr($number, $x);
                    }
                }

            }
            $format = substr($format, 0, strpos($format, '#')) . $number . substr($format, $point);

        }
        return (string) $format;        
    }


    /**
     * Returns if a float was found
     * Alias for isNumber()
     * 
     * @param  $input  - localized number string
     * @param  $locale - OPTIONAL locale
     * @return boolean
     */
    public static function isFloat($value, $locale = false)
    {
        return self::isNumber($value, $locale);
    }


    /**
     * Returns the first found integer from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456
     * '+23,3452.123' = 233452
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+'
     * 
     * @param $input     - string
     * @param $locale    - OPTIONAL locale 
     * @return float
     */
    public static function getInteger($input, $locale = false)
    {
        if (!is_string($input)) {
            return $input;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');

        // Parse input locale aware
        $regex = '/(' . $symbols['minus'] . '){0,1}(\d+(\\' . $symbols['group']
               . '){0,1})*(\\' . $symbols['decimal'] . '){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0])) {
            self::throwException('No value in ' . $input . ' found');
        }

        // Float or Integer ?
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-") {
            $found = strtr($found, $symbols['minus'], '-');
        }
        $found = str_replace($symbols['group'], "", $found);

        // Do precision
        if (strpos($found, $symbols['decimal']) !== false) {
            $found = substr($found, 0, strpos($found, '.') - 1);
        }

        return intval($found);
    }


    /**
     * Returns a localized number
     * 
     * @param $value  - number to localize
     * @param $locale - OPTIONAL locale
     * @return string - locale formatted number
     * 
     * @todo UTF8 not handled properly... waiting for Zend_Locale_UTF8
     */
    public static function toInteger($value, $locale = false)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale, 'decimalnumberformat');
        $format = $format['default'];

        // seperate negative format pattern when avaiable 
        if (strpos($format, ';') !== false) {
            if (bccomp($value, 0) < 0) {
                $format = substr($format, strpos($format, ';') + 1);
            } else {
                $format = substr($format, 0, strpos($format, ';'));
            }
        }

        // set negative sign
        if (bccomp($value, 0) < 0) {
            if (strpos($format, '-') === false) {
                $format = $symbols['minus'] . $format;
            } else {
                $format = strtr($format, '-', $symbols['minus']);
            }
        }

        // get integer part
        $number = bcsub($value, 0, 0);
        if (strpos($number, '-') !== false) {
            $number = substr($number, 1);
        }
        $group  = strrpos($format, ',');
        $group2 = strpos ($format, ',');
        $point  = strpos ($format, '.');
        $format = substr($format, 0, $point) . substr($format, strrpos($format, '#') + 1);

        // Add seperation
        if ($group == 0) {

            // no seperation
            $format = $number . substr($format, $point);

        } else if ($group == $group2) {

            // only 1 seperation
            $seperation = ($point - $group - 1);
            for ($x = (strlen($number) - $seperation); $x > ($group2 - 2); $x -= $seperation) {
                 $number = substr($number, 0, $x) . $symbols['group']
                         . substr($number, $x);
            }
            $format = substr($format, 0, strpos($format, '#')) . $number . substr($format, $point);

        } else {

            // 2 seperations
            if (strlen($number) > ($point - $group - 1)) { 
                $seperation = ($point - $group - 1);
                $number = substr($number, 0, strlen($number) - $seperation) . $symbols['group']
                        . substr($number, strlen($number) - $seperation);

                if ((strlen($number) - 1) > ($point - $group)) {
                    $seperation2 = ($group - $group2 - 1);
                    for ($x = (strlen($number) - $seperation - $seperation2 - 1); $x > ($group2 - 2); $x -= $seperation2) {
                         $number = substr($number, 0, $x) . $symbols['group']
                                 . substr($number, $x);
                    }
                }

            }
            $format = substr($format, 0, strpos($format, '#')) . $number . substr($format, $point);

        }
        return (string) $format;        
    }


    /**
     * Returns if a integer was found
     * 
     * @param  $input  - localized number string
     * @param  $locale - OPTIONAL locale
     * @return boolean
     */
    public static function isInteger($value, $locale = false)
    {
        return self::isNumber($value, $locale);
    }


    /**
     * Returns an array with the normalized date from an locale date
     * a input of 10.01.2006 for locale 'de' would return
     * array ('day' => 10, 'month' => 1, 'year' => 2006)
     *
     * @param $date   - string : date string
     * @param $type   - string : type of date (full, long, short...)
     * @param $locale - locale : locale for date normalization
     * @return array
     */
    public static function getDate($date, $type, $locale)
    {
        // @todo Implement
        self::throwException('function not implemented');

        if ($type == 'default') {
            $type = Zend_Locale_Date::getContent($locale, 'defdateformat', 'gregorian');
            $type = $type['pattern'];
        }

        // Get correct date for this locale
        $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $type));

        // Parse input locale aware
        $pattern = str_split($format['pattern']);
        $last = 0;
        $step = 0;
        $format[0] = $pattern[0];
        for ($key = 1; $key < count($pattern); ++$key)
        {
            if ($pattern[$key] == $pattern[$last])
            {
                $format[$step] .= $pattern[$key];
            } else {
                ++$step;
                $format[$step] = $pattern[$key];
            }
            $last = $key;
        }

        unset($format['pattern']);

        foreach($format as $found)
        {
            switch($found)
            {
                // Era
                case 'G'    :
                case 'GG'   :
                case 'GGG'  :
                case 'GGGG' :
                case 'GGGGG':
                    break;

                // Quarter
                case 'Q'    :
                case 'QQ'   :
                case 'QQQ'  :
                case 'QQQQ' :
                // Quarter standalone
                case 'q'    :
                case 'qq'   :
                case 'qqq'  :
                case 'qqqq' :

                // Month
                case 'M'    :
                case 'MM'   :
                case 'MMM'  :
                case 'MMMM' :
                case 'MMMMM':

                // Month standalone
                case 'm'    :
                case 'mm'   :
                case 'mmm'  :
                case 'mmmm' :
                case 'mmmmm':

                // week of year
                case 'w'    :
                case 'ww'   :
                
                // week of month
                case 'W'    :

                // day of month
                case 'd'    :
                case 'dd'   :
            }
            
            if ($found[0] == 'y')
            {}
        }        
print_r($format);
        $regex = '/[GyYuQqMLwWdDFgEecahHKkmsSAzZv]*/';
        $found = preg_match_all($regex, $format['pattern'], $match);

print_r($found);
print_r($match);
    }


    public static function isDate()
    {
        // @todo Implement
        self::throwException('function not implemented');
    }


    public static function getTime()
    {
        // @todo Implement
        self::throwException('function not implemented');
    }


    public static function isTime()
    {
        // @todo Implement 
        self::throwException('function not implemented');
    }


    /**
     * Throw an exception
     *
     * Note : for performance reasons, the "load" of Zend/Measure/Exception is dynamic
     */
    public static function throwException($message)
    {
        // For performance reasons, we use this dynamic inclusion
        require_once 'Zend/Locale/Exception.php';
        throw new Zend_Locale_Exception($message);
    }
}