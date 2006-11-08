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
     * @param $precision - OPTIONAL precision of float value
     * @return string
     */
    public static function getNumber($input, $precision = false, $locale = false)
    {
        if (!is_string($input))
            return $input;

        if (!is_int($precision) and ($locale == false)) {
            $locale    = $precision;
            $precision = false;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/(' . $symbols['minus'] . '){0,1}(\d+(\\' . $symbols['group'] . '){0,1})*(\\' .
                        $symbols['decimal'] . '){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0]))
            self::throwException('No value in ' . $input . ' found');
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-")
            $found = strtr($found,$symbols['minus'],'-');
        $found = str_replace($symbols['group'],'', $found);

        // Do precision
        if (strpos($found, $symbols['decimal']) !== false) {
            if ($symbols['decimal'] != '.') {
                $found = str_replace($symbols['decimal'], ".", $found);
            }

            $pre = substr($found, strpos($found, '.') + 1);
            if ($precision === false) {
                $precision = strlen($pre);
            }

            if (strlen($pre) >= $precision) {
                $found = substr($found, 0, strlen($found) - strlen($pre) + $precision);
            }
        }

        return $found;
    }


    /**
     * Returns a locale formatted number
     * 
     * @param $value     - number to localize
     * @param $precision - OPTIONAL precision
     * @param $locale    - OPTIONAL locale
     * @return string    - locale formatted number
     */
    public static function toNumber($value, $precision = false, $locale = false)
    {
        if (!is_int($precision) and ($locale == false)) {
            $locale    = $precision;
            $precision = false;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale, 'decimalnumberformat');
        $format  = $format['default'];
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
            if ($precision === false) {
                $precision = iconv_substr($value, iconv_strpos($value, '.') + 1);
            } else {
                $precision = iconv_substr($value, iconv_strpos($value, '.') + 1, $precision);
            }
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
            $number = iconv_substr($number, 1);
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
     * Alias for getNumber
     * 
     * @param $input     - string
     * @param $locale    - OPTIONAL locale 
     * @param $precision - OPTIONAL precision of float value
     * @return float
     */
    public static function getFloat($input, $precision = false, $locale = false)
    {
        return floatval(self::getNumber($input, $precision, $locale));
    }


    /**
     * Returns a locale formatted integer number
     * Alias for toNumber()
     * 
     * @param $value  - number to localize
     * @param $precision - OPTIONAL precision
     * @param $locale - OPTIONAL locale
     * @return string - locale formatted number
     */
    public static function toFloat($value, $precision = false, $locale = false)
    {
        return self::toNumber($value, $precision, $locale);
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
        return intval(self::getFloat($input, 0, $locale));
    }


    /**
     * Returns a localized number
     * 
     * @param $value  - number to localize
     * @param $locale - OPTIONAL locale
     * @return string - locale formatted number
     */
    public static function toInteger($value, $locale = false)
    {
        return self::toNumber($value, 0, $locale);
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
     * Parse a date with a defined format
     * 
     * @param string $date - date string
     * @param string $type - date type CLDR format !!
     * @param locale $locale
     * @return mixed
     */
    private static function _parseDate($date, $format, $locale)
    {
        $found = array();

        // get position
        $day       = iconv_strpos($format, 'd');
        $month     = iconv_strpos($format, 'M');
        $year      = iconv_strpos($format, 'y');

        // search month strings
        if (substr($format, $month, 3) == 'MMM') {
            if (substr($format, $month, 4) == 'MMMM') {
                // search full month name
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'full'));
            } else {
                // search abbreviated month name
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
            }
            foreach ($monthlist as $monthnr => $month) {
                if (iconv_strpos($date, $month) !== false) {
                    $found['month'] = $monthnr;
                }
            }
        }

        // search first number part
        $first = self::getInteger($date, 0, $locale);

        if (($day < $month) && ($day < $year)) {
            // dd MM yy , dd yy MM
            $format['day'] = iconv_substr($first, 0, 2);
        } else if (($year < $day) && ($year < $month)) {
            // yy dd MM , yy MM dd
            if (substr($format, $year, 4) === 'yyyy') {
                // yyyy - 4 digits needed
                $format['year'] = iconv_substr($first, 0, 4);
            } else {
                // yy - only 2 digits needed
                $format['year'] = iconv_substr($first, 0, 2);
            }
        } else if (!isset($found['month']) && ($month < $day) && ($month < $year)) {
            // MM dd yy , MM yy dd , month as number
            $format['month'] = iconv_substr($first, 0, 2);
        }

        // truncate first found number part
        $date = substr($date, strpos($date, $first) + strlen($first));
        // search second number part
        $second = self::getInteger($date, 0, $locale);

        if (!isset($format['day']) && 
           ((($day < $month) && ($day > $year)) or
            (($day > $month) && ($day < $year)))) {
            // MM dd yy , yy dd MM
            $format['day'] = iconv_substr($second, 0, 2);
        } else if (!isset($format['year']) &&
            ((($year < $month) && ($year > $day)) or
            (($year > $month) && ($year < $day)))) {
            // MM yy dd , dd yy MM
            if (substr($format, $year, 4) === 'yyyy') {
                // yyyy - 4 digits needed
                $format['year'] = iconv_substr($second, 0, 4);
            } else {
                // yy - only 2 digits needed
                $format['year'] = iconv_substr($second, 0, 2);
            }
        } else if (!isset($format['month']) &&
            ((($month < $day) && ($month > $year)) or
             (($month > $day) && ($month < $year)))) {
            $format['month'] = iconv_substr($second, 0, 2);
        }
        
        // truncate second found number part
        $date = substr($date, strpos($date, $second) + strlen($second));
        // search third number part
        $third = self::getInteger($date, 0, $locale);

        if (!isset($format['day']) && 
            (($day > $month) && ($day > $year))) {
            // MM yy dd , yy MM dd
            $format['day'] = iconv_substr($third, 0, 2);
        } else if (!isset($format['year']) &&
            (($year > $month) && ($year > $day))) {
            // MM dd yy , dd MM yy
            if (substr($format, $year, 4) === 'yyyy') {
                // yyyy - 4 digits needed
                $format['year'] = iconv_substr($third, 0, 4);
            } else {
                // yy - only 2 digits needed
                $format['year'] = iconv_substr($third, 0, 2);
            }
        } else if (!isset($format['month']) &&
            (($month > $day) && ($month > $year))) {
            $format['month'] = iconv_substr($third, 0, 2);
        }

        if (empty($format['month']) or empty($format['day']) or !isset($format['year'])) {
            return false;
        }
        return $format;
    }


    /**
     * Returns an array with the normalized date from an locale date
     * a input of 10.01.2006 for locale 'de' would return
     * array ('day' => 10, 'month' => 1, 'year' => 2006)
     *
     * @param string $date    date string
     * @param string $format  date type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of date string
     * @return array
     */
    public static function getDate($date, $format = false, $locale = false)
    {
        if ($format === false) {
            for ($X = 1; $X <= 4; ++$X) {
                switch($X) {
                    case 1:
                        $type = 'short';
                        break;
                    case 2:
                        $type = 'medium';
                        break;
                    case 3:
                        $type = 'long';
                        break;
                    default:
                        $type = 'full';
                        break;
                }
                // Get correct date for this locale
                $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $type));
                $format = $format['pattern'];

                $date = self::_parseDate($date, $format, $locale);
                if ($date !== false) {
                    return $date;
                }
            }
        } else {
            $date = self::_parseDate($date, $format, $locale);
            if ($date !== false) {
                return $date;
            }
        }

        return false;
    }

    /**
     * Returns is the given string is a date
     *
     * @param string $date    date string
     * @param string $format  date type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of date string
     * @return boolean
     */
    public static function isDate($date, $format = false, $locale = false)
    {
        if (self::getDate($date, $format, $locale) === false) {
            return false;
        }
        return true;
    }


    /**
     * Parse a time with a defined format
     * 
     * @param string $time - time string
     * @param string $type - time type CLDR format !!
     * @param locale $locale
     * @return mixed
     */
    private static function _parseTime($time, $format, $locale)
    {
        $found = array();

        // get position
        $second    = iconv_strpos($format, 's');
        $minute    = iconv_strpos($format, 'm');
        $hour      = iconv_strpos($format, 'H');

        // search first number part
        $first = self::getInteger($time, 0, $locale);

        if (($second < $minute) && ($second < $hour)) {
            // ss mm HH , ss HH mm
            $format['second'] = iconv_substr($first, 0, 2);
        } else if (($hour < $second) && ($hour < $minute)) {
            // HH ss mm , HH mm ss
            $format['hour'] = iconv_substr($first, 0, 2);
        } else if (($minute < $second) && ($minute < $hour)) {
            // mm HH ss, mm ss HH
            $format['minute'] = iconv_substr($first, 0, 2);
        }

        // truncate first found number part
        $time = substr($time, strpos($time, $first) + strlen($first));
        // search second number part
        $sec = self::getInteger($time, 0, $locale);

        if (!isset($format['second']) && 
           ((($second < $minute) && ($second > $hour)) or
            (($second > $minute) && ($second < $hour)))) {
            // mm ss HH , HH ss mm
            $format['second'] = iconv_substr($sec, 0, 2);
        } else if (!isset($format['hour']) &&
            ((($hour < $minute) && ($hour > $second)) or
            (($hour > $minute) && ($hour < $second)))) {
            // mm HH ss , ss HH mm
            $format['hour'] = iconv_substr($sec, 0, 2);
        } else if (!isset($format['minute']) &&
            ((($minute < $second) && ($minute > $hour)) or
             (($minute > $second) && ($minute < $hour)))) {
            $format['minute'] = iconv_substr($sec, 0, 2);
        }
        
        // truncate second found number part
        $time = substr($time, strpos($time, $second) + strlen($second));
        // search third number part
        $third = self::getInteger($time, 0, $locale);

        if (!isset($format['second']) && 
            (($second > $minute) && ($second > $hour))) {
            // HH mm ss , mm HH ss
            $format['second'] = iconv_substr($third, 0, 2);
        } else if (!isset($format['hour']) &&
            (($hour > $minute) && ($hour > $second))) {
            $format['hour'] = iconv_substr($third, 0, 2);
        } else if (!isset($format['minute']) &&
            (($minute > $second) && ($minute > $hour))) {
            $format['minute'] = iconv_substr($third, 0, 2);
        }

        if (empty($format['minute']) or empty($format['second']) or !isset($format['hour'])) {
            return false;
        }
        return $format;
    }
    

    /**
     * Returns an array with the normalized time from an locale time
     * a input of 11:20:55 for locale 'de' would return
     * array ('hour' => 11, 'minute' => 20, 'second' => 55)
     *
     * @param string $time    time string
     * @param string $format  time type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of time string
     * @return array
     */
    public static function getTime($time, $format = false, $locale = false)
    {
        if ($format === false) {
            for ($X = 1; $X <= 4; ++$X) {
                switch($X) {
                    case 1:
                        $type = 'short';
                        break;
                    case 2:
                        $type = 'medium';
                        break;
                    case 3:
                        $type = 'long';
                        break;
                    default:
                        $type = 'full';
                        break;
                }
                // Get correct date for this locale
                $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', $type));
                $format = $format['pattern'];

                $time = self::_parseTime($time, $format, $locale);
                if ($time !== false) {
                    return $time;
                }
            }
        } else {
            $time = self::_parseTime($time, $format, $locale);
            if ($time !== false) {
                return $time;
            }
        }

        return false;
    }


    /**
     * Returns is the given string is a time
     *
     * @param string $time    time string
     * @param string $format  time type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of time string
     * @return boolean
     */
    public static function isTime($time, $format = false, $locale = false)
    {
        if (self::getTime($time, $format, $locale) === false) {
            return false;
        }
        return true;
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