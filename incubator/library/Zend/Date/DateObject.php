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
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend_Date
 * @subpackage Zend_Date_DateObject
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date_DateObject {


    /**
     * UNIX Timestamp
     */
    private $_unixtimestamp;


    /**
     * Table of Monthdays
     */
    private $_monthTable = array(31,28,31,30,31,30,31,31,30,31,30,31);


    /**
     * Table of Years
     */
    private $_yearTable = array(
        1970 => 0,            1960 => -315619200,   1950 => -631152000,
        1940 => -946771200,   1930 => -1262304000,  1920 => -1577923200,
        1910 => -1893456000,  1900 => -2208988800,  1890 => -2524521600,
        1880 => -2840140800,  1870 => -3155673600,  1860 => -3471292800,
        1850 => -3786825600,  1840 => -4102444800,  1830 => -4417977600,
        1820 => -4733596800,  1810 => -5049129600,  1800 => -5364662400,
        1790 => -5680195200,  1780 => -5995814400,  1770 => -6311347200,
        1760 => -6626966400,  1750 => -6942499200,  1740 => -7258118400,
        1730 => -7573651200,  1720 => -7889270400,  1710 => -8204803200,
        1700 => -8520336000,  1690 => -8835868800,  1680 => -9151488000,
        1670 => -9467020800,  1660 => -9782640000,  1650 => -10098172800,
        1640 => -10413792000, 1630 => -10729324800, 1620 => -11044944000,
        1610 => -11360476800, 1600 => -11676096000);


    /**
     * active timezone
     */
    private $_timezone = false;


    /**
     * Generates the standard date object
     * must be given as Timestamp
     * This object simulates the PHP 5.2 Date Object
     *
     * @param $date mixed - timestamp number
     */
    public function __construct($date)
    {
        $this->setTimestamp($date);
    }


    /**
     * Sets a new timestamp
     *
     * @param $date mixed - timestamp number
     */
    public function setTimestamp($date)
    {
        // no date value, take actual time
        if (empty($date))
        {
            $this->_unixtimestamp = time();
            return true;
        }

        if (is_numeric($date))
        {
          $this->_unixtimestamp = $date;
          return true;
        }

        $this->throwException('"'.$date.'" is no valid date');
    }


    /**
     * Returns unix timestamp
     *
     * @return  timestamp
     */
    public function getTimestamp()
    {
        return $this->_unixtimestamp;
    }


    /**
     * Internal mktime function
     * for handling 64bit timestamps
     *
     * Returns a timestamp to a given GMT/UTC time
     * Summer/winter time is only supported for 32bit timestamps
     * Year has to be 4 digits otherwise it would be recognised as
     * year 70 AD instead of 1970 AD as expected !!
     *
     * @param $hour   number  - hour
     * @param $minute number  - minute
     * @param $second number  - second
     * @param $month  number  - month
     * @param $day    number  - day
     * @param $year   number  - year
     * @param $dst    boolean - summer/wintertime
     * @param $gmt    boolean - timezone
     */
    public function mktime($hour, $minute, $second, $month = false, $day = false, $year = false, $dst= false, $gmt = false)
    {
        // only time - use PHP internal
        if ($month === false)
            return $gmt ? @gmmktime($hour, $minute, $second) : @mktime($hour, $minute, $second);

        // complete date but in 32bit timestamp - use PHP internal
        if ((1901 < $year) and ($year < 2038))
            return $gmt ? @gmmktime($hour, $minute, $second, $month, $day, $year, $dst) : @mktime($hour, $minute, $second, $month, $day, $year, $dst);

        // after here we are handling 64bit timestamps

        // get difference fronm local to gmt
        $difference = ($gmt) ? 0 : $this->_gmtDifference();

        // date to integer
        $day   = intval($day);
        $month = intval($month);
        $year  = intval($year);

        // correct months > 12 and months < 1
        if ($month > 12)
        {
            $overlap = floor($month / 12);
            $year   += $overlap;
            $month  -= $overlap*12;
        } else {
            $overlap = ceil((1-$month) / 12);
            $year   -= $overlap;
            $month  += $overlap*12;
        }

        $date = 0;
        if ($year >= 1970)
        {

            // Date is after UNIX epoch
            // go through leapyears
            // add months from letest given year
            for ($count = 1970; $count <= $year; $count++)
            {
                $leapyear = $this->isLeapYear($count);
                if ($count < $year)
                {
                    $date += 365;
                    if ($leapyear == true)
                        $date++;
                } else {
                    for ($mcount = 0; $mcount < ($month -1); $mcount++)
                    {
                        $date += $this->_monthTable[$mcount];
                        if (($leapyear == true) and ($month == 1))
                            $date++;
                    }
                }
            }

            $date += $day-1;

            return (($date * 86400) + ($hour * 3600) + ($minute * 60) + $second + $difference);
        } else {

            // Date is after UNIX epoch
            // go through leapyears
            // add months from letest given year
            for ($count = 1969; $count >= $year; $count--)
            {
                $leapyear = $this->isLeapYear($count);
                if ($count > $year)
                {
                    $date += 365;
                    if ($leapyear == true)
                        $date++;
                } else {
                    for ($mcount = 11; $mcount > ($month-1); $mcount--)
                    {
                        $date += $this->_monthTable[$mcount];
                        if (($leapyear == true) and ($month == 1))
                            $date++;
                    }
                }
            }

            $date += ($this->_monthTable[$mcount] - $day);
            $date = -(($date*86400) + (86400 - (($hour * 3600) + ($minute * 60 + $second))) - $difference);

            // gregorian correction for 5.Oct.1582
            if ($date < -12220185600)
            {
                $date += 864000;
            } else if ($date < -12219321600) {
                $date  = -12219321600;
            }

            return $date;
        }
    }


    /**
     * Returns the difference from local time to GMT
     *
     * @return  integer
     */
    private function _gmtDifference()
    {
        if ($this->_timezone !== false)
            return $this->_timezone;

        $this->_timezone = mktime(0,0,0,1,2,1970,0) - gmmktime(0,0,0,1,2,1970,0);
        return $this->_timezone;
    }


    /**
     * Returns true if given date is a leap year
     *
     * @param $year  integer
     * @return boolean - true if year is leap year
     */
    public function isLeapYear($year)
    {
        // all leapyears can be devided through 4
        if (($year % 4) != 0)
            return false;

        // all leapyears can be devided through 400 
        if ($year % 400 == 0)
        {
            return true;
        } else if (($year > 1582) and ($year % 100 == 0)) {
            return false;
        }

        return true;
    }


    /**
     * Internal date function
     * for handling 64bit timestamps
     *
     * Returns a formatted date for a timestamp
     * Cannot handle daylight savings
     *
     * @param $format     string - format for output
     * @param $timestamp  mixed
     * @param $gmt        boolean - timezone
     * @return  string
     */
    public function date($format, $timestamp = false, $gmt = false)
    {
        if ($timestamp === false)
            return ($gmt) ? @gmdate($format) : @date($format);

        if (abs($timestamp) <= 0x7FFFFFFF)
            return ($gmt) ? @gmdate($format, $timestamp) : @date($format, $timestamp);

        $date = $this->getDate($timestamp, true, $gmt);
        
        $length = strlen($format);
        $output = '';
        
        for ($i = 0; $i < $length; $i++)
        {
            switch($format[$i])
            {
                // day formats
                case 'd':  // day of month, 2 digits, with leading zero, 01 - 31
                    $output .= (($date['mday'] < 10) ? '0'.$date['mday'] : $date['mday']);
                    break;
                case 'D':  // day of week, 3 letters, Mon - Sun
                    $output .= gmdate('D', 86400*(3+$this->dayOfWeek($date['year'], $date['mon'], $date['mday'])));
                    break;
                case 'j':  // day of month, without leading zero, 1 - 31
                    $output .= $date['mday'];
                    break;
                case 'l':  // day of week, full string name, Sunday - Saturday
                    $output .= gmdate('l', 86400*(3+$this->dayOfWeek($date['year'], $date['mon'], $date['mday'])));
                    break;
                case 'N':  // ISO 8601 numeric day of week, 1 - 7
                    $output .= ($this->dayOfWeek($date['year'], $date['mon'], $date['mday']) + 1);
                    break;
                case 'S':  // english suffix for day of month, st nd rd th
                    if (($date['mday'] % 10) == 1)
                    {
                        $output .= 'st';
                    } else if ((($date['mday'] % 10) == 2) and ($date['mday'] != 12)) {
                        $output .= 'nd';
                    } else if (($date['mday'] % 10) == 3) {
                        $output .= 'rd';
                    } else {
                        $output .= 'th';
                    }
                    break;
                case 'w':  // numeric day of week, 0 - 6
                    $output .= $this->dayOfWeek($date['year'], $date['mon'], $date['mday']);
                    break;
                case 'z':  // day of year, 0 - 365
                    $output .= $date['yday'];
                    break;

                // week formats
                case 'W':  // ISO 8601, week number of year
                    $output .= $this->weekNumber($date['year'], $date['mon'], $date['mday']);
                    break;

                // month formats
                case 'F':  // string month name, january - december
                    $output .= date('F',mktime(0, 0, 0, $date['mon'], 2, 1971));
                    break;
                case 'm':  // number of month, with leading zeros, 01 - 12
                    $output .= (($date['mon'] < 10) ? '0'.$date['mon'] : $date['mon']);
                    break;
                case 'M':  // 3 letter month name, Jan - Dec
                    $output .= date('M',mktime(0, 0, 0, $date['mon'], 2, 1971));
                    break;
                case 'n':  // number of month, without leading zeros, 1 - 12
                    $output .= $date['mon'];
                    break;
                case 't':  // number of day in month
                    $output .= $date['ndays'];
                    break;

                // year formats
                case 'L':  // is leap year ?
                    $output .= $date['leap'] ? '1' : '0';
                    break;
                case 'o':  // ISO 8601 year number
                    $firstday = $this->dayOfWeek($date['year'], 1, 1);
                    if (($date['mon'] == 1) and (3 < $firstday) and ($firstday < (8 - $date['day'])))
                        $output .= ($date['year'] - 1);
                    else
                        $output .= $date['year'];
                    break;
                case 'Y':  // year number, 4 digits
                    $output .= $date['year'];
                    break;
                case 'y':  // year number, 2 digits
                    $output .= substr($date['year'], strlen($date['year'])-2, 2);
                    break;

                // time formats
                case 'a':  // lower case am/pm
                    $output .= (($date['hours'] >= 12) ? 'pm' : 'am');
                    break;
                case 'A':  // upper case am/pm
                    $output .= (($date['hours'] >= 12) ? 'PM' : 'AM');
                    break;
                case 'B':  // swatch internet time
                    // TODO: add format Swatch Internet Time
                    break;
                case 'g':  // hours without leading zeros, 12h format
                    if ($date['hours'] > 12)
                    {
                        $hour = $date['hours'] - 12;
                    } else {
                        if ($date['hours'] == 0)
                        {
                            $hour = '12';
                        } else {
                            $hour = $date['hours'];
                        }
                    }
                    
                    $output .= $hour;
                    break;
                case 'G':  // hours without leading zeros, 24h format
                    $output .= $date['hours'];
                    break;
                case 'h':  // hours with leading zeros, 12h format
                    if ($date['hours'] > 12)
                    {
                        $hour = $date['hours'] - 12;
                    } else {
                        if ($date['hours'] == 0)
                        {
                            $hour = '12';
                        } else {
                            $hour = $date['hours'];
                        }
                    }

                    $output .= (($hour < 10) ? '0'.$hour : $hour);
                    break;
                case 'H':  // hours with leading zeros, 24h format
                    $output .= (($date['hours'] < 10) ? '0'.$date['hours'] : $date['hours']);
                    break;
                case 'i':  // minutes with leading zeros
                    $output .= (($date['minutes'] < 10) ? '0'.$date['minutes'] : $date['minutes']);
                    break;
                case 's':  // seconds with leading zeros
                    $output .= (($date['seconds'] < 10) ? '0'.$date['seconds'] : $date['seconds']);
                    break;

                // timezone formats
                case 'e':  // timezone identifier
                    // TODO: add format timezone identifier, UTC, GMT 
                    break;
                case 'I':  // daylight saving time or not
                    // TODO: add format is in daylight saving time
                    break;
                case 'O':  // difference to GMT in hours
                    $gmt = ($gmt) ? 0 : -$this->_gmtDifference();
                    $output .= sprintf('%s%04d', ($gmt < 0) ? '+' : '-', abs($gmt)/36);
                    break;
                case 'P':  // difference to GMT with colon
                    $gmt = ($gmt) ? 0 : -$this->_gmtDifference();
                    $gmt = sprintf('%s%04d', ($gmt < 0) ? '+' : '-', abs($gmt)/36);
                    $output = $output.substr($gmt,0,3).':'.substr($gmt,3);
                    break;
                case 'T':  // timezone settings
                    $output .= date('T');
                    break;
                case 'Z':  // timezone offset in seconds
                    $output .= ($gmt) ? 0 : -$this->_gmtDifference();
                    break;

                // complete time formats
                case 'c':  // ISO 8601 date format
                    // TODO: add format ISO 8601 complete date
                    break;
                case 'r':  // RFC 2822 date format
                    $difference = $this->_gmtDifference();
                    $output .= gmdate('D',86400*(3+$this->dayOfWeek($date['year'], $date['mon'], $date['mday']))).
                               ', '.(($date['mday'] < 10) ? '0'.$date['mday'] : $date['mday']).
                               ' '.date('M',mktime(0, 0, 0, $date['mon'], 2, 1971)).
                               ' '.$date['year'].
                               ' '.(($date['hours'] < 10) ? '0'.$date['hours'] : $date['hours']).
                               ':'.(($date['minutes'] < 10) ? '0'.$date['minutes'] : $date['minutes']).
                               ':'.(($date['seconds'] < 10) ? '0'.$date['seconds'] : $date['seconds']).
                               ' '.sprintf('%s%04d',($difference < 0) ? '+' : '-', abs($difference)/36);
                    break;
                case 'U':  // Unix timestamp
                    $output .= $timestamp;
                    break;

                // special formats
                case "\\":  // next letter to print with no format
                    $i++;
                    if ($i < $length)
                        $output .= $format[$i];
                    break;
                default:  // letter is no format so add it direct
                    $output .= $format[$i];
                    break;
            }
            
            return $output;
        }
    }


    /**
     * Returns the day of week
     * 0 = sunday, 6 = saturday
     *
     * @param $year  integer
     * @param $month integer
     * @param $day   integer
     * @return dayOfWeek
     */
    public function dayOfWeek($year, $month, $day)
    {
        // gregorian correction
        $correction = 0;
        if (($year < 1582) or (($year == 1582) and (($month < 10) or (($month == 10) && ($day < 15)))))
            $correction = 3;

        if ($month > 2)
        {
            $month -= 2;
        } else {
            $month += 10;
            $year--;
        }

        $day = floor((13 * $month - 1) / 5) +
               $day + ($year % 100) +
               floor(($year % 100) / 4) +
               floor(($year / 100) / 4) - 2 *
               floor($year / 100) + 77 + $correction;

        return $day - 7 * floor($day / 7);
    }


    /**
     * Internal getDate function
     * for handling 64bit timestamps
     *
     * Returns an array with date parts to a given GMT/UTC timestamp
     *
     * $all defines is ALL date parts should be returned.
     * Default is false, so the function works faster
     *
     * @param  $timestamp  mixed
     * @param  $all        boolean
     * @param  $gmt        boolean - timezone
     * @return array
     */
    public function getDate($timestamp = false, $all = false, $gmt = false)
    {
        // actual timestamp
        if ($timestamp === false)
            return getdate();

        // 32bit timestamp
        if (abs($timestamp) <= 0x7FFFFFFF)
            return @getdate($timestamp);

        // timezone correction
        $gmttimestamp = $timestamp - ($gmt ? 0 : $this->_gmtDifference());

        // gregorian correction
        if ($gmttimestamp < -12219321600)
            $gmttimestamp -= 864000;

        // timestamp lower 0
        if ($gmttimestamp < 0)
        {
            if ($gmt)
                $timestamp = $gmttimestamp;

            $sec = 0;
            $act = 1970;
            // iterate through 10 years table, increasing speed
            foreach($this->_yearTable as $year => $seconds)
            {
                if ($gmttimestamp >= $seconds)
                {
                    $i = $act;
                    break;
                }
                $sec = $seconds;
                $act   = $year;
            }

            $gmttimestamp -= $sec;
            if (!isset($i))
                $i = $act;

            // iterate the max last 10 years
            for (; --$i >= 0; )
            {
                $day = $gmttimestamp;

                $gmttimestamp += 31536000;
                if ($leapyear = $this->isLeapYear($i))
                    $gmttimestamp += 86400;

                if ($gmttimestamp >= 0)
                {
                    $year = $i;
                    break;
                }
            }

            $secondsPerYear = 86400 * ($leapyear ? 366 : 365) + $day;

            $gmttimestamp = $day;
            // iterate through months
            for ($i = 12; --$i >= 0;)
            {
                $day = $gmttimestamp;

                $gmttimestamp += $this->_monthTable[$i] * 86400;
                if ($leapyear)
                    $gmttimestamp += 86400;
                if ($gmttimestamp >= 0)
                {
                    $month = $i;
                    $numday = $this->_monthTable[$i];
                    if ($leapyear)
                        ++$numday;
                    break;
                }
            }

            $gmttimestamp = $day;
            $numberdays = $numday + ceil(($gmttimestamp + 1) / 86400);

            $gmttimestamp += ($numday - $numberdays + 1) * 86400;
            $hours = floor($gmttimestamp / 3600);
        } else {
            // iterate through years
            for ($i = 1970;;$i++)
            {
                $day = $gmttimestamp;

                $gmttimestamp -= 31536000;
                if ($leapyear = $this->isLeapYear($i))
                    $gmttimestamp -= 86400;

                if ($gmttimestamp < 0)
                {
                    $year = $i;
                    break;
                }
            }

            $secondsPerYear = $day;

            $gmttimestamp = $day;
            // iterate through months
            for ($i = 0; $i <= 11; $i++)
            {
                $day = $gmttimestamp;
                $gmttimestamp -= $this->_monthTable[$i] * 86400;
                if ($leapyear)
                    $gmttimestamp -= 86400;
                if ($gmttimestamp < 0)
                {
                    $month = $i;
                    $numday = $this->_monthTable[$i];
                    if ($leapyear)
                        ++$numday;
                    break;
                }
            }

            $gmttimestamp = $day;
            $numberdays = ceil(($gmttimestamp + 1) / 86400);
            $gmttimestamp = $gmttimestamp - ($numberdays - 1) * 86400;
            $hours = floor($gmttimestamp / 3600); 
        }

        $gmttimestamp -= $hours * 3600;

        $minutes = floor($gmttimestamp / 60);
        $seconds = $gmttimestamp - $minutes * 60;

        if ($all)
        {
            return array(
                'seconds' => $seconds,
                'minutes' => $minutes,
                'hours'   => $hours,
                'mday'    => $numberdays,
                'mon'     => $month,
                'year'    => $year,
                'yday'    => floor($secondsPerYear / 86400),
                'leap'    => $leapyear,
                'ndays'   => $numdays
            );
        }

        $dayofweek = $this->dayOfWeek($year, $month, $numberdays);

        return array(
                'seconds' => $seconds,
                'minutes' => $minutes,
                'hours'   => $hours,
                'mday'    => $numberdays,
                'wday'    => $dayofweek,
                'mon'     => $month,
                'year'    => $year,
                'yday'    => floor($secondsPerYear / 86400),
                'weekday' => gmdate('l',86400*(3+$dayofweek)),
                'month'   => gmdate('F',mktime(0,0,0,$month,2,1971)),
                'leap'    => $leapyear,
                'ndays'   => $numdays,
                0         => $timestamp
        );
    }


    /**
     * Internal getWeekNumber function
     * for handling 64bit timestamps
     *
     * Returns the ISO 8601 week number of a given date
     *
     * @param  $year  integer
     * @param  $month integer
     * @param  $day   integer
     * @return integer
     */
    public function weekNumber($year, $month, $day)
        {
            $dayofweek = $this->dayOfWeek($year, $month, $day);
            $firstday = $this->dayOfWeek($year, 1, 1);
            if (($month == 1) and (3 < $firstday) and ($firstday < (8 - $day)))
            {
                $dayofweek = $firstday - 1;
                $firstday = $this->dayOfWeek($year - 1, 1, 1);
                $month = 12;
                $day = 31;
            } else if (($month == 12) and ((31 - $day) < $this->dayOfWeek($year + 1, 1, 1)) and
                       ($this->dayOfWeek($year + 1, 1, 1) < 4))
            {
                return 1;
            }
            
            return ($this->dayOfWeek($year, 1, 1) < 4) + 4 * ($month - 1) +
                   (2 * ($month - 1) + ($day - 1) + $firstday - $dayofweek + 6) * 36 / 256;
/*
            $a = intval((14-$month)/12);
            $y = intval($year+4800-$a);
            $m = intval($month + 12*$a - 3);
            $J = $day + (153*$m+2)/5 + $y*365 + $y/4 - $y/100 + $y/400 - 32045;
            $d4 = ($J+31741 - ($J % 7)) % 146097 % 36524 % 1461;
            $L = $d4/1460;
            $d1 = (($d4-$L) % 365) + $L;
            $WeekNumber = intval(($d1/7)+1);
            return $WeekNumber;}*/
        }


    /**
     * Throw an exception
     *
     * Note : for performance reasons, the "load" of Zend/Date/Exception is dynamic
     */
    public static function throwException($message)
    {
        require_once('Zend/Date/Exception.php');
        throw new Zend_Date_Exception($message);
    }
}