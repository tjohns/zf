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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Gdata_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * Tests that do not require online access to servers
 */
require_once 'Zend/Gdata/AppTest.php';
require_once 'Zend/Gdata/App/UtilTest.php';
require_once 'Zend/Gdata/App/AuthorTest.php';
require_once 'Zend/Gdata/App/CategoryTest.php';
require_once 'Zend/Gdata/App/ContentTest.php';
require_once 'Zend/Gdata/App/ControlTest.php';
require_once 'Zend/Gdata/App/FeedTest.php';
require_once 'Zend/Gdata/App/GeneratorTest.php';
require_once 'Zend/Gdata/GdataTest.php';
require_once 'Zend/Gdata/QueryTest.php';

require_once 'Zend/Gdata/AttendeeStatusTest.php';
require_once 'Zend/Gdata/AttendeeTypeTest.php';
require_once 'Zend/Gdata/CommentsTest.php';
require_once 'Zend/Gdata/EntryLinkTest.php';
require_once 'Zend/Gdata/EventStatusTest.php';
require_once 'Zend/Gdata/ExtendedPropertyTest.php';
require_once 'Zend/Gdata/FeedLinkTest.php';
require_once 'Zend/Gdata/OpenSearchItemsPerPageTest.php';
require_once 'Zend/Gdata/OpenSearchStartIndexTest.php';
require_once 'Zend/Gdata/OpenSearchTotalResultsTest.php';
require_once 'Zend/Gdata/OriginalEventTest.php';
require_once 'Zend/Gdata/RecurrenceTest.php';
require_once 'Zend/Gdata/RecurrenceExceptionTest.php';
require_once 'Zend/Gdata/ReminderTest.php';
require_once 'Zend/Gdata/TransparencyTest.php';
require_once 'Zend/Gdata/VisibilityTest.php';
require_once 'Zend/Gdata/WhenTest.php';
require_once 'Zend/Gdata/WhereTest.php';
require_once 'Zend/Gdata/WhoTest.php';

require_once 'Zend/Gdata/CalendarTest.php';
require_once 'Zend/Gdata/CalendarFeedTest.php';
require_once 'Zend/Gdata/CalendarEventTest.php';
require_once 'Zend/Gdata/CalendarFeedCompositeTest.php';
require_once 'Zend/Gdata/Calendar/EventQueryTest.php';
require_once 'Zend/Gdata/Calendar/EventQueryExceptionTest.php';
require_once 'Zend/Gdata/Calendar/EventEntryTest.php';
require_once 'Zend/Gdata/Calendar/AccessLevelTest.php';
require_once 'Zend/Gdata/Calendar/ColorTest.php';
require_once 'Zend/Gdata/Calendar/HiddenTest.php';
require_once 'Zend/Gdata/Calendar/LinkTest.php';
require_once 'Zend/Gdata/Calendar/SelectedTest.php';
require_once 'Zend/Gdata/Calendar/SendEventNotificationsTest.php';
require_once 'Zend/Gdata/Calendar/TimezoneTest.php';
require_once 'Zend/Gdata/Calendar/WebContentTest.php';
require_once 'Zend/Gdata/Calendar/QuickAddTest.php';

require_once 'Zend/Gdata/Spreadsheets/ColCountTest.php';
require_once 'Zend/Gdata/Spreadsheets/RowCountTest.php';
require_once 'Zend/Gdata/Spreadsheets/CellTest.php';
require_once 'Zend/Gdata/Spreadsheets/CustomTest.php';
require_once 'Zend/Gdata/Spreadsheets/WorksheetEntryTest.php';
require_once 'Zend/Gdata/Spreadsheets/CellEntryTest.php';
require_once 'Zend/Gdata/Spreadsheets/ListEntryTest.php';
require_once 'Zend/Gdata/Spreadsheets/SpreadsheetFeedTest.php';
require_once 'Zend/Gdata/Spreadsheets/WorksheetFeedTest.php';
require_once 'Zend/Gdata/Spreadsheets/CellFeedTest.php';
require_once 'Zend/Gdata/Spreadsheets/ListFeedTest.php';
require_once 'Zend/Gdata/Spreadsheets/DocumentQueryTest.php';
require_once 'Zend/Gdata/Spreadsheets/CellQueryTest.php';
require_once 'Zend/Gdata/Spreadsheets/ListQueryTest.php';

/**
 * Tests that do require online access to servers
 * and authentication credentials
 */

require_once 'Zend/Gdata/GdataOnlineTest.php';

require_once 'Zend/Gdata/CalendarOnlineTest.php';

require_once 'Zend/Gdata/SpreadsheetsOnlineTest.php';

require_once 'Zend/Gdata/SkipTests.php';

class Zend_Gdata_AllTests
{

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Gdata');

        /**
         * Tests that do not require online access to servers
         */
        $suite->addTestSuite('Zend_Gdata_AppTest');
        $suite->addTestSuite('Zend_Gdata_App_UtilTest');
        $suite->addTestSuite('Zend_Gdata_App_AuthorTest');
        $suite->addTestSuite('Zend_Gdata_App_CategoryTest');
        $suite->addTestSuite('Zend_Gdata_App_ContentTest');
        $suite->addTestSuite('Zend_Gdata_App_ControlTest');
        $suite->addTestSuite('Zend_Gdata_App_FeedTest');
        $suite->addTestSuite('Zend_Gdata_App_GeneratorTest');
        $suite->addTestSuite('Zend_Gdata_GdataTest');
        $suite->addTestSuite('Zend_Gdata_QueryTest');
        
        $suite->addTestSuite('Zend_Gdata_AttendeeStatusTest');
        $suite->addTestSuite('Zend_Gdata_AttendeeTypeTest');
        $suite->addTestSuite('Zend_Gdata_CommentsTest');
        $suite->addTestSuite('Zend_Gdata_EntryLinkTest');
        $suite->addTestSuite('Zend_Gdata_EventStatusTest');
        $suite->addTestSuite('Zend_Gdata_ExtendedPropertyTest');
        $suite->addTestSuite('Zend_Gdata_FeedLinkTest');
        $suite->addTestSuite('Zend_Gdata_OpenSearchItemsPerPageTest');
        $suite->addTestSuite('Zend_Gdata_OpenSearchStartIndexTest');
        $suite->addTestSuite('Zend_Gdata_OpenSearchTotalResultsTest');
        $suite->addTestSuite('Zend_Gdata_OriginalEventTest');
        $suite->addTestSuite('Zend_Gdata_RecurrenceTest');
        $suite->addTestSuite('Zend_Gdata_RecurrenceExceptionTest');
        $suite->addTestSuite('Zend_Gdata_ReminderTest');
        $suite->addTestSuite('Zend_Gdata_TransparencyTest');
        $suite->addTestSuite('Zend_Gdata_VisibilityTest');
        $suite->addTestSuite('Zend_Gdata_WhenTest');
        $suite->addTestSuite('Zend_Gdata_WhereTest');
        $suite->addTestSuite('Zend_Gdata_WhoTest');

        $suite->addTestSuite('Zend_Gdata_CalendarTest');
        $suite->addTestSuite('Zend_Gdata_CalendarFeedTest');
        $suite->addTestSuite('Zend_Gdata_CalendarEventTest');
        $suite->addTestSuite('Zend_Gdata_CalendarFeedCompositeTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_EventQueryTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_EventQueryExceptionTest');        
        $suite->addTestSuite('Zend_Gdata_Calendar_EventEntryTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_AccessLevelTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_ColorTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_HiddenTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_LinkTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_SelectedTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_SendEventNotificationsTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_TimezoneTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_WebContentTest');
        $suite->addTestSuite('Zend_Gdata_Calendar_QuickAddTest');
        
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_ColCountTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_RowCountTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_CellTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_CustomTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_WorksheetEntryTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_CellEntryTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_ListEntryTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_SpreadsheetFeedTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_WorksheetFeedTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_CellFeedTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_ListFeedTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_DocumentQueryTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_CellQueryTest');
        $suite->addTestSuite('Zend_Gdata_Spreadsheets_ListQueryTest');

        if (defined('TESTS_ZEND_GDATA_ONLINE_ENABLED') &&
        constant('TESTS_ZEND_GDATA_ONLINE_ENABLED') == true &&
        defined('TESTS_ZEND_GDATA_CLIENTLOGIN_ENABLED') &&
        constant('TESTS_ZEND_GDATA_CLIENTLOGIN_ENABLED') == true) {
            /**
             * Tests that do require online access to servers
             * and authentication credentials
             */
            if (defined('TESTS_ZEND_GDATA_BLOGGER_ONLINE_ENABLED') &&
            constant('TESTS_ZEND_GDATA_BLOGGER_ONLINE_ENABLED') == true) {
                $suite->addTestSuite('Zend_Gdata_GdataOnlineTest');
            }
            
            $suite->addTestSuite('Zend_Gdata_CalendarOnlineTest');
            
            if (defined('TESTS_ZEND_GDATA_SPREADSHEETS_ONLINE_ENABLED') &&
            constant('TESTS_ZEND_GDATA_SPREADSHEETS_ONLINE_ENABLED') == true) {
                $suite->addTestSuite('Zend_Gdata_SpreadsheetsOnlineTest');
            }
        } else {
            $suite->addTestSuite('Zend_Gdata_SkipOnlineTest');
        }
        return $suite;
    }

}

if (PHPUnit_MAIN_METHOD == 'Zend_Gdata_AllTests::main') {
    Zend_Gdata_AllTests::main();
}
