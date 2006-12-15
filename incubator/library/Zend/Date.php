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
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Date classes
 */
require_once 'Zend.php';
require_once 'Zend/Date/DateObject.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date {

    // Class wide Date Constants

    // day formats
    const DAY            = 'Zend_Date::DAY';            // d - 2 digit day of month, 01-31
    const WEEKDAY_SHORT  = 'Zend_Date::WEEKDAY_SHORT';  // D - 3 letter day of week - locale aware, Mon-Sun
    const DAY_SHORT      = 'Zend_Date::DAY_SHORT';      // j - 1,2 digit day of month, 1-31
    const WEEKDAY        = 'Zend_Date::WEEKDAY';        // l - full day name - locale aware, Monday - Sunday
    const WEEKDAY_8601   = 'Zend_Date::WEEKDAY_8601';   // N - digit weekday ISO 8601, 1-7 1 = monday, 7=sunday
    const DAY_SUFFIX     = 'Zend_Date::DAY_SUFFIX';     // S - english suffix day of month, st-th
    const WEEKDAY_DIGIT  = 'Zend_Date::WEEKDAY_DIGIT';  // w - weekday, 0-6 0=sunday, 6=saturday
    const DAY_OF_YEAR    = 'Zend_Date::DAY_OF_YEAR';    // z - Number of day of year

    const WEEKDAY_NARROW = 'Zend_Date::WEEKDAY_NARROW'; // --- 1 letter day name - locale aware, M-S
    const WEEKDAY_NAME   = 'Zend_Date::WEEKDAY_NAME';   // --- 2 letter day name - locale aware,Mo-Su

    // week formats
    const WEEK           = 'Zend_Date::WEEK';           // W - number of week ISO8601, 1-53

    // month formats
    const MONTH          = 'Zend_Date::MONTH';          // F - full month name - locale aware, January-December
    const MONTH_SHORT    = 'Zend_Date::MONTH_SHORT';    // m - 2 digit month, 01-12
    const MONTH_NAME     = 'Zend_Date::MONTH_NAME';     // M - 3 letter monthname - locale aware, Jan-Dec
    const MONTH_DIGIT    = 'Zend_Date::MONTH_DIGIT';    // n - 1 digit month, no leading zeros, 1-12
    const MONTH_DAYS     = 'Zend_Date::MONTH_DAYS';     // t - Number of days this month

    const MONTH_NARROW   = 'Zend_Date::MONTH_NARROW';   // --- 1 letter month name - locale aware, J-D

    // year formats
    const LEAPYEAR       = 'Zend_Date::LEAPYEAR';       // L - is leapyear ?, 0-1
    const YEAR_8601      = 'Zend_Date::YEAR_8601';      // o - number of year ISO8601
    const YEAR           = 'Zend_Date::YEAR';           // Y - 4 digit year
    const YEAR_SHORT     = 'Zend_Date::YEAR_SHORT';     // y - 2 digit year, leading zeros 00-99

    const YEAR_SHORT_8601= 'Zend_Date::YEAR_SHORT_8601';// --- 2 digit number of year ISO8601

    // time formats
    const MERIDIEM       = 'Zend_Date::MERIDIEM';       // A,a - AM/PM - locale aware, AM/PM
    const SWATCH         = 'Zend_Date::SWATCH';         // B - Swatch Internet Time
    const HOUR_SHORT_AM  = 'Zend_Date::HOUR_SHORT_AM';  // g - 1 digit hour, no leading zero, 1-12 am/pm
    const HOUR_SHORT     = 'Zend_Date::HOUR_SHORT';     // G - 1 digit hour, no leading zero, 0-23
    const HOUR_AM        = 'Zend_Date::HOUR_AM';        // h - 2 digit hour, leading zeros, 01-12 am/pm
    const HOUR           = 'Zend_Date::HOUR';           // H - 2 digit hour, leading zeros, 00-23
    const MINUTE         = 'Zend_Date::MINUTE';         // i - 2 digit minute, leading zeros, 00-59
    const SECOND         = 'Zend_Date::SECOND';         // s - 2 digit second, leading zeros, 00-59
    const MILLISECOND    = 'Zend_Date::MILLISECOND';    // --- milliseconds

    const MINUTE_SHORT   = 'Zend_Date::MINUTE_SHORT';   // --- 1 digit minute, no leading zero, 0-59
    const SECOND_SHORT   = 'Zend_Date::SECOND_SHORT';   // --- 1 digit second, no leading zero, 0-59

    // timezone formats
    const TIMEZONE_NAME  = 'Zend_Date::TIMEZONE_NAME';  // e - timezone string
    const DAYLIGHT       = 'Zend_Date::DAYLIGHT';       // I - is Daylight saving time ?, 0-1
    const GMT_DIFF       = 'Zend_Date::GMT_DIFF';       // O - GMT difference, -1200 +1200
    const GMT_DIFF_SEP   = 'Zend_Date::GMT_DIFF_SEP';   // P - seperated GMT diff, -12:00 +12:00
    const TIMEZONE       = 'Zend_Date::TIMEZONE';       // T - timezone, EST, GMT, MDT
    const TIMEZONE_SECS  = 'Zend_Date::TIMEZONE_SECS';  // Z - timezone offset in seconds, -43200 +43200

    // date strings
    const ISO_8601       = 'Zend_Date::ISO_8601';       // c - ISO 8601 date string
    const RFC_2822       = 'Zend_Date::RFC_2822';       // r - RFC 2822 date string
    const TIMESTAMP      = 'Zend_Date::TIMESTAMP';      // U - unix timestamp

    // additional formats
    const ERA            = 'Zend_Date::ERA';            // --- short name of era, locale aware,
    const ERA_NAME       = 'Zend_Date::ERA_NAME';       // --- full name of era, locale aware,
    const DATES          = 'Zend_Date::DATES';          // --- standard date, locale aware
    const DATE_FULL      = 'Zend_Date::DATE_FULL';      // --- full date, locale aware
    const DATE_LONG      = 'Zend_Date::DATE_LONG';      // --- long date, locale aware
    const DATE_MEDIUM    = 'Zend_Date::DATE_MEDIUM';    // --- medium date, locale aware
    const DATE_SHORT     = 'Zend_Date::DATE_SHORT';     // --- short date, locale aware
    const TIMES          = 'Zend_Date::TIMES';          // --- standard time, locale aware
    const TIME_FULL      = 'Zend_Date::TIME_FULL';      // --- full time, locale aware
    const TIME_LONG      = 'Zend_Date::TIME_LONG';      // --- long time, locale aware
    const TIME_MEDIUM    = 'Zend_Date::TIME_MEDIUM';    // --- medium time, locale aware
    const TIME_SHORT     = 'Zend_Date::TIME_SHORT';     // --- short time, locale aware
    const ATOM           = 'Zend_Date::ATOM';           // --- DATE_ATOM
    const COOKIE         = 'Zend_Date::COOKIE';         // --- DATE_COOKIE
    const RFC_822        = 'Zend_Date::RFC_822';        // --- DATE_RFC822
    const RFC_850        = 'Zend_Date::RFC_850';        // --- DATE_RFC850
    const RFC_1036       = 'Zend_Date::RFC_1036';       // --- DATE_RFC1036
    const RFC_1123       = 'Zend_Date::RFC_1123';       // --- DATE_RFC1123
    const RSS            = 'Zend_Date::RSS';            // --- DATE_RSS
    const W3C            = 'Zend_Date::W3C';            // --- DATE_W3C

    private $_Fractional = 0;

    private $_Const = array(
        // day formats
        'Zend_Date::DAY'            => 'd',
        'Zend_Date::WEEKDAY_SHORT'  => 'D',
        'Zend_Date::DAY_SHORT'      => 'j',
        'Zend_Date::WEEKDAY'        => 'l',
        'Zend_Date::WEEKDAY_8601'   => 'N',
        'Zend_Date::DAY_SUFFIX'     => 'S',
        'Zend_Date::WEEKDAY_DIGIT'  => 'w',
        'Zend_Date::DAY_OF_YEAR'    => 'z',

        'Zend_Date::WEEKDAY_NARROW' => 'xxw',
        'Zend_Date::WEEKDAY_NAME'   => 'xxn',

        // week formats
        'Zend_Date::WEEK'           => 'W',

        // month formats
        'Zend_Date::MONTH'          => 'F',
        'Zend_Date::MONTH_SHORT'    => 'm',
        'Zend_Date::MONTH_NAME'     => 'M',
        'Zend_Date::MONTH_DIGIT'    => 'n',
        'Zend_Date::MONTH_DAYS'     => 't',

        'Zend_Date::MONTH_NARROW'   => 'xxm',

        // year formats
        'Zend_Date::LEAPYEAR'       => 'L',
        'Zend_Date::YEAR_8601'      => 'o',
        'Zend_Date::YEAR'           => 'Y',
        'Zend_Date::YEAR_SHORT'     => 'y',
        
        'Zend_Date::YEAR_SHORT_8601'=> 'xya',

        // time formats
        'Zend_Date::MERIDIEM'       => 'A',
        'Zend_Date::SWATCH'         => 'B',
        'Zend_Date::HOUR_SHORT_AM'  => 'g',
        'Zend_Date::HOUR_SHORT'     => 'G',
        'Zend_Date::HOUR_AM'        => 'h',
        'Zend_Date::HOUR'           => 'H',
        'Zend_Date::MINUTE'         => 'i',
        'Zend_Date::SECOND'         => 's',
        'Zend_Date::MILLISECOND'    => 'xms',

        'Zend_Date::MINUTE_SHORT'   => 'xxi',
        'Zend_Date::SECOND_SHORT'   => 'xxs',

        // timezone formats
        'Zend_Date::TIMEZONE_NAME'  => 'e',
        'Zend_Date::DAYLIGHT'       => 'I',
        'Zend_Date::GMT_DIFF'       => 'O',
        'Zend_Date::GMT_DIFF_SEP'   => 'P',
        'Zend_Date::TIMEZONE'       => 'T',
        'Zend_Date::TIMEZONE_SECS'  => 'Z',

        // date strings
        'Zend_Date::ISO_8601'       => 'c',
        'Zend_Date::RFC_2822'       => 'r',
        'Zend_Date::TIMESTAMP'      => 'U',

        // additional formats
        'Zend_Date::ERA'            => 'xxe',
        'Zend_Date::ERA_NAME'       => 'xxf',
        'Zend_Date::DATES'          => 'xdd',
        'Zend_Date::DATE_FULL'      => 'xdf',
        'Zend_Date::DATE_LONG'      => 'xdl',
        'Zend_Date::DATE_MEDIUM'    => 'xdm',
        'Zend_Date::DATE_SHORT'     => 'xds',
        'Zend_Date::TIMES'          => 'xtt',
        'Zend_Date::TIME_FULL'      => 'xtf',
        'Zend_Date::TIME_LONG'      => 'xtl',
        'Zend_Date::TIME_MEDIUM'    => 'xtm',
        'Zend_Date::TIME_SHORT'     => 'xts',
        'Zend_Date::ATOM'           => 'xxa',
        'Zend_Date::COOKIE'         => 'xxc',
        'Zend_Date::RFC_822'        => 'xr2',
        'Zend_Date::RFC_850'        => 'xr5',
        'Zend_Date::RFC_1036'       => 'xr6',
        'Zend_Date::RFC_1123'       => 'xr3',
        'Zend_Date::RSS'            => 'xxr',
        'Zend_Date::W3C'            => 'xx3'
    );


    /**
     * Date Object
     */
    public $_Date;


    /**
     * Locale Object / Setting
     */
    private $_Locale = '';


    /**
     * Generates the standard date object
     * could be
     *   - Unix timestamp
     *   - ISO
     *   - Locale
     *
     * @param $date string   - OPTIONAL date string depending on $parameter
     * @param $locale string - OPTIONAL locale for parsing input
     * @param $part mixed    - OPTIONAL defines the input format of $date
     * @return object
     */
    public function __construct($date = FALSE, $part = FALSE, $locale = FALSE)
    {
        // set locale
        if ($locale === FALSE) {
            $this->_Locale = new Zend_Locale();
        } else {
            $this->_Locale = $locale;
        }

        // set timestamp if not given
        if ($date === FALSE) {
            $date = time();
        }

        // set datepart
        if (($part !== FALSE) or (!is_numeric($date))) {

            $this->_Date = new Zend_Date_DateObject(0);
            $this->set($date, $part, $this->_Locale, FALSE);

        } else {
            $this->_Date = new Zend_Date_DateObject($date);
        }
    }


    /**
     * Returns the unix timestamp
     *
     * @return timestamp
     */
    public function getTimestamp()
    {
        return $this->_Date->getTimestamp();
    }


    /**
     * Sets a new timestamp
     *
     * @param $timestamp timestamp to set
     * @return object
     */
    public function setTimestamp($timestamp)
    {
        $this->_Date->setTimestamp($timestamp);
        return $this;
    }


    /**
     * Adds a timestamp
     *
     * @param $timestamp timestamp to add
     * @return object
     */
    public function addTimestamp($timestamp)
    {
        if (!is_numeric($timestamp)) {
            throw Zend::exception('Zend_Date_Exception', 'timestamp excepted');
        }

        $stamp = bcadd($this->_Date->getTimestamp(), $timestamp);
        return new Zend_Date($stamp);
    }


    /**
     * Substracts a timestamp
     *
     * @param $timestamp timestamp  to substract
     * @return object
     */
    public function subTimestamp($timestamp)
    {
        if (!is_numeric($timestamp)) {
            throw Zend::exception('Zend_Date_Exception', 'timestamp excepted');
        }

        return new Zend_Date($this->compareTimestamp($timestamp));
    }


    /**
     * Compares two timestamps, returning the difference as integer
     *
     * @param $timestamp timestamp to compare
     * @return object
     */
    public function compareTimestamp($timestamp)
    {
        if (is_object($timestamp)) {
            $timestamp = $timestamp->getTimestamp();
        }

        return bcsub($this->_Date->getTimestamp(), $timestamp);
    }


    /**
     * Compares two timestamps, returning boolean TRUE or FALSE
     *
     * @param $timestamp timestamp to compare
     * @return object
     */
    public function isTimestamp($timestamp)
    {
        if (is_object($timestamp)) {
            $timestamp = $timestamp->getTimestamp();
        }

        if ($this->getTimestamp() == $timestamp) {
            return TRUE;
        }
        
        return FALSE;
    }
    

    /**
     * Returns a string representation of the object
     * Supported format tokens are
     * G - era, y - year, Y - ISO year, M - month, w - week of year, D - day of year, d - day of month
     * E - day of week, e - number of weekday, h - hour 1-12, H - hour 0-23, m - minute, s - second
     * A - milliseconds of day, z - timezone, Z - timezone offset, S - fractional second
     * 
     * Not supported tokens are
     * u - extended year, Q - quarter, q - quarter, L - stand alone month, W - week of month
     * F - day of week of month, g - modified julian, c - stand alone weekday, k - hour 0-11, K - hour 1-24
     * v - wall zone
     * 
     * @param  $locale  locale  - OPTIONAL locale for parsing input
     * @param  $format  string  - OPTIONAL an rule for formatting the output
     * @param  $gmt     boolean - OPTIONAL, FALSE = actual timezone time, TRUE = UTC time
     * @return string
     */
    public function toString($format = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        if ($locale === FALSE) {
            $locale = $this->_Locale;
        }

        if ($format === FALSE) {
            $date = Zend_Locale_Data::getContent($locale, 'defdateformat', 'gregorian');
            $time = Zend_Locale_Data::getContent($locale, 'deftimeformat', 'gregorian');
            $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $date['default']));
            $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', $time['default']));
            
            $format  = $date['pattern'];
            $format .= " ";
            $format .= $time['pattern'];
        }

        // get format tokens
        $j = 0;
        $comment = FALSE;
        $output = array();
        for($i = 0; $i < strlen($format); ++$i) {

            if ($format[$i] == "'") {
                if ($comment == FALSE) {
                    $comment = TRUE;
                    ++$j;
                    $output[$j] = "'";
                } else if (isset($format[$i+1]) and ($format[$i+1] == "'")) {
                    $output[$j] .= "'";
                    ++$i;
                } else {
                    $comment = FALSE;
                }
                continue;
            }

            if (isset($output[$j]) and ($output[$j][0] == $format[$i]) or
                ($comment == TRUE)) { 
                $output[$j] .= $format[$i];
            } else {
                ++$j;
                $output[$j] = $format[$i];
            }
        }

        // fill format tokens with date information
        for($i = 1; $i <= count($output); ++$i) {

            // fill fixed tokens
            switch ($output[$i]) {

                // eras
                case 'GGGGG' :
                    $output[$i] = substr($this->get(Zend_Date::ERA, $gmt, $locale), 0, 1);
                    break;

                case 'GGGG' :
                    $output[$i] = $this->get(Zend_Date::ERA_NAME, $gmt, $locale);
                    break;

                case 'GGG' :
                case 'GG'  :
                case 'G'   :
                    $output[$i] = $this->get(Zend_Date::ERA, $gmt, $locale);
                    break; 


                // years
                case 'yy' :
                    $output[$i] = $this->get(Zend_Date::YEAR_SHORT, $gmt, $locale);
                    break; 


                // ISO years
                case 'YY' :
                    $output[$i] = $this->get(Zend_Date::YEAR_SHORT_8601, $gmt, $locale);
                    break;


                // months
                case 'MMMMM' :
                    $output[$i] = substr($this->get(Zend_Date::MONTH_NARROW, $gmt, $locale), 0, 1);
                    break;

                case 'MMMM' :
                    $output[$i] = $this->get(Zend_Date::MONTH, $gmt, $locale);
                    break;

                case 'MMM' :
                    $output[$i] = $this->get(Zend_Date::MONTH_NAME, $gmt, $locale);
                    break;

                case 'MM' :
                    $output[$i] = $this->get(Zend_Date::MONTH_SHORT, $gmt, $locale);
                    break;

                case 'M' :
                    $output[$i] = $this->get(Zend_Date::MONTH_DIGIT, $gmt, $locale);
                    break;


                // week
                case 'ww' :
                    $output[$i] = str_pad($this->get(Zend_Date::WEEK, $gmt, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'w' :
                    $output[$i] = $this->get(Zend_Date::WEEK, $gmt, $locale);
                    break;


                // monthday
                case 'dd' :
                    $output[$i] = $this->get(Zend_Date::DAY, $gmt, $locale);
                    break;

                case 'd' :
                    $output[$i] = $this->get(Zend_Date::DAY_SHORT, $gmt, $locale);
                    break;


                // yearday
                case 'DDD' :
                    $output[$i] = str_pad($this->get(Zend_Date::DAY_OF_YEAR, $gmt, $locale), 3, '0', STR_PAD_LEFT);
                    break;

                case 'DD' :
                    $output[$i] = str_pad($this->get(Zend_Date::DAY_OF_YEAR, $gmt, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'D' :
                    $output[$i] = $this->get(Zend_Date::DAY_OF_YEAR, $gmt, $locale);
                    break;


                // weekday
                case 'EEEEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NARROW, $gmt, $locale);
                    break;

                case 'EEEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY, $gmt, $locale);
                    break;

                case 'EEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_SHORT, $gmt, $locale);
                    break;

                case 'EE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NAME, $gmt, $locale);
                    break;

                case 'E' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NARROW, $gmt, $locale);
                    break;


                // weekday number
                case 'ee' :
                    $output[$i] = str_pad($this->get(Zend_Date::WEEKDAY_8601, $gmt, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'e' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_8601, $gmt, $locale);
                    break;


                // period
                case 'a' :
                    $output[$i] = $this->get(Zend_Date::MERIDIEM, $gmt, $locale);
                    break;


                // hour
                case 'hh' :
                    $output[$i] = $this->get(Zend_Date::HOUR_AM, $gmt, $locale);
                    break;

                case 'h' :
                    $output[$i] = $this->get(Zend_Date::HOUR_SHORT_AM, $gmt, $locale);
                    break;

                case 'HH' :
                    $output[$i] = $this->get(Zend_Date::HOUR, $gmt, $locale);
                    break;

                case 'H' :
                    $output[$i] = $this->get(Zend_Date::HOUR_SHORT, $gmt, $locale);
                    break;


                // minute
                case 'mm' :
                    $output[$i] = $this->get(Zend_Date::MINUTE, $gmt, $locale);
                    break;

                case 'm' :
                    $output[$i] = $this->get(Zend_Date::MINUTE_SHORT, $gmt, $locale);
                    break;


                // second
                case 'ss' :
                    $output[$i] = $this->get(Zend_Date::SECOND, $gmt, $locale);
                    break;

                case 's' :
                    $output[$i] = $this->get(Zend_Date::SECOND_SHORT, $gmt, $locale);
                    break;

                case 'S' :
                    $output[$i] = $this->get(Zend_Date::MILLISECOND, $gmt, $locale);
                    break;


                // zone
                case 'zzzz' :
                    $output[$i] = $this->get(Zend_Date::TIMEZONE_NAME, $gmt, $locale);
                    break;

                case 'zzz' :
                case 'zz'  :
                case 'z'   :
                    $output[$i] = $this->get(Zend_Date::TIMEZONE, $gmt, $locale);
                    break;


                // zone offset
                case 'ZZZZ' :
                    $output[$i] = $this->get(Zend_Date::GMT_DIFF_SEP, $gmt, $locale);
                    break;

                case 'ZZZ' :
                case 'ZZ'  :
                case 'Z'   :
                    $output[$i] = $this->get(Zend_Date::GMT_DIFF, $gmt, $locale);
                    break;
            }

            // fill variable tokens
            if (($output[$i][0] !== "'") and (preg_match('/y+/', $output[$i]))) {
                $length     = strlen($output[$i]);
                $output[$i] = $this->get(Zend_Date::YEAR, $gmt, $locale);
                $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
            }

            if (($output[$i][0] !== "'") and (preg_match('/Y+/', $output[$i]))) {
                $length     = strlen($output[$i]);
                $output[$i] = $this->get(Zend_Date::YEAR_8601, $gmt, $locale);
                $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
            }

            if (($output[$i][0] !== "'") and (preg_match('/A+/', $output[$i]))) {
                $length     = strlen($output[$i]);
                $seconds    = $this->get(Zend_Date::TIMESTAMP,   $gmt, $locale);
                $month      = $this->get(Zend_Date::MONTH_DIGIT, $gmt, $locale);
                $day        = $this->get(Zend_Date::DAY_SHORT,   $gmt, $locale);
                $year       = $this->get(Zend_Date::YEAR,        $gmt, $locale);
                $seconds   -= $this->_Date->mktime(0, 0, 0, $month, $day, $year, FALSE, $gmt);
                $output[$i] = str_pad($seconds, $length, '0', STR_PAD_LEFT);
            }
            
            if ($output[$i][0] === "'") {
                $output[$i] = substr($output[$i], 1);
            }
        }
        
        return implode('', $output);
    }


    /**
     * Returns a string representation of the object
     * Alias for toString
     *
     * @param  $locale  locale  - OPTIONAL locale for parsing input
     * @param  $format  string  - OPTIONAL an rule for formatting the output
     * @param  $gmt     boolean - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return string
     */
    public function __toString($format = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->toString($format, $gmt, $locale);
    }


    /**
     * Returns a integer representation of the object
     * Returns FALSE when the given part is no value f.e. Month-Name
     *
     * @param  $part   OPTIONAL  part of date to return as integer
     * @param  $gmt    OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return integer or FALSE
     */
    public function toValue($part = FALSE, $gmt = FALSE)
    {
        $result = $this->get($part, $gmt, FALSE);
        if (is_numeric($result)) {
          return intval("$result");
        } else {
          return FALSE;
        }
    }


    /**
     * Returns a timestamp or a part of a date
     *
     * @param  $part   - OPTIONAL, datepart, if empty the timestamp will be returned
     * @param  $locale - OPTIONAL, locale for output
     * @param  $gmt    - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return mixed   timestamp or datepart as string or integer
     */
    public function get($part = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        if ($part === FALSE) {
            $part = Zend_Date::TIMESTAMP;
        }

        if ($locale === FALSE) {
            $locale = $this->_Locale;
        }

        switch($part) {

            // day formats
            case Zend_Date::DAY :
                return $this->_Date->date('d', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::WEEKDAY_SHORT :
                $weekday = strtolower($this->_Date->date('D', $this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'wide', $weekday));
                return substr($day[$weekday], 0, 3);
                break;

            case Zend_Date::DAY_SHORT :
                return $this->_Date->date('j', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::WEEKDAY :
                $weekday = strtolower($this->_Date->date('D', $this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'wide', $weekday));
                return $day[$weekday];
                break;

            case Zend_Date::WEEKDAY_8601 :
                return $this->_Date->date('N', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::DAY_SUFFIX :
                return $this->_Date->date('S', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::WEEKDAY_DIGIT :
                return $this->_Date->date('w', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::DAY_OF_YEAR :
                return $this->_Date->date('z', $this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::WEEKDAY_NARROW :
                $weekday = strtolower($this->_Date->date('D', $this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'abbreviated', $weekday));
                return substr($day[$weekday], 0, 1);
                break;

            case Zend_Date::WEEKDAY_NAME :
                $weekday = strtolower($this->_Date->date('D', $this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'abbreviated', $weekday));
                return $day[$weekday];
                break;


            // week formats
            case Zend_Date::WEEK :
                return $this->_Date->date('W', $this->_Date->getTimestamp(), $gmt);
                break;


            // month formats
            case Zend_Date::MONTH :
                $month = $this->_Date->date('n', $this->_Date->getTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'wide', $month));
                return $mon[$month];
                break;

            case Zend_Date::MONTH_SHORT :
                return $this->_Date->date('m', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::MONTH_NAME :
                $month = $this->_Date->date('n', $this->_Date->getTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return $mon[$month];
                break;

            case Zend_Date::MONTH_DIGIT :
                return $this->_Date->date('n', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::MONTH_DAYS :
                return $this->_Date->date('t', $this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::MONTH_NARROW :
                $month = $this->_Date->date('n', $this->_Date->getTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return substr($mon[$month], 0, 1);
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                return $this->_Date->date('L', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::YEAR_8601 :
                return $this->_Date->date('o', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::YEAR :
                return $this->_Date->date('Y', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::YEAR_SHORT :
                return $this->_Date->date('y', $this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                $year = $this->_Date->date('o', $this->_Date->getTimestamp(), $gmt);
                return substr($year, -2);
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                $am = $this->_Date->date('a', $this->_Date->getTimestamp(), $gmt);
                $amlocal = Zend_Locale_Data::getContent($locale, 'daytime', 'gregorian');
                return $amlocal[$am];
                break;

            case Zend_Date::SWATCH :
                return $this->_Date->date('B', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR_SHORT_AM :
                return $this->_Date->date('g', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR_SHORT :
                return $this->_Date->date('G', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR_AM :
                return $this->_Date->date('h', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR :
                return $this->_Date->date('H', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::MINUTE :
                return $this->_Date->date('i', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::SECOND :
                return $this->_Date->date('s', $this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::MINUTE_SHORT :
                return $this->_Date->date('i', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::SECOND_SHORT :
                return $this->_Date->date('s', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::MILLISECOND :
                return $this->_Fractional;
                break;


            // timezone formats
            case Zend_Date::TIMEZONE_NAME :
                return $this->_Date->date('e', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::DAYLIGHT :
                return $this->_Date->date('I', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::GMT_DIFF :
                return $this->_Date->date('O', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::GMT_DIFF_SEP :
                return $this->_Date->date('P', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::TIMEZONE :
                return $this->_Date->date('T', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::TIMEZONE_SECS :
                return $this->_Date->date('Z', $this->_Date->getTimestamp(), $gmt);
                break;


            // date strings
            case Zend_Date::ISO_8601 :
                return $this->_Date->date('c', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_2822 :
                return $this->_Date->date('r', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::TIMESTAMP :
                return $this->_Date->getTimestamp();
                break;


            // additional formats
            case Zend_Date::ERA :
                $year = $this->_Date->date('Y', $this->_Date->getTimestamp(), $gmt);
                if ($year < 0) {
                    $era = Zend_Locale_Data::getContent($locale, 'erashort', array('gregorian', '0'));
                    return $era['0'];
                }
                $era = Zend_Locale_Data::getContent($locale, 'erashort', array('gregorian', '1'));
                return $era['1'];
                break;

            case Zend_Date::ERA_NAME :
                $year = $this->_Date->date('Y', $this->_Date->getTimestamp(), $gmt);
                if ($year < 0) {
                    $era = Zend_Locale_Data::getContent($locale, 'era', array('gregorian', '0'));
                    return $era['0'];
                }
                $era = Zend_Locale_Data::getContent($locale, 'era', array('gregorian', '1'));
                if (!isset($era['1'])) {
                    return false;
                }
                return $era['1'];
                break;

            case Zend_Date::DATES :
                $default = Zend_Locale_Data::getContent($locale, 'defdateformat', 'gregorian');
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $default['default']));
                return $this->toString($date['pattern'], $gmt, $locale);
                break;

            case Zend_Date::DATE_FULL :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'full'));
                return $this->toString($date['pattern'], $gmt, $locale);
                break;

            case Zend_Date::DATE_LONG :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'long'));
                return $this->toString($date['pattern'], $gmt, $locale);
                break;

            case Zend_Date::DATE_MEDIUM :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'medium'));
                return $this->toString($date['pattern'], $gmt, $locale);
                break;

            case Zend_Date::DATE_SHORT :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'short'));
                return $this->toString($date['pattern'], $gmt, $locale);
                break;

            case Zend_Date::TIMES :
                $default = Zend_Locale_Data::getContent($locale, 'deftimeformat', 'gregorian');
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', $default['default']));
                return $this->toString($time['pattern'], $gmt, $locale);
                break;

            case Zend_Date::TIME_FULL :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'full'));
                return $this->toString($time['pattern'], $gmt, $locale);
                break;

            case Zend_Date::TIME_LONG :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'long'));
                return $this->toString($time['pattern'], $gmt, $locale);
                break;

            case Zend_Date::TIME_MEDIUM :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'medium'));
                return $this->toString($time['pattern'], $gmt, $locale);
                break;

            case Zend_Date::TIME_SHORT :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'short'));
                return $this->toString($time['pattern'], $gmt, $locale);
                break;

            case Zend_Date::ATOM :
                return $this->_Date->date('Y\-m\-d\TH\:i\:sP', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::COOKIE :
                return $this->_Date->date('l\, d\-M\-y H\:i\:s e', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_822 :
                return $this->_Date->date('D\, d M y H\:i\:s O', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_850 :
                return $this->_Date->date('l\, d\-M\-y H\:i\:s e', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_1036 :
                return $this->_Date->date('D\, d M y H\:i\:s O', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_1123 :
                return $this->_Date->date('D\, d M Y H\:i\:s O', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::RSS :
                return $this->_Date->date('D\, d M Y H\:i\:s O', $this->_Date->getTimestamp(), $gmt);
                break;

            case Zend_Date::W3C :
                return $this->_Date->date('Y\-m\-d\TH\:i\:sP', $this->_Date->getTimestamp(), $gmt);
                break;


            default :
                return $this->_Date->date($part, $this->_Date->getTimestamp(), $gmt);
                break;
        }
    }


    /**
     * Return digit from standard names (english)
     * Faster implementation than locale aware searching
     */
    private function getDigitFromName($name)
    {
        switch($name) {
            case "Jan":
                return 1;

            case "Feb":
                return 2;

            case "Mar":
                return 3;

            case "Apr":
                return 4;

            case "May":
                return 5;

            case "Jun":
                return 6;

            case "Jul":
                return 7;

            case "Aug":
                return 8;

            case "Sep":
                return 9;

            case "Oct":
                return 10;

            case "Nov":
                return 11;

            case "Dec":
                return 12;
        }
    }


    /**
     * Sets the given date as new date
     *
     * @param  $date string  - date which shall be our new date object
     * @param  $part         - datepart, if empty the timestamp will be returned
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return timestamp
     */
    public function set($date, $part = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_calculate('set', $date, $part, $gmt, $locale);
    }
    

    /**
     * Adds a date to another date. Could add f.e. minutes, hours, days, months to a date object
     *
     * @param  $date string  - date which shall be our new date object
     * @param  $part         - OPTIONAL, datepart, if empty the timestamp will be returned
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return timestamp
     */
    public function add($date, $part = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        $this->_calculate('add', $date, $part, $gmt, $locale);
        return $this->get($part, $gmt, $locale);
    }


    /**
     * Substracts a date from another date. Could sub f.e. minutes, hours, days from a date object
     *
     * @param  $date string  - date which shall be our new date object
     * @param  $part         - OPTIONAL, datepart, if empty the timestamp will be returned
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return timestamp
     */
    public function sub($date, $part = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        $this->_calculate('sub', $date, $part, $locale, $gmt);
        return $this->get($part, $gmt, $locale);
    }


    /**
     * Compares a date with another date. Returns a date object with the difference date
     *
     * @param  $date object   - date which shall be compared with our actual date object
     * @param  $part datepart - OPTIONAL datepart to set
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return timestamp
     */
    public function compare($date, $part = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        $this->_calculate('sub', $date, $part, $gmt, $locale);
        return $this->get($part, $gmt, $locale);
    }


    /**
     * Returns a duplicate date object
     *
     * @param $part datepart - OPTIONAL the part of date to clone
     * @return object
     */
    public function cloneIt($part = FALSE)
    {
        $this->_calculate('clone', $this, $part, FALSE, FALSE);
    }


    /**
     * Calculates the date or object
     *
     * @param  $calc string  - calculation to make
     * @param  $date string  - date which shall be our new date object
     * @param  $comp string  - second date for calculation
     * @return timestamp
     */
    private function _assign($calc, $date, $comp = 0)
    {
        switch ($calc) {
            case 'add' :
                $this->_Date->setTimestamp(bcadd($this->_Date->getTimestamp(), $date));
                return $this->_Date->getTimestamp();
                break;
            case 'sub' :
                $this->_Date->setTimestamp(bcsub($this->_Date->getTimestamp(), $date));
                return $this->_Date->getTimestamp();
                break;
            case 'cmp' :
                return bcsub($comp, $date);
                break;
            case 'clone' :
                return new Zend_Date($comp);
                break;
            case 'set' :
//print "\nCOMP:".$comp."::".$this->_Date->date('D\, d M Y H\:i\:s O',$comp);
//print "\nDATE:".$date."::".$this->_Date->date('D\, d M Y H\:i\:s O',$date);
                $this->_Date->setTimestamp(bcsub($this->_Date->getTimestamp(), $comp));
//print "\nDATE2:".$this->get(Zend_Date::RSS);
                
                $this->_Date->setTimestamp(bcadd($this->_Date->getTimestamp(), $date));
//print "\nDATE3:".$this->get(Zend_Date::RSS);
                return $this->_Date->getTimestamp();
                break;
        }
    }


    /**
     * Calculates the date or object
     *
     * @param  $calc string  - calculation to make
     * @param  $date string  - date which shall be our new date object
     * @param  $part         - datepart, if empty the timestamp will be returned
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return timestamp
     */
    private function _calculate($calc, $date, $part = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        if ($locale === FALSE) {
            $locale = $this->_Locale;
        }

        // create date parts
        $year   = $this->get(Zend_Date::YEAR);
        $month  = $this->get(Zend_Date::MONTH_DIGIT);
        $day    = $this->get(Zend_Date::DAY_SHORT);
        $hour   = $this->get(Zend_Date::HOUR_SHORT);
        $minute = $this->get(Zend_Date::MINUTE_SHORT);
        $second = $this->get(Zend_Date::SECOND_SHORT);

        // if object extract value
        if (is_object($date)) {
            $date = $date->get($part, $gmt, $locale);
        }
        
        // $date as object, part of foreign date as own date
        switch($part) {

            // day formats
            case Zend_Date::DAY :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, intval($date), 1970, -1, $gmt), 
                                                 $this->_Date->mktime(0, 0, 0, 1, intval($day),  1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'day expected');
                break;

            case Zend_Date::WEEKDAY_SHORT :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper(substr($value, 0, 3)) == strtoupper($date)) {
                         $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw Zend::exception('Zend_Date_Exception', 'weekday expected');
                break;

            case Zend_Date::DAY_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, intval($date), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, intval($day), 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'day expected');
                break;

            case Zend_Date::WEEKDAY :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw Zend::exception('Zend_Date_Exception', 'weekday expected');
                break;

            case Zend_Date::WEEKDAY_8601 :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, intval($date), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, $weekday,      1970, -1, $gmt));
                }

                // Weekday not found
                throw Zend::exception('Zend_Date_Exception', 'weekday expected');
                break;

            case Zend_Date::DAY_SUFFIX :
                throw Zend::exception('Zend_Date_Exception', 'day suffix not supported');
                break;

            case Zend_Date::WEEKDAY_DIGIT :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, ($date + 1), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, $weekday,    1970, -1, $gmt));
                }

                // Weekday not found
                throw Zend::exception('Zend_Date_Exception', 'weekday expected');
                break;

            case Zend_Date::DAY_OF_YEAR :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1,      $date, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, $month, $day,  1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'day expected');
                break;

            case Zend_Date::WEEKDAY_NARROW :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper(substr($value, 0, 1)) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw Zend::exception('Zend_Date_Exception', 'weekday expected');
                break;

            case Zend_Date::WEEKDAY_NAME :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw Zend::exception('Zend_Date_Exception', 'weekday expected');
                break;


            // week formats
            case Zend_Date::WEEK :
                if (is_numeric($date)) {
                    $week = (int) $this->get(Zend_Date::WEEK, $gmt, $locale);
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, ($date * 7), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, ($week * 7), 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'week expected');
                break;


            // month formats
            case Zend_Date::MONTH :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $gmt, $locale));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }

                // Monthname found
                if ($cnt < 12) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $found,   1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, $monthnr, 1, 1970, -1, $gmt));
                }

                // Monthname not found
                throw Zend::exception('Zend_Date_Exception', 'month expected');
                break;

            case Zend_Date::MONTH_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, intval($date), 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, $month,        1, 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'month expected');
                break;

            case Zend_Date::MONTH_NAME :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $gmt, $locale));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }

                // Monthname found
                if ($cnt < 12) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $found,   1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, $monthnr, 1, 1970, -1, $gmt));
                }

                // Monthname not found
                throw Zend::exception('Zend_Date_Exception', 'month expected');
                break;

            case Zend_Date::MONTH_DIGIT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, intval($date), 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, $month,        1, 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'month expected');
                break;

            case Zend_Date::MONTH_DAYS :
                throw Zend::exception('Zend_Date_Exception', 'month days not supported');
                break;


            case Zend_Date::MONTH_NARROW :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $gmt, $locale));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper(substr($value, 0, 1)) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }

                // Monthname found
                if ($cnt < 12) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $found,   1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, $monthnr, 1, 1970, -1, $gmt));
                }

                // Monthname not found
                throw Zend::exception('Zend_Date_Exception', 'month expected');
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                throw Zend::exception('Zend_Date_Exception', 'leap year not supported');
                break;

            case Zend_Date::YEAR_8601 :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1, $year,         -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'year expected');
                break;

            case Zend_Date::YEAR :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1, $year,         -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'year expected');
                break;

            case Zend_Date::YEAR_SHORT :
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($date >= 0) and ($date <= 100)) {
                        $date += 1900;
                        if ($date < 1970) {
                            $date += 100;
                        }
                        return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, $date, -1, $gmt),
                                                     $this->_Date->mktime(0, 0, 0, 1, 1, $year, -1, $gmt));
                    }
                }
                throw Zend::exception('Zend_Date_Exception', 'year expected');
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($date >= 0) and ($date <= 100)) {
                        $date += 1900;
                        if ($date < 1970) {
                            $date += 100;
                        }
                        return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, $date, -1, $gmt),
                                                     $this->_Date->mktime(0, 0, 0, 1, 1, $year, -1, $gmt));
                    }
                }
                throw Zend::exception('Zend_Date_Exception', 'year expected');
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                throw Zend::exception('Zend_Date_Exception', 'meridiem not supported');
                break;

            case Zend_Date::SWATCH :
                if (is_numeric($date)) {
                    $rest = intval($date);
                    $hours = floor($rest * 24 / 1000);
                    $rest = $rest - ($hours * 1000 / 24);
                    $minutes = floor($rest * 1440 / 1000);
                    $rest = $rest - ($minutes * 1000 / 1440);
                    $seconds = floor($rest * 86400 / 1000); 
                    return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,  $minute,  $second,  1, 1, 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'swatchstamp expected');
                break;

            case Zend_Date::HOUR_SHORT_AM :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'hour expected');
                break;

            case Zend_Date::HOUR_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'hour expected');
                break;

            case Zend_Date::HOUR_AM :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'hour expected');
                break;

            case Zend_Date::HOUR :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'hour expected');
                break;

            case Zend_Date::MINUTE :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, intval($date), 0, 1, 1, 0, -1, $gmt),
                                                 $this->_Date->mktime(0, $minute,       0, 1, 1, 0, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'minute expected');
                break;

            case Zend_Date::SECOND :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, intval($date), 1, 1, 0, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, $second,       1, 1, 0, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'second expected');
                break;

            case Zend_Date::MILLISECOND :
                switch($calc) {
                    case 'set' :
                        return $this->setMillisecond(intval($date));
                        break;
                    case 'add' :
                        return $this->addMillisecond(intval($date));
                        break;
                    case 'sub' :
                        return $this->subMillisecond(intval($date));
                        break;
                }
                return $this->compareMillisecond(intval($date));
                break;

            case Zend_Date::MINUTE_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, intval($date), 0, 1, 1, 0, -1, $gmt),
                                                 $this->_Date->mktime(0, $minute,       0, 1, 1, 0, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'minute expected');
                break;

            case Zend_Date::SECOND_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, intval($date), 1, 1, 0, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, $second,       1, 1, 0, -1, $gmt));
                }
                throw Zend::exception('Zend_Date_Exception', 'second expected');
                break;


            // timezone formats
            // break intentionally omitted
            case Zend_Date::TIMEZONE_NAME :
            case Zend_Date::DAYLIGHT :
            case Zend_Date::GMT_DIFF :
            case Zend_Date::GMT_DIFF_SEP :
            case Zend_Date::TIMEZONE :
            case Zend_Date::TIMEZONE_SECS :
                return FALSE;
                break;


            // date strings
            case Zend_Date::ISO_8601 :
                $result = preg_match('/\d{4}-\d{2}-\d{2}[T\s]{1}\d{2}:\d{2}:\d{2}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }

                $years   = substr($match[0], 0, 4);
                $months  = substr($match[0], 5, 2);
                $days    = substr($match[0], 8, 2);
                $hours   = substr($match[0], 11, 2);
                $minutes = substr($match[0], 14, 2);
                $seconds = substr($match[0], 17, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_2822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::TIMESTAMP :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->getTimestamp());
                }
                return FALSE;
                break;


            // additional formats
            // break intentionally omitted
            case Zend_Date::ERA :
            case Zend_Date::ERA_NAME :
                return FALSE;
                break;

            case Zend_Date::DATES :
                if (strlen($part) > 3) {
                    $parsed = Zend_Locale_Format::getDate($date, substr($part, 3), $locale);
                } else {
                    $parsed = Zend_Locale_Format::getDate($date, 'default', $locale);
                }
                return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $parsed['month'], 
                                                         $parsed['day'], $parsed['year'], -1, $gmt),
                                             $this->_Date->mktime(0, 0, 0, $month, $day, $year, -1, $gmt));
                break;

            case Zend_Date::DATE_FULL :
                $parsed = Zend_Locale_Format::getDate($date, 'full', $locale);
                return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $parsed['month'], 
                                                         $parsed['day'], $parsed['year'], -1, $gmt),
                                             $this->_Date->mktime(0, 0, 0, $month, $day, $year, -1, $gmt));
                break;

            case Zend_Date::DATE_LONG :
                $parsed = Zend_Locale_Format::getDate($date, 'long', $locale);
                return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $parsed['month'], 
                                                         $parsed['day'], $parsed['year'], -1, $gmt),
                                             $this->_Date->mktime(0, 0, 0, $month, $day, $year, -1, $gmt));
                break;

            case Zend_Date::DATE_MEDIUM :
                $parsed = Zend_Locale_Format::getDate($date, 'medium', $locale);
                return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $parsed['month'], 
                                                         $parsed['day'], $parsed['year'], -1, $gmt),
                                             $this->_Date->mktime(0, 0, 0, $month, $day, $year, -1, $gmt));
                break;

            case Zend_Date::DATE_SHORT :
                $parsed = Zend_Locale_Format::getDate($date, 'short', $locale);
                return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, $parsed['month'], 
                                                         $parsed['day'], $parsed['year'], -1, $gmt),
                                             $this->_Date->mktime(0, 0, 0, $month, $day, $year, -1, $gmt));
                break;

            case Zend_Date::TIMES :
                if (strlen($part) > 3) {
                    $parsed = Zend_Locale_Format::getTime($date, substr($part, 3), $locale);
                } else {
                    $parsed = Zend_Locale_Format::getTime($date, 'default', $locale);
                }
                return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                         1, 1, 0, -1, $gmt),
                                             $this->_Date->mktime($hour, $minute, $second, 1, 1, 0, -1, $gmt));
                break;

            case Zend_Date::TIME_FULL :
                $parsed = Zend_Locale_Format::getTime($date, 'full', $locale);
                return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                         1, 1, 0, -1, $gmt),
                                             $this->_Date->mktime($hour, $minute, $second, 1, 1, 0, -1, $gmt));
                break;

            case Zend_Date::TIME_LONG :
                $parsed = Zend_Locale_Format::getTime($date, 'long', $locale);
                return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                         1, 1, 0, -1, $gmt),
                                             $this->_Date->mktime($hour, $minute, $second, 1, 1, 0, -1, $gmt));
                break;

            case Zend_Date::TIME_MEDIUM :
                $parsed = Zend_Locale_Format::getTime($date, 'medium', $locale);
                return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                         1, 1, 0, -1, $gmt),
                                             $this->_Date->mktime($hour, $minute, $second, 1, 1, 0, -1, $gmt));
                break;

            case Zend_Date::TIME_SHORT :
                $parsed = Zend_Locale_Format::getTime($date, 'short', $locale);
                return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                         1, 1, 0, -1, $gmt),
                                             $this->_Date->mktime($hour, $minute, $second, 1, 1, 0, -1, $gmt));
                break;

            case Zend_Date::ATOM :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }

                $years   = substr($match[0], 0, 4);
                $months  = substr($match[0], 5, 2);
                $days    = substr($match[0], 8, 2);
                $hours   = substr($match[0], 11, 2);
                $minutes = substr($match[0], 14, 2);
                $seconds = substr($match[0], 17, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::COOKIE :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }
                $match[0] = substr($match[0], strpos(' '+1));
                
                $days    = substr($match[0], 0, 2);
                $months  = $this->getDigitFromName(substr($match[0], 3, 3));
                $years   = substr($match[0], 7, 4);
                $years  += 2000;
                $hours   = substr($match[0], 10, 2);
                $minutes = substr($match[0], 13, 2);
                $seconds = substr($match[0], 16, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }
                
                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $years  += 2000;
                $hours   = substr($match[0], 15, 2);
                $minutes = substr($match[0], 18, 2);
                $seconds = substr($match[0], 21, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_850 :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }

                $match[0] = substr($match[0], strpos(' '+1));
                
                $days    = substr($match[0], 0, 2);
                $months  = $this->getDigitFromName(substr($match[0], 3, 3));
                $years   = substr($match[0], 7, 4);
                $years  += 2000;
                $hours   = substr($match[0], 10, 2);
                $minutes = substr($match[0], 13, 2);
                $seconds = substr($match[0], 16, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_1036 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }
                
                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $years  += 2000;
                $hours   = substr($match[0], 15, 2);
                $minutes = substr($match[0], 18, 2);
                $seconds = substr($match[0], 21, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_1123 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::RSS :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            case Zend_Date::W3C :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result) {
                    return FALSE;
                }

                $years   = substr($match[0], 0, 4);
                $months  = substr($match[0], 5, 2);
                $days    = substr($match[0], 8, 2);
                $hours   = substr($match[0], 11, 2);
                $minutes = substr($match[0], 14, 2);
                $seconds = substr($match[0], 17, 2);

                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, $months, $days, $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  $month,  $day,  $year,  -1, $gmt));
                break;

            default :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->getTimestamp());
                }
                return FALSE;
                break;
        }
    }


    /**
     * Returns TRUE when both date objects have equal dates set
     *
     * @param $date object
     * @return boolean
     */
    public function equals($date, $part = '')
    {
        $locale = $this->_Locale;

        // if object extract value
        if (is_object($date)) {
            $date = $date->get($part, $gmt, $locale);
        }

        if (empty($part)) {
            return ($date->getTimestamp() == $this->getTimestamp);
        }

        return ($date == $date->get($part));
    }


    /**
     * Returns the maximum date or datepart for the set date object
     *
     * @todo  implement function
     * @param $part  datepart / string - OPTIONAL
     * @return object / datepart
     */
    public function getMaximum($part)
    {
        throw Zend::exception('Zend_Date_Exception','function yet not implemented');
    }


    /**
     * Returns the minimum date or datepart for the set date object/calendar type
     *
     * @todo  implement function
     * @param $part  datepart / string - OPTIONAL
     * @return object / datepart
     */
    public function getMinimum($part)
    {
        throw Zend::exception('Zend_Date_Exception','function yet not implemented');
    }


    /**
     * Returns the first day of the week
     *
     * @todo  implement function
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $format string   - OPTIONAL how to return the day (Number, Name, DateObject)
     * @return mixed - object / datepart / integer
     */
    public function getFirstDayOfWeek($locale, $format)
    {
        throw Zend::exception('Zend_Date_Exception','function yet not implemented');
    }


    /**
     * Returns the time
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getTime($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $time = $this->get(Zend_Date::TIMES, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime($time['hour'], $time['minute'], $time['second'], 1, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated time
     *
     * @param $time string    - OPTIONAL time to calculate, when null the actual time is calculated
     * @param string $format  - OPTIONAL format to parse
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _time($time, $format, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($time)) {
            $format = 'HH:mm:ss';
            $time = $this->_Date->date($format);
        } else if (is_object($time)) {
            $time = $time->get(Zend_Date::TIMES, FALSE, $locale);
        }

        if ($format === FALSE) {
            $format = Zend_Date::TIMES;
        } else if (strlen($format) > 3) {
            $format = Zend_Date::TIMES . $format;
        }

        return $this->_calcdetail($time, $format, $locale, $calculation);
    }


    /**
     * Sets a new time
     *
     * @param $time string     - OPTIONAL time to compare, when null the actual time is compared
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setTime($time, $format = FALSE, $locale = FALSE)
    {
        return $this->_time($time, $format, $locale, 'set');
    }


    /**
     * Adds a time
     *
     * @param $time string     - OPTIONAL time to compare, when null the actual time is compared
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addTime($time, $format = FALSE, $locale = FALSE)
    {
        return $this->_time($time, $format, $locale, 'add');
    }


    /**
     * Substracts a time
     *
     * @param $time string     - OPTIONAL time to compare, when null the actual time is compared
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function subTime($time, $format = FALSE, $locale = FALSE)
    {
        return $this->_time($time, $format, $locale, 'sub');
    }


    /**
     * Compares only the time, returning the difference
     *
     * @param $time string     - OPTIONAL time to compare, when null the actual time is compared
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function compareTime($time, $format = FALSE, $locale = FALSE)
    {
        return $this->_time($time, $format, $locale, 'compare');
    }


    /**
     * Compares only the time, returning boolean TRUE/FALSE
     *
     * @param $time string     - OPTIONAL time to compare, when null the actual time is compared
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function isTime($time, $format = FALSE, $locale = FALSE)
    {
        return ($this->compareTime($time, $format, $locale) == 0);
    }


    /**
     * Returns the date part
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getDate($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $date = $this->get(Zend_Date::DATES, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, $date['month'], $date['day'], $date['year'], $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated date
     *
     * @param $date string    - OPTIONAL date to calculate, when null the actual date is calculated
     * @param string $format  - OPTIONAL format to parse
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _date($date, $format, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($date)) {
            $format = 'dd MM yy';
            $date = $this->_Date->date($format);
        } else if (is_object($date)) {
            $date = $date->get(Zend_Date::DATES, FALSE, $locale);
        }

        if ($format === FALSE) {
            $format = Zend_Date::DATES;
        } else if (strlen($format) > 3) {
            $format = Zend_Date::DATES . $format;
        }

        return $this->_calcdetail($date, $format, $locale, $calculation);
    }


    /**
     * Sets a new date
     *
     * @param $date string     - OPTIONAL date to set, when null the actual date is set
     * @param string $format  - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setDate($date, $format = FALSE, $locale = FALSE)
    {
        return $this->_date($date, $format, $locale, 'set');
    }


    /**
     * Adds a date
     *
     * @param $date string     - OPTIONAL date to add, when null the actual date is add
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addDate($date, $format = FALSE, $locale = FALSE)
    {
        return $this->_date($date, $format, $locale, 'add');
    }


    /**
     * Substracts a date
     *
     * @param $date string     - OPTIONAL date to sub, when null the actual date is subbed
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function subDate($date, $format = FALSE, $locale = FALSE)
    {
        return $this->_date($date, $format, $locale, 'sub');
    }


    /**
     * Compares only the date, returning the difference date
     *
     * @param $date string     - OPTIONAL date to compare, when null the actual date is compared
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function compareDate($date, $format = FALSE, $locale = FALSE)
    {
        return $this->_date($date, $format, $locale, 'compare');
    }


    /**
     * Compares only the date, returning boolean TRUE/FALSE
     *
     * @param $date string     - OPTIONAL date to compare, when null the actual date is compared
     * @param string $format   - OPTIONAL format to parse must be CLDR format!
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function isDate($date, $format = FALSE, $locale = FALSE)
    {
        return ($this->compareDate($date, $format, $locale) == 0);
    }
    
    
    /**
     * Returns a ISO8601 formatted date - ISO is locale-independent
     *
     * @return string
     */
    public function getIso()
    {
        return $this->get(Zend_Date::ISO_8601);
    }


    /**
     * Sets a new ISOdate
     *
     * @param $date string     - OPTIONAL ISOdate to set, when null the actual date is set
     * @return object
     */
    public function setIso($date = FALSE)
    {
        if (empty($date)) {
            $date = $this->_Date->date('c');
        }
        return $this->set($date, Zend_Date::ISO_8601);
    }


    /**
     * Adds a ISOdate
     *
     * @param $date string  - OPTIONAL ISOdate to add, when null the actual date is add
     * @return object
     */
    public function addIso($date = FALSE)
    {
        if (empty($date)) {
            $date = $this->_Date->date('c');
        }
        return $this->add($date, Zend_Date::ISO_8601);
    }


    /**
     * Substracts a ISOdate
     *
     * @param $date string  - OPTIONAL ISOdate to sub, when null the actual date is sub
     * @return object
     */
    public function subIso($date = FALSE)
    {
        if (empty($date)) {
            $date = $this->_Date->date('c');
        }
        return $this->sub($date, Zend_Date::ISO_8601);
    }


    /**
     * Compares IsoDate with date object, returning the difference date
     *
     * @param $date string - OPTIONAL ISOdate to compare, when null the actual date is used for compare
     * @return object
     */
    public function compareIso($date = FALSE)
    {
        if (empty($date)) {
            $date = $this->_Date->date('c');
        }
        return $this->compare($date, Zend_Date::ISO_8601);
    }


    /**
     * Returns a RFC822 formatted date - RFC822 is locale-independent
     *
     * @return string
     */
    public function getArpa()
    {
        return $this->get(Zend_Date::RFC822);
    }


    /**
     * Sets a new RFC822 formatted date
     *
     * @param $date string     - OPTIONAL RFC822 date to set, when null the actual date is set
     * @return object
     */
    public function setArpa($date)
    {
        if (empty($date)) {
            $date = $this->_Date->date('D\, d M y H\:m\:s O');
        }
        return $this->set($date, Zend_Date::RFC822);
    }


    /**
     * Adds a RFC822 formatted date
     *
     * @param $date string  - OPTIONAL RFC822 date to add, when null the actual date is add
     * @return object
     */
    public function addArpa($date)
    {
        if (empty($date)) {
            $date = $this->_Date->date('D\, d M y H\:m\:s O');
        }
        return $this->add($date, Zend_Date::RFC822);
    }


    /**
     * Substracts a RFC822 formatted date
     *
     * @param $date string  - OPTIONAL RFC822 date to sub, when null the actual date is sub
     * @return object
     */
    public function subArpa($date)
    {
        if (empty($date)) {
            $date = $this->_Date->date('D\, d M y H\:m\:s O');
        }
        return $this->sub($date, Zend_Date::RFC822);
    }


    /**
     * Compares a RFC822 formatted date with date object, returning the difference date
     *
     * @param $date string - OPTIONAL RFC822 date to compare, when null the actual date is used for compare
     * @return object
     */
    public function compareArpa($date)
    {
        if (empty($date)) {
            $date = $this->_Date->date('D\, d M y H\:m\:s O');
        }
        return $this->compare($date, Zend_Date::RFC822);
    }


    /**
     * Check if location is supported
     * 
     * @param $location array - locations array
     * @return $horizon float
     */
    private function _checkLocation($location)
    {
        if (!isset($location['longitude']) or !isset($location['latitude'])) {
            throw Zend::exception('Zend_Date_Exception','Location must include \'longitude\' and \'latitude\'');
        }
        if (($location['longitude'] > 180) or ($location['longitude'] < -180)) {
            throw Zend::exception('Zend_Date_Exception','Longitude must be between -180 and 180');
        }
        if (($location['latitude'] > 90) or ($location['latitude'] < -90)) {
            throw Zend::exception('Zend_Date_Exception','Latitude must be between -90 and 90');
        }

        if (!isset($location['horizon'])){
            $location['horizon'] = 'effective';
        }

        switch ($location['horizon']) {
            case 'civil' :
                return -0.104528;
                break;
            case 'nautic' :
                return -0.207912;
                break;
            case 'astonomic' :
                return -0.309017;
                break;
            default :
                return -0.0145439;
                break;
        }
    }


    /**
     * Returns the time of sunrise for this date and a given location
     *
     * @param  $location array - location of sunrise
     * @return object
     */
    public function getSunRise($location)
    {
        $horizon = $this->_checkLocation($location);
        return new Zend_Date($this->_Date->calcSun($location, $horizon, TRUE));
    }


    /**
     * Returns the time of sunset for this date and a given location
     *
     * @param  $location array - location of sunset
     * @return object
     */
    public function getSunSet($location)
    {
        $horizon = $this->_checkLocation($location);
        return new Zend_Date($this->_Date->calcSun($location, $horizon, FALSE));
    }


    /**
     * Returns an array with all sun-infos for a time and location
     *
     * @param  $location array - location of suninfo
     * @return array
     */
    public function getSunInfo($location)
    {
        if (isset($location['horizon'])) {
            $horizon = $this->_checkLocation($location);
            $suninfo = array(
                'sunrise' => $this->_Date->calcSun($location, $horizon, TRUE),
                'sunset'  => $this->_Date->calcSun($location, $horizon, FALSE)
            );
            return $suninfo;
        }
        
        $suninfo = array();
        for ($i = 0; $i < 4; ++$i) {
            switch ($i) {
                case 0 :
                    $location['horizon'] = 'effective';
                    break;
                case 1 :
                    $location['horizon'] = 'civil';
                    break;
                case 2 :
                    $location['horizon'] = 'nautic';
                    break;
                case 3 :
                    $location['horizon'] = 'astronomic';
                    break;
            }
            $horizon = $this->_checkLocation($location);
            $suninfo['sunrise'][$location['horizon']] = $this->_Date->calcSun($location, $horizon, TRUE);
            $suninfo['sunset'][$location['horizon']]  = $this->_Date->calcSun($location, $horizon, FALSE);
        }
        return $suninfo;
    }


    /**
     * Returns the timezone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return date_default_timezone_get();
    }


    /**
     * Sets the timezone
     *
     * @param $timezone string - timezone to set
     * @return boolean
     */
    public function setTimeZone($timezone)
    {
        return date_default_timezone_set($timezone);
    }


    /**
     * Returns if the date is a leap year
     *
     * @return boolean
     */
    public function isLeapYear()
    {
        return $this->_Date->isLeapYear();
    }


    /**
     * Returns if the date is todays date
     *
     * @return boolean
     */
    public function isToday()
    {
        $today = $this->_Date->date('Ymd');
        $day   = $this->_Date->date('Ymd',$this->_Date->getTimestamp());
        return ($today == $day);
    }


    /**
     * Returns if the date is yesterdays date
     *
     * @return boolean
     */
    public function isYesterday()
    {
        $today = $this->_Date->date('Ymd');
        $day   = $this->_Date->date('Ymd',$this->_Date->getTimestamp());
        return (($today - $day) == 1);
    }


    /**
     * Returns if the date is tomorrows date
     *
     * @return boolean
     */
    public function isTomorrow()
    {
        $today = $this->_Date->date('Ymd');
        $day   = $this->_Date->date('Ymd',$this->_Date->getTimestamp());
        return (($today - $day) == -1);
    }


    /**
     * Returns if the date is after this date
     *
     * @param $date object     - date to compare
     * @return boolean
     */
    public function isAfter($date)
    {
        if ($this->get() < $date->get()) {
            return TRUE;
        }

        return FALSE;
    }


    /**
     * Returns if the date is before this date
     *
     * @param $date object     - date to compare
     * @return boolean
     */
    public function isBefore($date)
    {
        if ($this->get() > $date->get()) {
            return TRUE;
        }

        return FALSE;
    }


    /**
     * Returns actual date as object
     *
     * @return object
     */
    public function now()
    {
        return new Zend_Date('',$this->_Locale);
    }


    /**
     * Calculate date details
     */
    private function _calcdetail($date, $type, $locale, $calculation)
    {
        switch($calculation) {
            case 'set' :
                return $this->set($date, $type, $locale, FALSE);
                break;
            case 'add' :
                return $this->add($date, $type, $locale, FALSE);
                break;
            case 'sub' :
                return $this->sub($date, $type, $locale, FALSE);
                break;
        }
        return $this->compare($date, $type, $locale, FALSE);
    }

    /**
     * Returns the year
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getYear($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $year = $this->get(Zend_Date::YEAR, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, 1, 1, $year, $locale, FALSE), $locale);
    }

    /**
     * Returns the calculated year
     *
     * @param $year string    - OPTIONAL year to calculate, when null the actual year is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _year($year, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($year)) {
            $year = $this->_Date->date('Y');
        } else if (is_object($year)) {
            $year = $year->get(Zend_Date::YEAR, FALSE, $locale);
        }

        return $this->_calcdetail($year, Zend_Date::YEAR, $locale, $calculation);
    }
    

    /**
     * Sets a new year
     *
     * @param $year object    - OPTIONAL year to set, when null the actual year is set
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setYear($year = FALSE, $locale = FALSE)
    {
        return $this->_year($year, $locale, 'set');
    }


    /**
     * Adds a year
     *
     * @param $year object    - OPTIONAL year to add, when null the actual year is add
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addYear($year = FALSE, $locale = FALSE)
    {
        return $this->_year($year, $locale, 'add');
    }


    /**
     * Substracts a year
     *
     * @param $year object    - OPTIONAL year to sub, when null the actual year is sub
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subYear($year = FALSE, $locale = FALSE)
    {
        return $this->_year($year, $locale, 'sub');
    }


    /**
     * Compares only the year part, returning the difference
     *
     * @param $year object    - OPTIONAL year to compare, when null the actual year is compared
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareYear($year = FALSE, $locale = FALSE)
    {
        return $this->_year($year, $locale, 'compare');
    }


    /**
     * Compares only the year part, returning boolean TRUE
     *
     * @param $year object    - OPTIONAL year to compare, when null the actual year is compared
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isYear($year = FALSE, $locale = FALSE)
    {
        return ($this->compareYear($year, $locale) == 0);
    }


    /**
     * Returns the month
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getMonth($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $month = $this->get(Zend_Date::MONTH_DIGIT, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, $month, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated month
     *
     * @param $month string    - OPTIONAL month to calculate, when null the actual month is calculated
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _month($month, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($month)) {
            $month = $this->_Date->date('n');
        } else if (is_object($month)) {
            $month = $month->get(Zend_Date::MONTH_DIGIT, FALSE, $locale);
        }

        if (is_numeric($month)) {
            $type = Zend_Date::MONTH_DIGIT;
        } else {
            switch (strlen($month)) {
                case 1 :
                   $type = Zend_Date::MONTH_NARROW;
                    break;
                case 3:
                    $type = Zend_Date::MONTH_NAME;
                    break;
                default:
                    $type = Zend_Date::MONTH;
                    break;
            }
        }
        return $this->_calcdetail($month, $type, $locale, $calculation);
    }
    

    /**
     * Sets a new month
     *
     * @param $month object   - OPTIONAL month to set, when null the actual month is set
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMonth($month = FALSE, $locale = FALSE)
    {
        return $this->_month($month, $locale, 'set');
    }


    /**
     * Adds a month
     *
     * @param $month object   - OPTIONAL month to add, when null the actual month is add
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addMonth($month = FALSE, $locale = FALSE)
    {
        return $this->_month($month, $locale, 'add');
    }


    /**
     * Substracts a month
     *
     * @param $month object   - OPTIONAL month to sub, when null the actual month is sub
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subMonth($month = FALSE, $locale = FALSE)
    {
        return $this->_month($month, $locale, 'sub');
    }


    /**
     * Compares only the month part, returning the difference
     *
     * @param $month object   - OPTIONAL month to compare, when null the actual month is compared
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     *      * @return string
     */
    public function compareMonth($month = FALSE, $locale = FALSE)
    {
        return $this->_month($month, $locale, 'compare');
    }


    /**
     * Compares only the month part, returning boolean TRUE
     *
     * @param $month object   - OPTIONAL month to compare, when null the actual month is compared
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isMonth($month = FALSE, $locale = FALSE)
    {
        return ($this->compareMonth($month, $locale) == 0);
    }


    /**
     * Returns the day
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getDay($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $day = $this->get(Zend_Date::DAY_SHORT, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, 1, $day, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated day
     *
     * @param $day string      - OPTIONAL day to calculate, when null the actual day is calculate
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _day($day, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($day)) {
            $day = $this->_Date->date('j');
        } else if (is_object($day)) {
            $day = $day->get(Zend_Date::DAY_SHORT, FALSE, $locale);
        }

        if (is_numeric($day)) {
            $type = Zend_Date::DAY_SHORT;
        } else {
            switch (strlen($day)) {
                case 1 :
                   $type = Zend_Date::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = Zend_Date::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = Zend_Date::WEEKDAY_SHORT;
                    break;
                default:
                    $type = Zend_Date::WEEKDAY;
                    break;
            }
        }
        return $this->_calcdetail($day, $type, $locale, $calculation);
    }

    /**
     * Sets a new day
     *
     * @param $day object     - OPTIONAL day to set, when null the actual day is set
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setDay($day = FALSE, $locale = FALSE)
    {
        return $this->_day($day, $locale, 'set');
    }


    /**
     * Adds a day
     *
     * @param $day object     - OPTIONAL day to add, when null the actual day is add
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addDay($day = FALSE, $locale = FALSE)
    {
        return $this->_day($day, $locale, 'add');
    }


    /**
     * Substracts a day
     *
     * @param $day object     - OPTIONAL day to sub, when null the actual day is sub
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subDay($day = FALSE, $locale = FALSE)
    {
        return $this->_day($day, $locale, 'sub');
    }


    /**
     * Compares only the day part, returning the difference
     *
     * @param $day string/integer - OPTIONAL day to compare, when null the actual day is used for compare
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareDay($day = FALSE, $locale = FALSE)
    {
        return $this->_day($day, $locale, 'compare');
    }


    /**
     * Compares only the day part, returning boolean TRUE
     *
     * @param $day string/integer - OPTIONAL day to compare, when null the actual day is used for compare
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isDay($day = FALSE, $locale = FALSE)
    {
        return ($this->compareDay($day, $locale) == 0);
    }


    /**
     * Returns the weekday
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getWeekday($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $day = $this->get(Zend_Date::DAY_SHORT, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, 1, $day, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated weekday
     *
     * @param $weekday string  - OPTIONAL weekday to calculate, when null the actual weekday is calculate
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _weekday($weekday, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($weekday)) {
            $weekday = $this->_Date->date('w');
        } else if (is_object($weekday)) {
            $weekday = $weekday->get(Zend_Date::WEEKDAY_DIGIT, FALSE, $locale);
        }

        if (is_numeric($weekday)) {
            $type = Zend_Date::WEEKDAY_DIGIT;
        } else {
            switch(strlen($weekday)) {
                case 1:
                   $type = Zend_Date::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = Zend_Date::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = Zend_Date::WEEKDAY_SHORT;
                    break;
                default:
                    $type = Zend_Date::WEEKDAY;
                    break;
            }
        }
        
        return $this->_calcdetail($weekday, $type, $locale, $calculation);
    }


    /**
     * Sets a new weekday
     *
     * @param $weekday string  - OPTIONAL weekday to set, when null the actual weekday is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setWeekday($weekday = FALSE, $locale = FALSE)
    {
        return $this->_weekday($weekday, $locale, 'set');
    }


    /**
     * Adds a weekday
     *
     * @param $weekday object  - OPTIONAL weekday to add, when null the actual weekday is add
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addWeekday($weekday = FALSE, $locale = FALSE)
    {
        return $this->_weekday($weekday, $locale, 'add');
    }


    /**
     * Substracts a weekday
     *
     * @param $weekday object  - OPTIONAL weekday to sub, when null the actual weekday is sub
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function subWeekday($weekday = FALSE, $locale = FALSE)
    {
        return $this->_weekday($weekday, $locale, 'sub');
    }


    /**
     * Compares only the weekday part, returning the difference
     *
     * @param $weekday object  - OPTIONAL weekday to compare, when null the actual weekday is compared
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareWeekday($weekday = FALSE, $locale = FALSE)
    {
        return $this->_weekday($weekday, $locale, 'compare');
    }


    /**
     * Compares only the weekday part, returning boolean TRUE
     *
     * @param $weekday object  - OPTIONAL weekday to compare, when null the actual weekday is compared
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isWeekday($weekday = FALSE, $locale = FALSE)
    {
        return ($this->compareWeekday($weekday, $locale) == 0);
    }


    /**
     * Returns the day of year
     *
     * @param $locale locale   - OPTIONAL locale for parsing
     * @return object
     */
    public function getDayOfYear($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $day = $day->get(Zend_Date::DAY_OF_YEAR, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, 1, $day, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated dayofyear
     *
     * @param $day string     - OPTIONAL dayofyear to calculate, when null the actual dayofyear is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _dayOfYear($day, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($day)) {
            $day = $this->_Date->date('z');
        } else if (is_object($day)) {
            $day = $day->get(Zend_Date::DAY_OF_YEAR, FALSE, $locale);
        }

        return $this->_calcdetail($day, Zend_Date::DAY_OF_YEAR, $locale, $calculation);
    }


    /**
     * Sets a new day of year
     *
     * @param $day object     - OPTIONAL day to set, when null the actual day is set
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setDayOfYear($day = FALSE, $locale = FALSE)
    {
        return $this->_dayOfYear($day, $locale, 'set');
    }


    /**
     * Adds a day of year
     *
     * @param $day object     - OPTIONAL day to add, when null the actual day is add
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addDayOfYear($day = FALSE, $locale = FALSE)
    {
        return $this->_dayOfYear($day, $locale, 'add');
    }


    /**
     * Substracts a day of year
     *
     * @param $day object     - OPTIONAL day to sub, when null the actual day is sub
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subDayOfYear($day = FALSE, $locale = FALSE)
    {
        return $this->_dayOfYear($day, $locale, 'sub');
    }


    /**
     * Compares only the day of year
     *
     * @param $day object     - OPTIONAL day to compare, when null the actual day is compared
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareDayOfYear($day = FALSE, $locale = FALSE)
    {
        return $this->_dayOfYear($day, $locale, 'compare');
    }


    /**
     * Compares only the day of year, returning boolean TRUE
     *
     * @param $day object     - OPTIONAL day to compare, when null the actual day is compared
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isDayOfYear($day = FALSE, $locale = FALSE)
    {
        return ($this->compareDayOfYear($day, $locale) == 0);
    }


    /**
     * Returns the hour
     *
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getHour($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $hour = $hour->get(Zend_Date::HOUR_SHORT, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime($hour, 0, 0, 1, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated hour
     *
     * @param $hour string    - OPTIONAL hour to calculate, when null the actual hour is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _hour($hour, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($hour)) {
            $hour = $this->_Date->date('G');
        } else if (is_object($hour)) {
            $hour = $hour->get(Zend_Date::HOUR_SHORT, FALSE, $locale);
        }

        return $this->_calcdetail($hour, Zend_Date::HOUR_SHORT, $locale, $calculation);
    }


    /**
     * Sets a new hour
     *
     * @param $hour object    - OPTIONAL hour to set, when null the actual hour is set
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setHour($hour = FALSE, $locale = FALSE)
    {
        return $this->_hour($hour, $locale, 'set');
    }


    /**
     * Adds a hour
     *
     * @param $hour object    - OPTIONAL hour to add, when null the actual hour is add
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addHour($hour = FALSE, $locale = FALSE)
    {
        return $this->_hour($hour, $locale, 'add');
    }


    /**
     * Substracts a hour
     *
     * @param $hour object    - OPTIONAL hour to sub, when null the actual hour is sub
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subHour($hour = FALSE, $locale = FALSE)
    {
        return $this->_hour($hour, $locale, 'sub');
    }


    /**
     * Compares only the hour part, returning the difference
     *
     * @param $hour object    - OPTIONAL hour to compare, when null the actual hour is compare
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareHour($hour = FALSE, $locale = FALSE)
    {
        return $this->_hour($hour, $locale, 'compare');
    }


    /**
     * Compares only the hour part, returning boolean TRUE
     *
     * @param $hour object    - OPTIONAL hour to compare, when null the actual hour is compare
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isHour($hour = FALSE, $locale = FALSE)
    {
        return ($this->compareHour($hour, $locale) == 0);
    }


    /**
     * Returns the minute
     *
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getMinute($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }
        $minute = $minute->get(Zend_Date::MINUTE_SHORT, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, $minute, 0, 1, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated minute
     *
     * @param $minute string    - OPTIONAL minute to calculate, when null the actual minute is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _minute($minute, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($minute)) {
            $minute = $this->_Date->date('i');
        } else if (is_object($minute)) {
            $minute = $minute->get(Zend_Date::MINUTE_SHORT, FALSE, $locale);
        }

        return $this->_calcdetail($minute, Zend_Date::MINUTE_SHORT, $locale, $calculation);
    }


    /**
     * Sets a new minute
     *
     * @param $minute object  - OPTIONAL minute to set, when null the actual minute is set
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMinute($minute = FALSE, $locale = FALSE)
    {
        return $this->_minute($minute, $locale, 'set');
    }


    /**
     * Adds a minute
     *
     * @param $minute object  - OPTIONAL minute to add, when null the actual minute is add
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addMinute($minute = FALSE, $locale = FALSE)
    {
        return $this->_minute($minute, $locale, 'add');
    }


    /**
     * Substracts a minute
     *
     * @param $minute object  - OPTIONAL minute to sub, when null the actual minute is sub
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subMinute($minute = FALSE, $locale = FALSE)
    {
        return $this->_minute($minute, $locale, 'add');
    }


    /**
     * Compares only the minute part, returning the difference
     *
     * @param $minute object  - OPTIONAL minute to compare, when null the actual minute is compared
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareMinute($minute = FALSE, $locale = FALSE)
    {
        return $this->_minute($minute, $locale, '´compare');
    }


    /**
     * Compares only the minute part, returning boolean TRUE
     *
     * @param $minute object  - OPTIONAL minute to compare, when null the actual minute is compared
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isMinute($minute = FALSE, $locale = FALSE)
    {
        return ($this->compareMinute($minute, $locale) == 0);
    }


    /**
     * Returns the second
     *
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getSecond($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        $second = $second->get(Zend_Date::SECOND_SHORT, FALSE, $locale);
        return new Zend_Date($this->_Date->mktime(0, 0, $second, 1, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Returns the calculated second
     *
     * @param $second string  - OPTIONAL second to calculate, when null the actual second is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _second($second, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($second)) {
            $second = $this->_Date->date('s');
        } else if (is_object($second)) {
            $second = $second->get(Zend_Date::SECOND_SHORT, FALSE, $locale);
        }

        return $this->_calcdetail($second, Zend_Date::SECOND_SHORT, $locale, $calculation);
    }


    /**
     * Sets a new second
     *
     * @param $second object  - OPTIONAL second to set, when null the actual second is set
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setSecond($second = FALSE, $locale = FALSE)
    {
        return $this->_second($second, $locale, 'set');
    }


    /**
     * Adds a second
     *
     * @param $second object  - OPTIONAL second to add, when null the actual second is add
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addSecond($second = FALSE, $locale = FALSE)
    {
        return $this->_second($second, $locale, 'add');
    }


    /**
     * Substracts a second
     *
     * @param $second object  - OPTIONAL second to sub, when null the actual second is sub'ed
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subSecond($second = FALSE, $locale = FALSE)
    {
        return $this->_second($second, $locale, 'sub');
    }


    /**
     * Compares only the second part, returning the difference
     *
     * @param $second object  - OPTIONAL second to compare, when null the actual second is compared
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareSecond($second = FALSE, $locale = FALSE)
    {
        return $this->_second($second, $locale, 'compare');
    }


    /**
     * Compares only the second part, returning boolean TRUE
     * @param $second object  - OPTIONAL second to compare, when null the actual second is compared
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return boolean
     */
    public function isSecond($second = FALSE, $locale = FALSE)
    {
        return ($this->compareSecond($second, $locale) == 0);
    }


    /**
     * Returns the milliseconds
     *
     * @return integer
     */
    public function getMilliSecond()
    {
        return $this->_Fractional;
    }


    /**
     * Sets a new millisecond
     *
     * @param $milli integer - OPTIONAL millisecond to set, when null the actual millisecond is set
     * @return integer
     */
    public function setMilliSecond($milli = FALSE)
    {
        if ($milli === FALSE) {
            $milli = 0;
        }
        $this->_Fractional = intval($milli);
        return $this->_Fractional;
    }


    /**
     * Adds a millisecond
     *
     * @param $milli object - OPTIONAL millisecond to add, when null the actual millisecond is add
     * @return integer
     */
    public function addMilliSecond($milli = FALSE)
    {
        if ($milli === FALSE) {
            $milli = 0;
        }
        $this->_Fractional += intval($milli);
        if ($this->_Fractional > 1000) {
            $this->addSecond(1);
            $this->_Fractional -= 1000;
        }
        return $this->_Fractional;
    }


    /**
     * Substracts a millisecond
     *
     * @param $milli object - OPTIONAL millisecond to sub, when null the actual millisecond is sub
     * @return integer
     */
    public function subMilliSecond($milli = FALSE)
    {
        if ($milli === FALSE) {
            $milli = 0;
        }
        $this->_Fractional -= intval($milli);
        if ($this->_Fractional < 0) {
            $this->subSecond(1);
            $this->_Fractional += 1000;
        }
        return $this->_Fractional;
    }


    /**
     * Compares only the millisecond part, returning the difference
     *
     * @param $milli integer - OPTIONAL millisecond to compare, when null the actual millisecond is used for compare
     * @return integer
     */
    public function compareMilliSecond($milli = FALSE)
    {
        if ($milli === FALSE) {
            $milli = 0;
        }
        $comp = $this->_Fractional - intval($milli);
        return $comp;
    }


    /**
     * Compares only the millisecond part, returning boolean TRUE
     *
     * @param $milli integer - OPTIONAL millisecond to compare, when null the actual millisecond is used for compare
     * @return string
     */
    public function isMilliSecond($milli = FALSE)
    {
        return ($this->compareMilliSecond($milli) == 0);
    }


    /**
     * Returns the week
     *
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return string
     */
    public function getWeek($locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        $week = $week->get(Zend_Date::WEEK, FALSE, $locale);
        return $week;
    }


    /**
     * Returns the calculated week
     *
     * @param $week string    - OPTIONAL week to calculate, when null the actual week is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _week($week, $locale, $calculation)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($week)) {
            $week = $this->_Date->date('W');
        } else if (is_object($week)) {
            $week = $week->get(Zend_Date::WEEK, FALSE, $locale);
        }

        return $this->_calcdetail($week, Zend_Date::WEEK, $locale, $calculation);
    }


    /**
     * Sets a new week
     *
     * @param $week object    - OPTIONAL week to set, when null the actual week is set
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setWeek($week = FALSE, $locale = FALSE)
    {
        return $this->_week($week, $locale, 'set');
    }


    /**
     * Adds a week
     *
     * @param $week object    - OPTIONAL week to add, when null the actual week is add
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function addWeek($week = FALSE, $locale = FALSE)
    {
        return $this->_week($week, $locale, 'add');
    }


    /**
     * Substracts a week
     *
     * @param $week object    - OPTIONAL week to sub, when null the actual week is sub
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return object
     */
    public function subWeek($week = FALSE, $locale = FALSE)
    {
        return $this->_week($week, $locale, 'sub');
    }


    /**
     * Compares only the week part, returning the difference
     *
     * @param $week object    - OPTIONAL week to compare, when null the actual week is compare
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return string
     */
    public function compareWeek($week = FALSE, $locale = FALSE)
    {
        return $this->_week($week, $locale, 'compare');
    }


    /**
     * Compares only the week part, returning boolean TRUE
     *
     * @param $week object    - OPTIONAL week to compare, when null the actual week is compare
     * @param $locale string  - OPTIONAL locale for parsing input
     * @return string
     */
    public function isWeek($week = FALSE, $locale = FALSE)
    {
        return ($this->compareWeek($week) == 0);
    }
}