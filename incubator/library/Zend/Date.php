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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Date classes
 */
require_once 'Zend/Date/DateObject.php';
require_once 'Zend/Date/Exception.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006-2007 Zend Technologies USA Inc. (http://www.zend.com)
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
    const RFC_3339       = 'Zend_Date::RFC_3339';       // --- DATE_RFC3339
    const RSS            = 'Zend_Date::RSS';            // --- DATE_RSS
    const W3C            = 'Zend_Date::W3C';            // --- DATE_W3C

    // Fractional second variables 
    private $_Fractional = 0;
    private $_Precision  = 3;


    // actual date object
    public $_Date;
    private $_Locale = NULL;


    /**
     * Generates the standard date object, could be a unix timestamp, localized date, 
     * string, integer and so on. Also parts of dates or time are supported
     * For detailed instruction please look in the docu.
     * 
     * @param  string|integer      $date    OPTIONAL Defines the date or datepart to set depending on $part
     * @param  string              $part    OPTIONAL Defines the input format of $date
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function __construct($date = FALSE, $part = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        foreach(array('date','part','gmt') as $param) {
            $type = gettype($$param);
            if ($type === 'object' && ($$param instanceof Zend_Locale)) {
                $locale = $$param;
                $restFalse = true;
            }
            if (isset($restFalse)) {
                $$param = false;
            }
        }

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
            $this->set($date, $part, $gmt, $this->_Locale);

        } else {
            $this->_Date = new Zend_Date_DateObject($date);
        }
    }


    /**
     * Returns the actual date as unix timestamp
     * A timestamp greater then the integer range will be returned as string
     * This function does not return the timestamp as object. Use copy() instead. 
     *
     * @return integer|string
     */
    public function getTimestamp()
    {
        return $this->_Date->getTimestamp();
    }


    /**
     * Returns the calculated timestamp
     * HINT: timestamps are always GMT
     *
     * @param  string                    $calc    Type of calculation to make
     * @param  string|integer|Zend_Date  $stamp   OPTIONAL Timestamp to calculate, when null the actual timestamp is calculated
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _timestamp($calc, $stamp, $locale)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (is_object($stamp)) {
            // extract timestamp from object
            $stamp = $stamp->get(Zend_Date::TIMESTAMP, TRUE, $locale);
        } else if (empty($stamp)) {
            $stamp = $this->_Date->date('U', FALSE, TRUE);
        }

        $return = $this->_calcdetail($calc, $stamp, Zend_Date::TIMESTAMP, FALSE, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp(), Zend_Date::TIMESTAMP, FALSE, $locale);
        }
        return $return;
    }


    /**
     * Sets a new timestamp
     *
     * @param  integer|string      $timestamp  Timestamp to set, if null the actual timestamp is set
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function setTimestamp($timestamp = NULL, $locale = NULL)
    {
        return $this->_timestamp('set', $timestamp, $locale);
    }


    /**
     * Adds a timestamp
     *
     * @param  integer|string      $timestamp  Timestamp to add, if null the actual timestamp is added
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function addTimestamp($timestamp = NULL, $locale = NULL)
    {
        return $this->_timestamp('add', $timestamp, $locale);
    }


    /**
     * Substracts a timestamp
     *
     * @param  integer|string      $timestamp  Timestamp to sub, if null the actual timestamp is substracted
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function subTimestamp($timestamp = NULL, $locale = NULL)
    {
        return $this->_timestamp('sub', $timestamp, $locale);
    }


    /**
     * Compares two timestamps, returning the difference as integer
     *
     * @param  integer|string      $timestamp  Timestamp to compare, if null the actual timestamp is compared
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTimestamp($timestamp = NULL, $locale = NULL)
    {
        return $this->_timestamp('cmp', $timestamp, $locale);
    }


    /**
     * Returns a string representation of the object
     * Supported format tokens are:
     * G - era, y - year, Y - ISO year, M - month, w - week of year, D - day of year, d - day of month
     * E - day of week, e - number of weekday, h - hour 1-12, H - hour 0-23, m - minute, s - second
     * A - milliseconds of day, z - timezone, Z - timezone offset, S - fractional second
     *
     * Not supported tokens are
     * u - extended year, Q - quarter, q - quarter, L - stand alone month, W - week of month
     * F - day of week of month, g - modified julian, c - stand alone weekday, k - hour 0-11, K - hour 1-24
     * v - wall zone
     *
     * @param  string              $format  OPTIONAL An rule for formatting the output, if null the default dateformat is used
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function toString($format = NULL, $gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (empty($format)) {
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
                    $output[$i] = substr($this->get(Zend_Date::ERA, $gmt, $locale), 0, 1) . ".";
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
     * Returns a string representation of the date which is equal with the timestamp
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString(NULL, FALSE, $this->_Locale);
    }


    /**
     * Returns a integer representation of the object
     * But returns false when the given part is no value f.e. Month-Name
     *
     * @param  string|integer|Zend_Date  $part  OPTIONAL Defines the date or datepart to return as integer
     * @param  boolean                   $gmt   OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @return integer|false
     */
    public function toValue($part = NULL, $gmt = FALSE)
    {
        $result = $this->get($part, $gmt, FALSE);
        if (is_numeric($result)) {
          return intval("$result");
        } else {
          return FALSE;
        }
    }


    /**
     * Returns a representation of a date or datepart
     * This could be for example a localized monthname, the time without date,
     * the era or only the fractional seconds. There are about 50 different supported dateparts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string              $part    OPTIONAL Part of the date to return, if null the timestamp is returned
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return integer|string  date or datepart
     */
    public function get($part = NULL, $gmt = FALSE, $locale = NULL)
    {
        if (empty($part)) {
            $part = Zend_Date::TIMESTAMP;
        }

        if (empty($locale)) {
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

            case Zend_Date::RFC_3339 :
                return $this->_Date->date('Y\-m\-d\TH\:i\:sP', $this->_Date->getTimestamp(), $gmt);
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
     * Sets the given date as new date or a given datepart as new datepart returning the new datepart 
     * This could be for example a localized dayname, the date without time,
     * the month or only the seconds. There are about 50 different supported dateparts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to set 
     * @param  string                    $part    OPTIONAL Part of the date to set, if null the timestamp is set
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function set($date, $part = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_calculate('set', $date, $part, $gmt, $locale);
    }


    /**
     * Adds a date or datepart to the existing date
     * This could be for example a ISO 8601 date, the hour,
     * the monthname or only the minute. There are about 50 different supported dateparts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to add 
     * @param  string                    $part    OPTIONAL Part of the date to add, if null the timestamp is added
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function add($date, $part = NULL, $gmt = TRUE, $locale = NULL)
    {
        $this->_calculate('add', $date, $part, $gmt, $locale);
        return $this->get($part, $gmt, $locale);
    }


    /**
     * Substracts a date from another date.
     * This could be for example a RFC2822 date, the time,
     * the year or only the timestamp. There are about 50 different supported dateparts.
     * For a complete list of supported datepart values look into the docu
     * Be aware: Adding -2 Months is not equal to Substracting 2 Months !!! 
     * 
     * @param  string|integer|Zend_Date  $date    Date or datepart to substract
     * @param  string                    $part    OPTIONAL Part of the date to sub, if null the timestamp is substracted
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function sub($date, $part = NULL, $gmt = TRUE, $locale = NULL)
    {
        $this->_calculate('sub', $date, $part, $gmt, $locale);
        return $this->get($part, $gmt, $locale);
    }


    /**
     * Compares a date or datepart with the existing one. 
     * Returns -1 if earlier, 0 if equal and 1 if later.
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to compare with the date object
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is substracted
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compare($date, $part = NULL, $gmt = TRUE, $locale = NULL)
    {
        $compare = $this->_calculate('cmp', $date, $part, $gmt, $locale);
        if ($compare > 0) {
            return 1;
        } else if ($compare < 0) {
            return -1;
        }
        return 0;
    }


    /**
     * Returns a new copy of the date object or a datepart
     * For a complete list of supported datepart values look into the docu
     * If a datepart is copies, all other dateparts are set to standard values.
     * For example: If only YEAR is copied, the returned date object is equal to
     * 01-01-YEAR 00:00:00 (01-01-1970 00:00:00 is equal to timestamp 0)
     * 
     * @param  string              $part    OPTIONAL Part of the date to compare, if null the timestamp is substracted
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function copy($part = NULL, $locale = NULL)
    {
        return $this->_calculate('copy', $this, $part, FALSE, $locale);
    }


    /**
     * Calculates the date or object
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer            $date    Date for calculation
     * @param  string|integer            $comp    Second date for calculation
     * @return integer|string|Zend_Date  new timestamp or Zend_Date depending on calculation
     * @todo   erase bcmath with helper class
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
            case 'copy' :
                return new Zend_Date($date);
                break;
            case 'set' :
                $this->_Date->setTimestamp(bcsub($this->_Date->getTimestamp(), $comp));
                $this->_Date->setTimestamp(bcadd($this->_Date->getTimestamp(), $date));
                return $this->_Date->getTimestamp();
                break;
        }
    }


    /**
     * Calculates the date or object
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $date    OPTIONAL Date or datepart to calculate with, if null the actual date is taken
     * @param  string                    $part    OPTIONAL Part of the date to calculate, if null the timestamp is used
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|string|Zend_Date  new timestamp
     * @throws Zend_Date_Exception
     */
    private function _calculate($calc, $date = NULL, $part = NULL, $gmt = FALSE, $locale = NULL)
    {
        foreach(array('date','part','gmt') as $param) {
            $type = gettype($$param);
            if ($type === 'object' && ($$param instanceof Zend_Locale)) {
                $locale = $$param;
                $restFalse = TRUE;
            }
            if (isset($restFalse)) {
                $$param = FALSE;
            }
        }

        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        // create date parts
        $year   = $this->get(Zend_Date::YEAR);
        $month  = $this->get(Zend_Date::MONTH_DIGIT);
        $day    = $this->get(Zend_Date::DAY_SHORT);
        $hour   = $this->get(Zend_Date::HOUR_SHORT);
        $minute = $this->get(Zend_Date::MINUTE_SHORT);
        $second = $this->get(Zend_Date::SECOND_SHORT);

        if (is_null($date)) {
            $temp = new Zend_Date($this->_Date->date('U', FALSE, $gmt), Zend_Date::TIMESTAMP, $gmt, $locale);
            $date = $temp->get($part, $gmt, $locale);
        }

        // if object extract value
        if (is_object($date)) {
            $date = $date->get($part, $gmt, $locale);
        }

        // $date as object, part of foreign date as own date
        switch($part) {

            // day formats
            case Zend_Date::DAY :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + intval($date), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + intval($day),  1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('day expected');
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
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw new Zend_Date_Exception('weekday expected');
                break;

            case Zend_Date::DAY_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + intval($date), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + intval($day),  1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('day expected');
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
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw new Zend_Date_Exception('weekday expected');
                break;

            case Zend_Date::WEEKDAY_8601 :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + intval($date), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + $weekday,      1970, -1, $gmt));
                }

                // Weekday not found
                throw new Zend_Date_Exception('weekday expected');
                break;

            case Zend_Date::DAY_SUFFIX :
                throw new Zend_Date_Exception('day suffix not supported');
                break;

            case Zend_Date::WEEKDAY_DIGIT :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $gmt, $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 2 + $date,    1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw new Zend_Date_Exception('weekday expected');
                break;

            case Zend_Date::DAY_OF_YEAR :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1,      1 + $date, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, $month, 1 + $day,  1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('day expected');
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
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw new Zend_Date_Exception('weekday expected');
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
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + $found,   1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + $weekday, 1970, -1, $gmt));
                }

                // Weekday not found
                throw new Zend_Date_Exception('weekday expected');
                break;


            // week formats
            case Zend_Date::WEEK :
                if (is_numeric($date)) {
                    $week = (int) $this->get(Zend_Date::WEEK, $gmt, $locale);
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1 + ($date * 7), 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1 + ($week * 7), 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('week expected');
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
                    if ($calc == 'set') {
                        --$found;
                        --$monthnr;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $found,   1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $monthnr, 1, 1970, -1, $gmt));
                }

                // Monthname not found
                throw new Zend_Date_Exception('month expected');
                break;

            case Zend_Date::MONTH_SHORT :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        --$date;
                        --$month;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + intval($date), 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $month,        1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('month expected');
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
                    if ($calc == 'set') {
                        --$found;
                        --$monthnr;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $found,   1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $monthnr, 1, 1970, -1, $gmt));
                }

                // Monthname not found
                throw new Zend_Date_Exception('month expected');
                break;

            case Zend_Date::MONTH_DIGIT :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        --$date;
                        --$month;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + intval($date), 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $month,        1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('month expected');
                break;

            case Zend_Date::MONTH_DAYS :
                throw new Zend_Date_Exception('month days not supported');
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
                    if ($calc == 'set') {
                        --$found;
                        --$monthnr;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $found,   1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $monthnr, 1, 1970, -1, $gmt));
                }

                // Monthname not found
                throw new Zend_Date_Exception('month expected');
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                throw new Zend_Date_Exception('leap year not supported');
                break;

            case Zend_Date::YEAR_8601 :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        $date -= 1970;
                        $year -= 1970;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + intval($date), -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + $year,         -1, $gmt));
                }
                throw new Zend_Date_Exception('year expected');
                break;

            case Zend_Date::YEAR :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        $date -= 1970;
                        $year -= 1970;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + intval($date), -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + $year,         -1, $gmt));
                }
                throw new Zend_Date_Exception('year expected');
                break;

            case Zend_Date::YEAR_SHORT :
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($date >= 0) and ($date <= 100)) {
                        $date += 1900;
                        if ($date < 1970) {
                            $date += 100;
                        }
                        if ($calc == 'set') {
                            $date -= 1970;
                            $year -= 1970;
                        }
                        return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + $date, -1, $gmt),
                                                     $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + $year, -1, $gmt));
                    }
                }
                throw new Zend_Date_Exception('year expected');
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($date >= 0) and ($date <= 100)) {
                        $date += 1900;
                        if ($date < 1970) {
                            $date += 100;
                        }
                        if ($calc == 'set') {
                            $date -= 1970;
                            $year -= 1970;
                        }
                        return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + $date, -1, $gmt),
                                                     $this->_Date->mktime(0, 0, 0, 1, 1, 1970 + $year, -1, $gmt));
                    }
                }
                throw new Zend_Date_Exception('year expected');
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                throw new Zend_Date_Exception('meridiem not supported');
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
                throw new Zend_Date_Exception('swatchstamp expected');
                break;

            case Zend_Date::HOUR_SHORT_AM :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('hour expected');
                break;

            case Zend_Date::HOUR_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('hour expected');
                break;

            case Zend_Date::HOUR_AM :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('hour expected');
                break;

            case Zend_Date::HOUR :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(intval($date), 0, 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour,         0, 0, 1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('hour expected');
                break;

            case Zend_Date::MINUTE :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, intval($date), 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, $minute,       0, 1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('minute expected');
                break;

            case Zend_Date::SECOND :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, intval($date), 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, $second,       1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('second expected');
                break;

            case Zend_Date::MILLISECOND :
                if (is_numeric($date)) {
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
                }
                throw new Zend_Date_Exception('milliseconds expected');
                break;

            case Zend_Date::MINUTE_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, intval($date), 0, 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, $minute,       0, 1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('minute expected');
                break;

            case Zend_Date::SECOND_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, intval($date), 1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime(0, 0, $second,       1, 1, 1970, -1, $gmt));
                }
                throw new Zend_Date_Exception('second expected');
                break;


            // timezone formats
            // break intentionally omitted
            case Zend_Date::TIMEZONE_NAME :
            case Zend_Date::TIMEZONE :
            case Zend_Date::TIMEZONE_SECS :
                throw new Zend_Date_Exception('timezone not supported');
                break;

            case Zend_Date::DAYLIGHT :
                throw new Zend_Date_Exception('daylight not supported');
                break;

            case Zend_Date::GMT_DIFF :
            case Zend_Date::GMT_DIFF_SEP :
                throw new Zend_Date_Exception('gmtdiff not supported');
                break;


            // date strings
            case Zend_Date::ISO_8601 :

                $next = 0;
                if (preg_match('/-\d{4}-\d{2}-\d{2}/', $date, $datematch)) {
                    // -yyyy-mm-dd
                    $minus = true;
                    $result = array('Y' => 1, 'M' => 6, 'd' => 9);
                    $next = 11;
                } else if (preg_match('/\d{4}-\d{2}-\d{2}/', $date, $datematch)) {
                    // yyyy-mm-dd
                    $result = array('Y' => 0, 'M' => 5, 'd' => 8);
                    $next = 10;
                } else if (preg_match('/-\d{2}-\d{2}-\d{2}/', $date, $datematch)) {
                    // -yy-mm-dd
                    $minus = true;
                    $result = array('y' => 1, 'M' => 4, 'd' => 7);
                    $next = 9;
                } else if (preg_match('/\d{2}-\d{2}-\d{2}/', $date, $datematch)) {
                    // yy-mm-dd
                    $result = array('y' => 0, 'M' => 3, 'd' => 6);
                    $next = 8;
                } else if (preg_match('/-\d{8}/', $date, $datematch)) {
                    // -yyyymmdd
                    $minus = true;
                    $result = array('Y' => 1, 'M' => 5, 'd' => 7);
                    $next = 9;
                } else if (preg_match('/\d{8}/', $date, $datematch)) {
                    // yyyymmdd
                    $result = array('Y' => 0, 'M' => 4, 'd' => 6);
                    $next = 8;
                } else if (preg_match('/-\d{6}/', $date, $datematch)) {
                    // -yymmdd
                    $minus = true;
                    $result = array('y' => 1, 'M' => 3, 'd' => 5);
                    $next = 7;
                } else if (preg_match('/\d{6}/', $date, $datematch)) {
                    // yymmdd
                    $result = array('y' => 0, 'M' => 2, 'd' => 4);
                    $next = 6;
                }
                if (strlen($date) > $next) {
                    $date = substr($date, $next);
                    // Thh:mm:ss
                    if (preg_match('/[T,\s]{1}\d{2}:\d{2}:\d{2}/', $date, $timematch)) {
                        // Thh:mm:ss | _hh:mm:ss
                        $result['h'] = 1;
                        $result['m'] = 4;
                        $result['s'] = 7;
                        $next += 9;
                    } else if (preg_match('/\d{2}:\d{2}:\d{2}/', $date, $timematch)) {
                        // hh:mm:ss
                        $result['h'] = 0;
                        $result['m'] = 3;
                        $result['s'] = 6;
                        $next += 8;
                    } else if (preg_match('/[T,\s]{1}\d{2}\d{2}\d{2}/', $date, $timematch)) {
                        // Thhmmss | _hhmmss
                        $result['h'] = 1;
                        $result['m'] = 3;
                        $result['s'] = 5;
                        $next += 7;
                    } else if (preg_match('/\d{2}\d{2}\d{2}/', $date, $timematch)) {
                        // hhmmss | hhmmss
                        $result['h'] = 0;
                        $result['m'] = 2;
                        $result['s'] = 4;
                        $next += 6;
                    }
                }

                if (!isset($result)) {
                    throw new Zend_Date_Exception('unsupported ISO8601 format');
                }

                if(isset($result['M'])) {
                    if (isset($result['Y'])) {
                        $years = substr($datematch[0], $result['Y'], 4);
                        if (isset($minus)) {
                            $years = 0 - $years;
                        }
                    } else {
                        $years = substr($datematch[0], $result['y'], 2);
                        if (isset($minus)) {
                            $years = 0 - $years;
                        }
                        if ($years >= 0) {
                            $years += 1900;
                            if ($years < 1970)
                                $years += 100;
                        }
                    }
                    $months  = substr($datematch[0], $result['M'], 2);
                    $days    = substr($datematch[0], $result['d'], 2);
                } else {
                    $years  = 1970;
                    $months = 1;
                    $days   = 1;
                }
                if (isset($result['h'])) {
                    $hours   = substr($timematch[0], $result['h'], 2);
                    $minutes = substr($timematch[0], $result['m'], 2);
                    $seconds = substr($timematch[0], $result['s'], 2);
                } else {
                    $hours   = 0;
                    $minutes = 0;
                    $seconds = 0;
                }

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_2822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('no RFC 2822 format');
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::TIMESTAMP :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->_Date->getTimestamp());
                }
                throw new Zend_Date_Exception('timestamp expected');
                break;


            // additional formats
            // break intentionally omitted
            case Zend_Date::ERA :
            case Zend_Date::ERA_NAME :
                throw new Zend_Date_Exception('era not supported');
                break;

            case Zend_Date::DATES :
                try {
                    $parsed = Zend_Locale_Format::getDate($date, false, $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $parsed['month'],
                                                             1 + $parsed['day'], 1970 + $parsed['year'], -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::DATE_FULL :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $parsed['month'],
                                                             1 + $parsed['day'], 1970 + $parsed['year'], -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::DATE_LONG :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $parsed['month'],
                                                             1 + $parsed['day'], 1970 + $parsed['year'], -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::DATE_MEDIUM :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $parsed['month'],
                                                             1 + $parsed['day'], 1970 + $parsed['year'], -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::DATE_SHORT :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);
                    if ($parsed['year'] < 100) {
                        $parsed['year'] += 1900;
                        if ($parsed['year'] < 1970) {
                            $parsed['year'] += 100;
                        }
                    }

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->_Date->mktime(0, 0, 0, 1 + $parsed['month'],
                                                             1 + $parsed['day'], 1970 + $parsed['year'], -1, $gmt),
                                                 $this->_Date->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::TIMES :
                try {
                    $parsed = Zend_Locale_Format::getTime($date, false, $locale);
                    return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                             1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour, $minute, $second, 1, 1, 1970, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::TIME_FULL :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], 0,
                                                             1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour, $minute, $second, 1, 1, 1970, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::TIME_LONG :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                             1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour, $minute, $second, 1, 1, 1970, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::TIME_MEDIUM :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                                             1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour, $minute, $second, 1, 1, 1970, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            case Zend_Date::TIME_SHORT :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->_Date->mktime($parsed['hour'], $parsed['minute'], 0,
                                                             1, 1, 1970, -1, $gmt),
                                                 $this->_Date->mktime($hour, $minute, $second, 1, 1, 1970, -1, $gmt));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage());
                }
                break;

            // ATOM and RFC_3339 are identical
            case Zend_Date::ATOM :
            case Zend_Date::RFC_3339:
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('ATOM format expected');
                }

                $years   = substr($match[0], 0, 4);
                $months  = substr($match[0], 5, 2);
                $days    = substr($match[0], 8, 2);
                $hours   = substr($match[0], 11, 2);
                $minutes = substr($match[0], 14, 2);
                $seconds = substr($match[0], 17, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::COOKIE :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('COOKIE format expected');
                }
                $match[0] = substr($match[0], strpos($match[0], ' ')+1);

                $days    = substr($match[0], 0, 2);
                $months  = $this->getDigitFromName(substr($match[0], 3, 3));
                $years   = substr($match[0], 7, 4);
                $years  += 2000;
                $hours   = substr($match[0], 10, 2);
                $minutes = substr($match[0], 13, 2);
                $seconds = substr($match[0], 16, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('RFC 822 format expected');
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $years  += 2000;
                $hours   = substr($match[0], 15, 2);
                $minutes = substr($match[0], 18, 2);
                $seconds = substr($match[0], 21, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_850 :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('RFC 850 format expected');
                }

                $match[0] = substr($match[0], strpos($match[0], ' ')+1);

                $days    = substr($match[0], 0, 2);
                $months  = $this->getDigitFromName(substr($match[0], 3, 3));
                $years   = substr($match[0], 7, 4);
                $years  += 2000;
                $hours   = substr($match[0], 10, 2);
                $minutes = substr($match[0], 13, 2);
                $seconds = substr($match[0], 16, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_1036 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('RFC 1036 format expected');
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $years  += 2000;
                $hours   = substr($match[0], 15, 2);
                $minutes = substr($match[0], 18, 2);
                $seconds = substr($match[0], 21, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::RFC_1123 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('RFC 1123 format expected');
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::RSS :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('RSS format expected');
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            case Zend_Date::W3C :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception('W3C format expected');
                }

                $years   = substr($match[0], 0, 4);
                $months  = substr($match[0], 5, 2);
                $days    = substr($match[0], 8, 2);
                $hours   = substr($match[0], 11, 2);
                $minutes = substr($match[0], 14, 2);
                $seconds = substr($match[0], 17, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->_Date->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, -1, $gmt),
                                             $this->_Date->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  -1, $gmt));
                break;

            default :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->_Date->getTimestamp());
                }
                throw new Zend_Date_Exception('timestamp expected');
                break;
        }
    }


    /**
     * Returns TRUE when both date objects or dateparts are equal.
     * For example:
     * 15.May.2000 <-> 15.June.2000 Equals only for Day or Year... all other will return false 
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to equal with
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function equals($date, $part = NULL, $gmt = TRUE, $locale = NULL)
    {
        if ($this->compare($date, $part, $gmt, $locale) == 0) {
            return true;
        }
        return false;
    }


    /**
     * Returns if the given date or datepart is earlier
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for day, year and date, but not for month
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to compare with
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isEarlier($date, $part = NULL, $gmt = TRUE, $locale = NULL)
    {
        if ($this->compare($date, $part, $gmt, $locale) == -1) {
            return true;
        }
        return false;
    }


    /**
     * Returns if the given date or datepart is later
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for month but false for day, year and date
     * Returns if the given date is later
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to compare with
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isLater($date, $part = NULL, $gmt = TRUE, $locale = NULL)
    {
        if ($this->compare($date, $part, $gmt, $locale) == 1) {
            return true;
        }
        return false;
    }


    /**
     * Returns only the time of the date as new Zend_Date object
     * For example: 
     * 15.May.2000 10:11:23 will return a dateobject equal to 01.Jan.1970 10:11:23
     * 
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getTime($gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::TIME_MEDIUM, $gmt, $locale), Zend_Date::TIME_MEDIUM, $gmt, $locale);
    }


    /**
     * Returns the calculated time
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $time    OPTIONAL Time to calculate with, if null the actual time is taken
     * @param  string                    $format  OPTIONAL Timeformat for parsing
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    private function _time($calc, $time = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (is_object($time)) {
            // extract time from object
            $time = $time->get(Zend_Date::TIME_MEDIUM, $gmt, $locale);
        } else {
            if (empty($time)) {
                $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian','medium'));
                $format = $format['pattern'];
                $parsed['hour']   = $this->_Date->date('H', FALSE, $gmt);
                $parsed['minute'] = $this->_Date->date('m', FALSE, $gmt);
                $parsed['second'] = $this->_Date->date('s', FALSE, $gmt);
            } else {
                $parsed = Zend_Locale_Format::getTime($time, $format, $locale);
            }
            $time = new Zend_Date(0, Zend_Date::TIMESTAMP, TRUE, $locale);
            $time->set($parsed['hour'], Zend_Date::HOUR, $gmt);
            $time->set($parsed['minute'], Zend_Date::MINUTE, $gmt);
            $time->set($parsed['second'], Zend_Date::SECOND, $gmt);
            $time = $time->get(Zend_Date::TIME_MEDIUM, $gmt, $locale);
        }
        $return = $this->_calcdetail($calc, $time, Zend_Date::TIME_MEDIUM, TRUE, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp(), Zend_Date::TIMESTAMP, TRUE, $locale);
        }
        return $return;
    }


    /**
     * Sets a new time for the date object. Format defines how to parse the time string.
     * Also a complete date can be given, but only the time is used for setting.
     * For example: dd.MMMM.yyTHH:mm' and 'ss sec'-> 10.May.07T25:11 and 44 sec => 1h11min44sec + 1 day
     * Returned is the new date object and the existing date is left as it was before
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL Time to set, if null the actual time is set
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function setTime($time = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_time('set', $time, $format, $gmt, $locale);
    }


    /**
     * Adds a time to the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> +10 hours
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL Time to add, if null the actual time is added
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function addTime($time = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_time('add', $time, $format, $gmt, $locale);
    }


    /**
     * Substracts a time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> -10 hours
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL Time to sub, if null the actual time is substracted
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function subTime($time = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_time('sub', $time, $format, $gmt, $locale);
    }


    /**
     * Compares the time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to default.
     * If no format us given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> 10 hours
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL Time to compare, if null the actual time is compared
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTime($time = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_time('cmp', $time, $format, $gmt, $locale);
    }


    /**
     * Returns the date from the date object withot time. It is returned as new object.
     * For example: 10.May.2000 10:30:00 -> 10.May.2000 00:00:00
     * If only parts are given the other parts are set to default.
     * For example. 15.May.2000 10:20:30 -> MMMM.yyyy -> 01.May.2000 00:00:00 
     *
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDate($gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::DATE_MEDIUM, $gmt, $locale), Zend_Date::DATE_MEDIUM, $gmt, $locale);
    }


    /**
     * Returns the calculated date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $time    OPTIONAL Date to calculate with, if null the actual date is taken
     * @param  string                    $format  OPTIONAL Dateformat for parsing
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _date($calc, $date = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (is_object($date)) {
            // extract date from object
            $date = $date->get(Zend_Date::DATE_MEDIUM, $gmt, $locale);
        } else {
            if (empty($date)) {
                $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian','medium'));
                $format = $format['pattern'];
                $parsed['year']  = $this->_Date->date('Y', FALSE, $gmt);
                $parsed['month'] = $this->_Date->date('m', FALSE, $gmt);
                $parsed['day']   = $this->_Date->date('d', FALSE, $gmt);
            } else {
                $parsed = Zend_Locale_Format::getDate($date, $format, $locale);
            }
            $date = new Zend_Date(0, Zend_Date::TIMESTAMP, TRUE, $locale);
            $date->set($parsed['year'], Zend_Date::YEAR, $gmt);
            $date->set($parsed['month'], Zend_Date::MONTH_SHORT, $gmt);
            $date->set($parsed['day'], Zend_Date::DAY, $gmt);
            $date = $date->get(Zend_Date::DATE_MEDIUM, $gmt, $locale);
        }
        $return = $this->_calcdetail($calc, $date, Zend_Date::DATE_MEDIUM, TRUE, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp(), Zend_Date::TIMESTAMP, TRUE, $locale);
        }
        return $return;
    }


    /**
     * Sets a new date for the date object. Format defines how to parse the date string.
     * Also a complete date with time can be given, but only the date is used for setting.
     * For example: MMMM.yy HH:mm-> May.07 22:11 => 01.May.07 00:00
     * Returned is the new date object and the existing time is left as it was before
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL Date to set, if null the actual date is set
     * @param  string                    $format  OPTIONAL Dateformat for parsing
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setDate($date = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_date('set', $date, $format, $gmt, $locale);
    }


    /**
     * Adds a date to the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> +10 months
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL date to add, if null the actual date is added
     * @param  string                    $format  OPTIONAL Dateformat for parsing input
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addDate($date = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_date('add', $date, $format, $gmt, $locale);
    }


    /**
     * Substracts a date from the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> -10 months
     * Be aware: Substracting 2 months is not equal to Adding -2 months !!!
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL date to sub, if null the actual date is substracted
     * @param  string                    $format  OPTIONAL Dateformat for parsing input
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subDate($date = NULL, $format = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_date('sub', $date, $format, $gmt, $locale);
    }


    /**
     * Compares the date from the existing date object, ignoring the time. 
     * Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: 10.01.2000 => 10.02.1999 -> FALSE
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL date to compare, if null the actual date is compared
     * @param  string                    $format  OPTIONAL Dateformat for parsing input
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function compareDate($date = NULL, $format = NULL, $gmt = false, $locale = NULL)
    {
        return $this->_date('cmp', $date, $format, $gmt, $locale);
    }


    /**
     * Returns the full ISO 8601 date from the date object.
     * Always the complete ISO 8601 specifiction is used. If an other ISO date is needed
     * (ISO 8601 defines several formats) use toString() instead. 
     * This function does not return the ISO date as object. Use copy() instead. 
     *
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function getIso($gmt = FALSE, $locale = NULL)
    {
        return $this->get(Zend_Date::ISO_8601, $gmt, $locale);
    }


    /**
     * Returns the calculated ISO date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $time    OPTIONAL ISO date to calculate with, if null the actual date is taken
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _iso($calc, $iso = NULL, $gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (is_object($iso)) {
            // extract iso from object
            $iso = $iso->get(Zend_Date::ISO_8601, $gmt, $locale);
        } else if (empty($iso)) {
            $iso = $this->_Date->date('c', FALSE, $gmt);
        }
        $return = $this->_calcdetail($calc, $iso, Zend_Date::ISO_8601, TRUE, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp(), Zend_Date::TIMESTAMP, TRUE, $locale);
        }
        return $return;
    }


    /**
     * Sets a new date for the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> 01.Sept.2005 00:00:00, 20050201T10:00:30 -> 01.Feb.2005 10h00m30s
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL ISO Date to set, if null the actual date is set
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setIso($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_iso('set', $date, $gmt, $locale);
    }


    /**
     * Adds a ISO date to the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> + 01.Sept.2005 00:00:00, 10:00:00 -> +10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL ISO Date to add, if null the actual date is added
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addIso($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_iso('add', $date, $gmt, $locale);
    }


    /**
     * Substracts a ISO date from the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL ISO Date to sub, if null the actual date is substracted
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subIso($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_iso('sub', $date, $gmt, $locale);
    }


    /**
     * Compares a ISO date with the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL ISO Date to sub, if null the actual date is substracted
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareIso($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_iso('cmp', $date, $gmt, $locale);
    }


    /**
     * Returns a RFC 822 compilant datestring from the date object.
     * This function does not return the RFC date as object. Use copy() instead. 
     *
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     * Returns a RFC822 formatted date - RFC822 is locale-independent
     *
     * @param $gmt   boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale object - OPTIONAL locale for parsing input
     * @return string
     */
    public function getArpa($gmt = FALSE, $locale = NULL)
    {
        return $this->get(Zend_Date::RFC_822, $gmt, $locale);
    }


    /**
     * Returns the calculated RFC 822 date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $arpa    OPTIONAL RFC 822 date to calculate with, if null the actual date is taken
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _arpa($calc, $arpa = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (is_object($arpa)) {
            // extract arpa fromobject
            $arpa = $arpa->get(Zend_Date::RFC_822, $gmt, $locale);
        } else if (empty($arpa)) {
            $arpa = $this->_Date->date('D\, d M y H\:m\:s O', FALSE, $gmt);
        }
        $return = $this->_calcdetail($calc, $arpa, Zend_Date::RFC_822, TRUE, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp(), Zend_Date::TIMESTAMP, FALSE, $locale);
        }
        return $return;
    }


    /**
     * Sets a RFC 822 date as new date for the date object.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL RFC 822 to set, if null the actual date is set
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setArpa($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_arpa('set', $date, $gmt, $locale);
    }


    /**
     * Adds a RFC 822 date to the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL RFC 822 Date to add, if null the actual date is added
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addArpa($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_arpa('add', $date, $gmt, $locale);
    }


    /**
     * Substracts a RFC 822 date from the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL RFC 822 Date to sub, if null the actual date is substracted
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subArpa($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_arpa('sub', $date, $gmt, $locale);
    }


    /**
     * Compares a RFC 822 compilant date with the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL RFC 822 Date to sub, if null the actual date is substracted
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareArpa($date = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_arpa('cmp', $date, $gmt, $locale);
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
            throw new Zend_Date_Exception('Location must include \'longitude\' and \'latitude\'');
        }
        if (($location['longitude'] > 180) or ($location['longitude'] < -180)) {
            throw new Zend_Date_Exception('Longitude must be between -180 and 180');
        }
        if (($location['latitude'] > 90) or ($location['latitude'] < -90)) {
            throw new Zend_Date_Exception('Latitude must be between -90 and 90');
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
            case 'astronomic' :
                return -0.309017;
                break;
            default :
                return -0.0145439;
                break;
        }
    }


    /**
     * Returns the time of sunrise for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of sunrise
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunRise($location)
    {
        $horizon = $this->_checkLocation($location);
        return new Zend_Date($this->_Date->calcSun($location, $horizon, TRUE));
    }


    /**
     * Returns the time of sunset for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of sunset
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunSet($location)
    {
        $horizon = $this->_checkLocation($location);
        return new Zend_Date($this->_Date->calcSun($location, $horizon, FALSE));
    }


    /**
     * Returns an array with the sunset and sunrise dates for all horizon types
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of suninfo
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return array - [sunset|sunrise][effective|civil|nautic|astronomic]
     * @throws Zend_Date_Exception
     */
    public function getSunInfo($location)
    {
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
            $suninfo['sunrise'][$location['horizon']] = new Zend_Date($this->_Date->calcSun($location, $horizon, TRUE));
            $suninfo['sunset'][$location['horizon']]  = new Zend_Date($this->_Date->calcSun($location, $horizon, FALSE));
        }
        return $suninfo;
    }


    /**
     * Returns the actual set timezone 
     *
     * @return string
     */
    public function getTimeZone()
    {
        return date_default_timezone_get();
    }


    /**
     * Sets a new timezone, for a list of supported timezones look here: http://php.net/timezones
     *
     * @param $timezone string - timezone to set
     * @return boolean
     */
    public function setTimeZone($timezone)
    {
        return @date_default_timezone_set($timezone);
    }


    /**
     * Returns if the year of the date is a leap year
     *
     * @return boolean
     */
    public function isLeapYear()
    {
        return (bool) $this->get(Zend_Date::LEAPYEAR);
    }


    /**
     * Returns if the set date is todays date
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
     * Returns if the set date is yesterdays date
     *
     * @return boolean
     */
    public function isYesterday()
    {
        $today = $this->_Date->date('Ymd');
        $day   = $this->_Date->date('Ymd',$this->_Date->getTimestamp());
        return (((int) $today - (int) $day) == 1);
    }


    /**
     * Returns if the set date is tomorrows date
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
     * Returns the actual date as new date object
     *
     * @return Zend_Date
     */
    public function now()
    {
        return new Zend_Date('',$this->_Locale);
    }


    /**
     * Calculate date details
     * 
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $date    Date or Part to calculate
     * @param  string                    $part    Datepart for Calculation
     * @param  boolean                   $gmt     TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|string  new date
     * @throws Zend_Date_Exception
     */
    private function _calcdetail($calc, $date, $type, $gmt, $locale)
    {
        switch($calc) {
            case 'set' :
                return $this->set($date, $type, $gmt, $locale);
                break;
            case 'add' :
                return $this->add($date, $type, $gmt, $locale);
                break;
            case 'sub' :
                return $this->sub($date, $type, $gmt, $locale);
                break;
        }
        return $this->compare($date, $type, $gmt, $locale);
    }

    /**
     * Returns only the year from the date object as new object.
     * For example: 10.May.2000 10:30:00 -> 01.Jan.2000 00:00:00
     *
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getYear($gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::YEAR, $gmt, $locale), Zend_Date::YEAR, $gmt, $locale);
    }

    /**
     * Returns the calculated date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $year    OPTIONAL Year to calculate with, if null the actual year is taken
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _year($calc, $year = NULL, $gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        if (is_object($year)) {
            // extract year from object
            $year = $date->get(Zend_Date::YEAR, $gmt, $locale);
        } else if (empty($year)) {
            $year = $this->_Date->date('Y', FALSE, $gmt);
        }
        $return = $this->_calcdetail($calc, $year, Zend_Date::YEAR, TRUE, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp(), Zend_Date::TIMESTAMP, TRUE, $locale);
        }
        return $return;
    }


    /**
     * Sets a new year
     * If the year is between 0 and 69, 2000 will be set (2000-2069)
     * If the year if between 70 and 99, 1999 will be set (1970-1999)
     * 3 or 4 digit years are set as expected. If you need to set year 0-99
     * use set() instead. 
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL Year to set, if null the actual year is set
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setYear($year = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_year('set', $year, $gmt, $locale);
    }


    /**
     * Adds the year to the existing date object
     * If the year is between 0 and 69, 2000 will be added (2000-2069)
     * If the year if between 70 and 99, 1999 will be added (1970-1999)
     * 3 or 4 digit years are added as expected. If you need to add years from 0-99
     * use add() instead. 
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL Year to add, if null the actual year is added
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addYear($year = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_year('add', $year, $gmt, $locale);
    }


    /**
     * Subs the year from the existing date object
     * If the year is between 0 and 69, 2000 will be substracted (2000-2069)
     * If the year if between 70 and 99, 1999 will be substracted (1970-1999)
     * 3 or 4 digit years are substracted as expected. If you need to substract years from 0-99
     * use sub() instead. 
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    OPTIONAL Year to sub, if null the actual year is substracted
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subYear($year = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_year('sub', $year, $gmt, $locale);
    }


    /**
     * Compares the year with the existing date object, ignoring other date parts. 
     * For example: 10.03.2000 -> 15.02.2000 -> TRUE
     *
     * @param  string|integer|Zend_Date  $time    OPTIONAL Year to compare, if null the actual year is compared
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function compareYear($year = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_year('cmp', $year, $gmt, $locale);
    }


    /**
     * Returns the month
     *
     * @param $gmt    boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale object  - OPTIONAL locale for parsing input
     * @return object
     */
    public function getMonth($gmt = FALSE, $locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::MONTH_SHORT, $gmt, $locale), Zend_Date::MONTH_SHORT, $gmt, $locale);
    }


    /**
     * Returns the calculated month
     *
     * @param $calc string   - type of calculation to make
     * @param $month mixed   - OPTIONAL time to calculate, when null the actual time is calculated
     * @param $gmt boolean   - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale object - OPTIONAL locale for parsing input
     * @return object
     */
    private function _month($calc, $month = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        if (is_object($month)) {
            // extract month from object
            $found = $date->get(Zend_Date::MONTH_DIGIT, $gmt, $locale);
        } else {
            if (empty($month)) {
                $found = $this->_Date->date('m', false, $gmt);
            } else {
                if (is_numeric($month)) {
                    $found = $month;
                } else {
                    $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
                    $monthlist2 = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));

                    $monthlist = array_merge($monthlist, $monthlist2);
                    $found = 0;
                    foreach ($monthlist as $key => $value) {
                        if (strtoupper($value) == strtoupper($month)) {
                            $found = $key + 1;
                            break;
                        }
                        ++$cnt;
                    }
                    if ($found == 0) {
                        foreach ($monthlist2 as $key => $value) {
                            if (strtoupper(substr($value, 0, 1)) == strtoupper($month)) {
                                $found = $key + 1;
                                break;
                            }
                            ++$cnt;
                        }
                    }
                }
            }
        }
        if ($found == 0) {
            throw new Zend_Date_Exception('unknown month name');
        }
        $return = $this->_calcdetail($calc, $found, Zend_Date::MONTH_DIGIT, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp());
        }
        return $return;
    }


    /**
     * Sets a new month
     *
     * @param $month mixed   - OPTIONAL month to set, when null the actual month is set
     * @param $gmt   boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale object - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMonth($month = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_month('set', $month, $gmt, $locale);
    }


    /**
     * Adds a month
     *
     * @param $month mixed   - OPTIONAL month to add, when null the actual month is add
     * @param $gmt   boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale object - OPTIONAL locale for parsing input
     * @return object
     */
    public function addMonth($month = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_month('add', $month, $gmt, $locale);
    }


    /**
     * Substracts a month
     *
     * @param $month mixed   - OPTIONAL month to sub, when null the actual month is sub
     * @param $gmt  boolean  - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale object - OPTIONAL locale for parsing input
     * @return object
     */
    public function subMonth($month = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_month('sub', $month, $gmt, $locale);
    }


    /**
     * Compares only the month part, returning the difference
     *
     * @param $month mixed   - OPTIONAL month to compare, when null the actual month is compared
     * @param $gmt   boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale object - OPTIONAL locale for parsing input
     * @return integer
     */
    public function compareMonth($month = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_month('cmp', $month, $gmt ,$locale);
    }


    /**
     * Returns the day
     *
     * @param $gmt     boolean             OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param $locale  string|Zend_Locale  OPTIONAL locale for parsing input
     * @return Zend_Date
     */
    public function getDay($gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::DAY_SHORT, $gmt, $locale), Zend_Date::DAY_SHORT, $gmt, $locale);
    }


    /**
     * Returns the calculated day
     *
     * @param $calc    string                    Type of calculation to make
     * @param $day     string|integer|Zend_Date  OPTIONAL Day to calculate, when null the actual day is calculated
     * @param $gmt     boolean                   OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param $locale  string|Zend_Locale        OPTIONAL locale for parsing input
     * @return Zend_Date|integer
     */
    private function _day($calc, $day = NULL, $gmt = FALSE, $locale = NULL)
    {
        if (is_object($day)) {
            $day = $day->get(Zend_Date::DAY_SHORT, FALSE, $locale);
        } else if (empty($day)) {
            $day = $this->_Date->date('j');
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
        $return = $this->_calcdetail($calc, $day, $type, $gmt, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp());
        }
        return $return;
    }


    /**
     * Sets a new day
     *
     * @param $day     string|integer|Zend_Date  OPTIONAL day to set, when null the actual day is set
     * @param $gmt     boolean                   OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param $locale  string|Zend_Locale        OPTIONAL locale for parsing input
     * @return Zend_Date
     */
    public function setDay($day = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_day('set', $day, $gmt, $locale);
    }


    /**
     * Adds a day
     *
     * @param $day     string|integer|Zend_Date  OPTIONAL day to add, when null the actual day is added
     * @param $gmt     boolean                   OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param $locale  string|Zend_Locale        OPTIONAL locale for parsing input
     * @return Zend_Date
     */
    public function addDay($day = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_day('add', $day, $gmt, $locale);
    }


    /**
     * Substracts a day
     *
     * @param $day     string|integer|Zend_Date  OPTIONAL day to sub, when null the actual day is substracted
     * @param $gmt     boolean                   OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param $locale  string|Zend_Locale        OPTIONAL locale for parsing input
     * @return Zend_Date
     */
    public function subDay($day = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_day('sub', $day, $gmt, $locale);
    }


    /**
     * Compares only the day part, returning the difference
     *
     * @param $day     string|integer|Zend_Date  OPTIONAL day to compare, when null the actual day is compared
     * @param $gmt     boolean                   OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param $locale  string|Zend_Locale        OPTIONAL locale for parsing input
     * @return integer                           0 = equal, 1 = later, -1 = earlier
     */
    public function compareDay($day = NULL, $locale = NULL)
    {
        return $this->_day('cmp', $day, $gmt, $locale);
    }


    /**
     * Returns the weekday
     *
     * @param $gmt    boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getWeekday($gmt = FALSE, $locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::WEEKDAY, $gmt, $locale), Zend_Date::WEEKDAY, $gmt, $locale);
    }


    /**
     * Returns the calculated weekday
     *
     * @param $weekday string  - OPTIONAL weekday to calculate, when null the actual weekday is calculate
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _weekday($weekday, $locale, $calc)
    {
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

        return $this->_calcdetail($calc, $weekday, $type, $gmt, $locale);
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
        return $this->_weekday('cmp', $weekday, $gmt, $locale);
    }


    /**
     * Returns the day of year
     *
     * @param $gmt    boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale locale   - OPTIONAL locale for parsing
     * @return object
     */
    public function getDayOfYear($gmt = FALSE, $locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::DAY_OF_YEAR, $gmt, $locale), Zend_Date::DAY_OF_YEAR, $gmt, $locale);
    }


    /**
     * Returns the calculated dayofyear
     *
     * @param $day string     - OPTIONAL dayofyear to calculate, when null the actual dayofyear is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _dayOfYear($day, $locale, $calc)
    {
        if (empty($day)) {
            $day = $this->_Date->date('z');
        } else if (is_object($day)) {
            $day = $day->get(Zend_Date::DAY_OF_YEAR, FALSE, $locale);
        }

        return $this->_calcdetail($calc, $day, Zend_Date::DAY_OF_YEAR, $gmt, $locale);
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
        return $this->_dayOfYear('cmp', $day, $gmt, $locale);
    }


    /**
     * Returns the hour
     *
     * @param $gmt    boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getHour($gmt = FALSE, $locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::HOUR, $gmt, $locale), Zend_Date::HOUR, $gmt, $locale);
    }


    /**
     * Returns the calculated hour
     *
     * @param $hour string    - OPTIONAL hour to calculate, when null the actual hour is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _hour($hour, $locale, $calc)
    {
        if (empty($hour)) {
            $hour = $this->_Date->date('G');
        } else if (is_object($hour)) {
            $hour = $hour->get(Zend_Date::HOUR_SHORT, FALSE, $locale);
        }

        return $this->_calcdetail($calc, $hour, Zend_Date::HOUR_SHORT, $gmt, $locale);
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
        return $this->_hour('cmp', $hour, $gmt, $locale);
    }


    /**
     * Returns the minute
     *
     * @param $gmt    boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getMinute($gmt = FALSE, $locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::MINUTE, $gmt, $locale), Zend_Date::MINUTE, $gmt, $locale);
    }


    /**
     * Returns the calculated minute
     *
     * @param $minute string    - OPTIONAL minute to calculate, when null the actual minute is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _minute($minute, $locale, $calc)
    {
        if (empty($minute)) {
            $minute = $this->_Date->date('i');
        } else if (is_object($minute)) {
            $minute = $minute->get(Zend_Date::MINUTE_SHORT, FALSE, $locale);
        }

        return $this->_calcdetail($calc, $minute, Zend_Date::MINUTE_SHORT, $gmt, $locale);
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
     * Returns the second
     *
     * @param $gmt    boolean - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getSecond($gmt = FALSE, $locale = FALSE)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::SECOND, $gmt, $locale), Zend_Date::SECOND, $gmt, $locale);
    }


    /**
     * Returns the calculated second
     *
     * @param $second string  - OPTIONAL second to calculate, when null the actual second is calculated
     * @param $locale string  - OPTIONAL locale for parsing input
     * @param $calculation string - type of calculation to make
     * @return object
     */
    private function _second($calc, $second, $gmt, $locale)
    {
        if (empty($second)) {
            $second = $this->_Date->date('s');
        } else if (is_object($second)) {
            $second = $second->get(Zend_Date::SECOND_SHORT, FALSE, $locale);
        }

        return $this->_calcdetail($calc, $second, Zend_Date::SECOND_SHORT, $gmt, $locale);
    }


    /**
     * Sets a new second
     *
     * @param $second object  - OPTIONAL second to set, when null the actual second is set
     * @param $gmt           - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale locale  - OPTIONAL locale for parsing input
     * @return object
     */
    public function setSecond($second = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_second('set', $second, $gmt, $locale);
    }


    /**
     * Adds a second
     *
     * @param $second object - OPTIONAL second to add, when null the actual second is add
     * @param $gmt           - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale locale - OPTIONAL locale for parsing input
     * @return object
     */
    public function addSecond($second = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_second('add', $second, $gmt, $locale);
    }


    /**
     * Substracts seconds
     *
     * @param $second object - OPTIONAL second to sub, when null the actual second is sub'ed
     * @param $gmt           - OPTIONAL, TRUE = UTC time, FALSE = actual time zone
     * @param $locale locale - OPTIONAL locale for parsing input
     * @return object
     */
    public function subSecond($second = FALSE, $gmt = FALSE, $locale = FALSE)
    {
        return $this->_second('sub', $second, $gmt, $locale);
    }


    /**
     * Compares only the second part, returning the difference
     *
     * @param  integer|Zend_Date   $second  OPTIONAL Second to compare, when null the actual second is compared
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareSecond($second = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_second('cmp', $second, $gmt, $locale);
    }


    /**
     * Gets the precision for fractional seconds
     *
     * @return integer
     */
    public function getFractionalPrecision()
    {
        return $this->_Precision;
    }


    /**
     * Sets the precision for fractional seconds
     *
     * @param  integer  $precision  Precision for the fractional datepart 3 = milliseconds
     * @throws Zend_Date_Exception
     */
    public function setFractionalPrecision($precision = 3)
    {
        if (!intval($precision) or ($precision < 0)) {
            throw new Zend_Date_Exception('precision must be positive');
        }
        $this->_Precision = $precision;
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
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to set, when null the actual millisecond is set
     * @return integer
     */
    public function setMilliSecond($milli = NULL)
    {
        if ($milli === FALSE) {
            $milli = 0;
        }
        $this->subMilliSecond($this->_Fractional);
        $this->addMilliSecond($milli);
        return $this->_Fractional;
    }


    /**
     * Adds a millisecond
     *
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to add, when null the actual millisecond is added
     * @return integer
     */
    public function addMilliSecond($milli = FALSE)
    {
        if ($milli === FALSE) {
            $milli = 0;
        }
        $this->_Fractional = bcadd($this->_Fractional, $milli, 0);
        $max = bcpow(10, $this->_Precision, 0);
        if ($max < 10) {
            throw new Zend_Date_Exception('no fractional defined, use setFractionalPrecision');
        }

        if (bccomp($this->_Fractional, $max) == 1) {
            while (bccomp($this->_Fractional, $max) == 1) {
                $this->addSecond(1);
                $this->_Fractional = bcsub($this->_Fractional, $max);
            }
        } else if (bccomp($this->_Fractional, 0) == -1) {
            while (bccomp($this->_Fractional, 0) == -1) {
                $this->subSecond(1);
                $this->_Fractional = bcadd($this->_Fractional, $max);
            }
        }
        return $this->_Fractional;
    }


    /**
     * Substracts a millisecond
     *
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to sub, when null the actual millisecond is subtracted
     * @return integer
     */
    public function subMilliSecond($milli = NULL)
    {
        $milli = bcsub(0, $milli);
        return $this->addMilliSecond($milli);
    }


    /**
     * Compares only the millisecond part, returning the difference
     *
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to compare, when null the actual millisecond is compared
     * @return integer
     */
    public function compareMilliSecond($milli = NULL)
    {
        if (is_null($milli)) {
            $milli = 0;
        }
        $comp = $this->_Fractional - intval($milli);
        return $comp;
    }


    /**
     * Returns the week
     *
     * @param  boolean             $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getWeek($gmt = FALSE, $locale = NULL)
    {
        if (empty($locale)) {
            $locale = $this->_Locale;
        }

        return new Zend_Date($this->get(Zend_Date::WEEK, $gmt, $locale), Zend_Date::WEEK, $gmt, $locale);
    }


    /**
     * Returns the calculated week
     *
     * @param  string                    $calc    Type of calculation to make
     * @param  string|integer|Zend_Date  $week    OPTIONAL Week to calculate, when null the actual week is calculated
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _week($calc, $week, $gmt, $locale)
    {
        if (is_object($week)) {
            // extract week from object
            $week = $date->get(Zend_Date::WEEK, $gmt, $locale);
        } else if (empty($date)) {
            $week = $this->_Date->date('W', FALSE, $gmt);
        }
        $return = $this->_calcdetail($calc, $week, Zend_Date::WEEK, TRUE, $locale);
        if ($calc != 'cmp') {
            return new Zend_Date($this->_Date->getTimestamp(), Zend_Date::TIMESTAMP, FALSE, $locale);
        }
        return $return;
    }


    /**
     * Sets a new week
     *
     * @param  string|integer|Zend_Date  $week    OPTIONAL Week to set, when null the actual day is set
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function setWeek($week = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_week('set', $week, $gmt, $locale);
    }


    /**
     * Adds a week
     *
     * @param  string|integer|Zend_Date  $week    OPTIONAL Week to add, when null the actual day is added
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function addWeek($week = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_week('add', $week, $gmt, $locale);
    }


    /**
     * Substracts a week
     *
     * @param  string|integer|Zend_Date  $week    OPTIONAL Week to compare, when null the actual day is compared
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function subWeek($week = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_week('sub', $week, $gmt, $locale);
    }


    /**
     * Compares only the week part, returning the difference
     *
     * @param  string|integer|Zend_Date  $week    OPTIONAL Week to compare, when null the actual day is compared
     * @param  boolean                   $gmt     OPTIONAL TRUE = UTC time, FALSE = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareWeek($week = NULL, $gmt = FALSE, $locale = NULL)
    {
        return $this->_week('cmp', $week, $gmt, $locale);
    }
}
