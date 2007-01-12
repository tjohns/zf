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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * This file contains experiments and code that is simply shared between those working on DateTest.php.
 * The tests below may or may not eventually be added to DateTest.php.
 * This file should *never* be included within the ZF test suite.
 * This file should *not* be released with ZF core.
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite
// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date


/**
 * Zend_Date
 */
require_once 'Zend.php';
require_once 'Zend/Date.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Date/Cities.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";


/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */
class Zend_DatePreTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        date_default_timezone_set('America/Los_Angeles');
    }

    public function testDateWithDateFragments()
    {
        // When specifying a timestamp, 0 always means 1970/01/01 00:00:00
        $date = new Zend_Date(0, Zend_Date::TIMESTAMP);
        $date->setGmt(true);
        echo "Timestamp = ", $date->get(), " new Zend_Date(0, Zend_Date::TIMESTAMP, true)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";
        echo "Timestamp (GMT) = ", $date->get(), " new Zend_Date(0, null, false)\n";
        echo "toString(null, GMT -i.e. true) = ", $date->toString(null, true), "\n";

        $date = new Zend_Date(0, Zend_Date::TIMESTAMP);
        $date->setGmt(false);
        echo "Timestamp = ", $date->get(), " new Zend_Date(0, Zend_Date::TIMESTAMP, false)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";
        $date->setGmt(true);
        echo "Timestamp (GMT) = ", $date->get(), " new Zend_Date(0, null, false)\n";
        echo "toString(null, GMT -i.e. true) = ", $date->toString(null), "\n";

        $date = new Zend_Date(0);
        echo "Timestamp = ", $date->get(), " new Zend_Date(0)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";

        $date = new Zend_Date(0, null, true);
        echo "Timestamp = ", $date->get(), " new Zend_Date(0, null, true)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";

        $date = new Zend_Date(0, null, false);
        echo "Timestamp = ", $date->get(), " new Zend_Date(0, null, false)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";

        echo "Timestamp (GMT) = ", $date->get(null, true), " new Zend_Date(0, null, false)\n";
        echo "toString(null, GMT -i.e. true) = ", $date->toString(null, true), "\n";

        $date = new Zend_Date('7',Zend_Date::HOUR, null, false);
        echo "Timestamp = ", $date->get(), " new Zend_Date('7',Zend_Date::HOUR, null, false)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";

        $date = new Zend_Date('7',Zend_Date::HOUR, null, true);
        echo "Timestamp = ", $date->get(), " new Zend_Date('7',Zend_Date::HOUR, null, true)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";

        $locale = new Zend_Locale('en_US');
        $date = new Zend_Date('01.01.2006', Zend_Date::DATES, $locale);
        echo "Timestamp = ", $date->get(), " new Zend_Date('01.01.2006', Zend_Date::DATES, $locale)\n";
        echo "Line#:", __LINE__, " ", $date->toString(), "\n";
    }

    public function testWeek()
    {
        $date1 = new Zend_Date(null,null,false,'en_US');
        $date2 = new Zend_Date(null,null,false,'en_US');
        $date3 = new Zend_Date(null,null,false,'en_US');
        $date4 = new Zend_Date(null,null,true,'en_US');
        $this->assertTrue($date1 instanceof Zend_Date);
        $this->assertTrue($date2 instanceof Zend_Date);
        $this->assertTrue($date3 instanceof Zend_Date);
        $this->assertTrue($date4 instanceof Zend_Date);

        var_dump($date1);
        echo "Timestamp date1 new Zend_Date(null,null,false,'en_US')= ", $date1->get(),
                " timestamps returned by get() are always UTC\n";
        echo "Timestamp date1 new Zend_Date(null,null,true,'en_US') = ", $date1->get(null,true),
                " timestamps returned by get() are always UTC\n";
        echo "Timestamp date4 new Zend_Date(null,null,true,'en_US') = ", $date4->get(),
                " timestamps returned by get() are always UTC\n";
        echo "Timestamp date4 new Zend_Date(null,null,true,'en_US') = ", $date4->get(null,true),
                " timestamps returned by get() are always UTC\n";

        echo "\n";

        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString()          : ",
                $date1->toString(), " defaults to formatting time in local timezone\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,false): ",
                $date1->toString(null,false), " formatted in local timezone\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,true) : ",
                $date1->toString(null,true), " formatted as UTC date string\n";

        echo "\n";

        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString()           : ",
                $date4->toString(), "  defaults to formatting time in local timezone\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,false) : ",
                $date4->toString(null,false), " formatted in local timezone\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,true)  : ",
                $date4->toString(null,true), " formatted as UTC date string\n";

        echo "\nAfter addWeek(1):\n";
        $date1->addWeek(1);
        echo "Timestamp 1 = ", $date1->get(), " ->addWeek(1)\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addWeek(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addWeek(1,false)\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addWeek(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addWeek(1,true)\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addWeek(0):\n";
        $date1->addWeek(0);
        echo "Timestamp 1 = ", $date1->get(), " ->addWeek(0) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addWeek(0,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addWeek(0,false) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addWeek(0,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addWeek(0,true) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter subWeek(1):\n";
        $date1->subWeek(1);
        echo "Timestamp 1 = ", $date1->get(), " ->subWeek(1) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->subWeek(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->subWeek(1,false) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->subWeek(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->subWeek(1,true) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addWeek(4):\n";
        $date1->addWeek(4);
        echo "Timestamp 1 = ", $date1->get(), " ->addWeek(4) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addWeek(4,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addWeek(4,false) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addWeek(4,true);
        echo "Timestamp 3 = ", $date3->get(), "->addWeek(4,true) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";
    }

    public function testDay()
    {
        echo "\n------------------------------------------\n";
        $date1 = new Zend_Date(null,null,false,'en_US');
        $date2 = new Zend_Date(null,null,false,'en_US');
        $date3 = new Zend_Date(null,null,false,'en_US');
        $date4 = new Zend_Date(null,null,true,'en_US');
        $this->assertTrue($date1 instanceof Zend_Date);
        $this->assertTrue($date2 instanceof Zend_Date);
        $this->assertTrue($date3 instanceof Zend_Date);
        $this->assertTrue($date4 instanceof Zend_Date);

        echo "Timestamp date1 new Zend_Date(null,null,false,'en_US')= ", $date1->get(), "\n";
        echo "Timestamp date4 new Zend_Date(null,null,true,'en_US') = ", $date4->get(), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString()          : ",$date1->toString(), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,false): ",$date1->toString(null,false), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,true) : ",$date1->toString(null,true), "\n";
        echo "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString()           : ",$date4->toString(), "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,false) : ",$date4->toString(null,false), "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,true)  : ",$date4->toString(null,true), "\n";

        echo "\nAfter addDay(1):\n";
        $date1->addDay(1);
        echo "Timestamp 1 = ", $date1->get(), " ->addDay(1)\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addDay(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addDay(1,false)\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addDay(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addDay(1,true)\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addDay(0):\n";
        $date1->addDay(0);
        echo "Timestamp 1 = ", $date1->get(), " ->addDay(0) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addDay(0,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addDay(0,false) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addDay(0,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addDay(0,true) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter subDay(1):\n";
        $date1->subDay(1);
        echo "Timestamp 1 = ", $date1->get(), " ->subDay(1) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->subDay(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->subDay(1,false) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->subDay(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->subDay(1,true) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addDay(4):\n";
        $date1->addDay(4);
        echo "Timestamp 1 = ", $date1->get(), " ->addDay(4) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addDay(4,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addDay(4,false) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addDay(4,true);
        echo "Timestamp 3 = ", $date3->get(), "->addDay(4,true) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";
    }

    public function testHour()
    {
        echo "\n------------------------------------------\n";
        $date1 = new Zend_Date(null,null,false,'en_US');
        $date2 = new Zend_Date(null,null,false,'en_US');
        $date3 = new Zend_Date(null,null,false,'en_US');
        $date4 = new Zend_Date(null,null,true,'en_US');
        $this->assertTrue($date1 instanceof Zend_Date);
        $this->assertTrue($date2 instanceof Zend_Date);
        $this->assertTrue($date3 instanceof Zend_Date);
        $this->assertTrue($date4 instanceof Zend_Date);

        echo "Timestamp date1 new Zend_Date(null,null,false,'en_US')= ", $date1->get(), "\n";
        echo "Timestamp date4 new Zend_Date(null,null,true,'en_US') = ", $date4->get(), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString()          : ",$date1->toString(), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,false): ",$date1->toString(null,false), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,true) : ",$date1->toString(null,true), "\n";
        echo "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString()           : ",$date4->toString(), "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,false) : ",$date4->toString(null,false), "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,true)  : ",$date4->toString(null,true), "\n";

        echo "\nAfter addHour(1):\n";
        $date1->addHour(1);
        echo "Timestamp 1 = ", $date1->get(), " ->addHour(1)\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addHour(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addHour(1,false)\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addHour(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addHour(1,true)\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addHour(0):\n";
        $date1->addHour(0);
        echo "Timestamp 1 = ", $date1->get(), " ->addHour(0) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addHour(0,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addHour(0,false) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addHour(0,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addHour(0,true) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter subHour(1):\n";
        $date1->subHour(1);
        echo "Timestamp 1 = ", $date1->get(), " ->subHour(1) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->subHour(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->subHour(1,false) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->subHour(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->subHour(1,true) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addHour(4):\n";
        $date1->addHour(4);
        echo "Timestamp 1 = ", $date1->get(), " ->addHour(4) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addHour(4,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addHour(4,false) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addHour(4,true);
        echo "Timestamp 3 = ", $date3->get(), "->addHour(4,true) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";
    }
    
    public function testMonth()
    {
        echo "\n------------------------------------------\n";
        $date1 = new Zend_Date(null,null,false,'en_US');
        $date2 = new Zend_Date(null,null,false,'en_US');
        $date3 = new Zend_Date(null,null,false,'en_US');
        $date4 = new Zend_Date(null,null,true,'en_US');
        $this->assertTrue($date1 instanceof Zend_Date);
        $this->assertTrue($date2 instanceof Zend_Date);
        $this->assertTrue($date3 instanceof Zend_Date);
        $this->assertTrue($date4 instanceof Zend_Date);

        echo "Timestamp date1 new Zend_Date(null,null,false,'en_US')= ", $date1->get(), "\n";
        echo "Timestamp date4 new Zend_Date(null,null,true,'en_US') = ", $date4->get(), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString()          : ",$date1->toString(), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,false): ",$date1->toString(null,false), "\n";
        echo "Start Time date1 new Zend_Date(null,null,false,'en_US') - toString(null,true) : ",$date1->toString(null,true), "\n";
        echo "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString()           : ",$date4->toString(), "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,false) : ",$date4->toString(null,false), "\n";
        echo "Start Time date4 new Zend_Date(null,null,true,'en_US') - toString(null,true)  : ",$date4->toString(null,true), "\n";

        echo "\nAfter addMonth(1):\n";
        $date1->addMonth(1);
        echo "Timestamp 1 = ", $date1->get(), " ->addMonth(1)\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addMonth(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addMonth(1,false)\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addMonth(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addMonth(1,true)\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addMonth(0):\n";
        $date1->addMonth(0);
        echo "Timestamp 1 = ", $date1->get(), " ->addMonth(0) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addMonth(0,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addMonth(0,false) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addMonth(0,true);
        echo "Timestamp 3 = ", $date3->get(), " ->addMonth(0,true) - should not change from above\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter subMonth(1):\n";
        $date1->subMonth(1);
        echo "Timestamp 1 = ", $date1->get(), " ->subMonth(1) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->subMonth(1,false);
        echo "Timestamp 2 = ", $date2->get(), " ->subMonth(1,false) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->subMonth(1,true);
        echo "Timestamp 3 = ", $date3->get(), " ->subMonth(1,true) - should be same as start time\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";


        echo "\nAfter addMonth(4):\n";
        $date1->addMonth(4);
        echo "Timestamp 1 = ", $date1->get(), " ->addMonth(4) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date1->toString(), "\n";

        $date2->addMonth(4,false);
        echo "Timestamp 2 = ", $date2->get(), " ->addMonth(4,false) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date2->toString(), "\n";

        $date3->addMonth(4,true);
        echo "Timestamp 3 = ", $date3->get(), "->addMonth(4,true) - should be exactly start time + 4 weeks\n";
        echo "Line#:", __LINE__, " ", $date3->toString(), "\n";
    }
    
}
