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
     * @todo No handling of dual seperated formats as Indian for now f.e. #,##,##0.###
     */
    public static function toNumber($value, $locale = false)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale,'decimalnumberformat');
        $format = $format['default'];

//print "\n<br>";
//print "\n<br>ORIGINAL:".$format;

        // seperate negative format pattern when avaiable 
        if (strpos($format, ';') !== false)
        {
            if (bccomp($value, 0) < 0)
                $format = substr($format, strpos($format, ';') + 1);
            else
                $format = substr($format, 0, strpos($format, ';'));
        }

//print "\n<br>CHOOSEN:".$format;
        // set negative sign
        if (bccomp($value, 0) < 0)
        {
            if (strpos($format, '-') === false)
                $format = $symbols['minus'].$format;
            else
                $format = strtr($format, '-', $symbols['minus']);
        }

//print "\n<br>NEGATIVE:".$format;
        // get number parts
//        $endtag = substr($format, strrpos($format, '#') + 1);
        if (strpos($value, '.') !== false)
            $precision = substr($value, strpos($value, '.') + 1);
        else
            $precision = '';

//print "\n<br>PRECISION:".$precision;
        // get fraction and format lengths
        bcscale(strlen($precision));
        $prec = bcsub($value, bcsub($value, '0', 0));
        if (strpos($prec, '-') !== false)
            $prec = substr($prec, 1);
        $number = bcsub($value, 0, 0);
        if (strpos($number, '-') !== false)
            $number = substr($number, 1);
        $group = strrpos($format, ',');
        $group2= strpos($format, ',');
        $point = strpos($format, '.');

//print "\n<br>PREC:".$prec;
        // Add fraction
        if ($prec == 0)
            $format = substr($format, 0, $point).substr($format, strrpos($format, '#') + 1);
        else
            $format = substr($format, 0, $point).$symbols['decimal'].substr($prec, 2).
                      substr($format, strrpos($format, '#') + 1);

//print "\n<br>APPROVED:".$format;

        // no seperation
        if ($group == 0)
        {
            $format = $number.substr($format, $point);
        } else if ($group == $group2) {
            // only 1 seperation
            $seperation = ($point - $group - 1);
//print "\n<br>LENGTH:".strlen($number);
//print "\n<br>NUMBER:".$number;
//print "\n<br>POINT:".$point;
//print "\n<br>GROUP:".$group;
//print "\n<br>GROUP2:".$group2;
//print "\n<br>SEPERATION:".$seperation;
            for ($x = (strlen($number) - $seperation); $x > ($group2 - 2); $x -= $seperation)
            {
//print "\n<br>X2:".$x;
                 $number = substr($number, 0, $x).$symbols['group'].
                           substr($number, $x);
//print "\n<br>NUMBER:".$number;
            }
            $format = substr($format, 0, strpos($format,'#')).$number.substr($format, $point);
        }
//print "\n<br>REST:".$prec;
//print "\n<br>VALUE:".$value;
//print "\n<br>NUMBER:".$number;
        
//print "\n<br>VALUE: $value FORMAT:".$format;
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
        if (!is_string($input))
            return $input;

        if (!is_int($precision) and ($locale == false))
        {
            $locale = $precision;
            $precision = false;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.$symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0]))
            self::throwException('No value in '.$input.' found');

        // Float or Integer ?
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-")
            $found = strtr($found,$symbols['minus'],'-');
        $found = str_replace($symbols['group'],"",$found);

        // Do precision
        if (strpos($found, $symbols['decimal']) !== false)
        {
            if ($symbols['decimal'] != '.')
                $found = str_replace($symbols['decimal'],".",$found);

            $pre = substr($found, strpos($found, '.') + 1);

            if (strlen($pre) > $precision)
                $found = substr($found, 0, strlen($found) - strlen($pre) + $precision);
            else if (strlen($pre) < $precision)
                $found = str_pad($found, $precision, '0');
            else
                $found = substr($found, 0, strpos($found, '.') - 1);
        }

        return floatval($found);
    }


    /**
     * 
     */
    public static function toFloat()
    {
        // @todo Implement
        self::throwException('function not implemented');
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
        if (!is_string($input))
            return $input;

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.$symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0]))
            self::throwException('No value in '.$input.' found');

        // Float or Integer ?
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-")
            $found = strtr($found,$symbols['minus'],'-');
        $found = str_replace($symbols['group'],"",$found);

        // Do precision
        if (strpos($found, $symbols['decimal']) !== false)
            $found = substr($found, 0, strpos($found, '.') - 1);

        return intval($found);
    }


    /**
     * 
     */
    public static function toInteger()
    {
        // @todo Implement
        self::throwException('function not implemented');

        if (!is_integer($value))
            return self::toFloat($value, $locale);

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale,'decimalnumberformat');
        $format = $format['default'];

        // seperate negative format pattern when avaiable 
        if (strpos($format, ';') !== false)
        {
            if ($value < 0)
                $format = substr($format, strpos($format, ';') + 1);
            else
                $format = substr($format, 0, strpos($format, ';'));
        }

        // set negative sign
        if ($value < 0)
        {
            if (strpos($format, '-') === false)
                $format = $symbols['minus'].$format;
            else
                $format = strtr($format, '-', $symbols['minus']);
        }

        // delete precision
        $precision = substr($format, strpos($format, '.') + 1);
        $format = $format.substr($precision, strrpos($precision, '#') + 1);

        $regex = '/[#0]*,{0,}/';
        preg_match_all($regex, $format, $found);

print_r($found);
print $format."\n<br>";
        $seperate = substr($format,strpos($format,',')+1);
        $seperate = strlen(substr($seperate,0,strpos($seperate, '.')));
        
        $length = strlen($value);
        
//                    <pattern>#,##0.###</pattern>
//                    <!-- number grouping same as India (thousands, lakhs, crores, etc.) -->
//                    <pattern draft="true">#,##,##0.###</pattern>

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
     * Returns the normalized date from an locale date
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

        // Get correct date for this locale
        $format = Zend_Locale_Data::getContent($locale,'dateformat', array('gregorian', $type));
print $format['pattern']."\n<br>";
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


    public static function toDate()
    {
        // @todo Implement
        self::throwException('function not implemented');
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


    public static function toTime()
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
        require_once('Zend/Locale/Exception.php');
        throw new Zend_Locale_Exception($message);
    }
}