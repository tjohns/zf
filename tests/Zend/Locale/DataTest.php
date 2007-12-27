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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Locale_Data
 */
require_once 'Zend/Locale/Data.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */
class Zend_Locale_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for reading with standard locale
     * expected array
     */
    public function testNoLocale()
    {
        $this->assertTrue(is_array(Zend_Locale_Data::getContent(null, 'languagelist')),'array expected');

        try {
            $value = Zend_Locale_Data::getContent('nolocale','languagelist');
            $this->fail('locale should throw exception');
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $locale = new Zend_Locale('de');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent($locale, 'languagelist')),'array expected');
    }


    /**
     * test for reading without type
     * expected empty array
     */
    public function testNoType()
    {
        try {
            $value = Zend_Locale_Data::getContent('de','');
            $this->fail('content should throw an exception');
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Data::getContent('de','xxxxxxx');
            $this->fail('content should throw an exception');
        } catch (Zend_Locale_Exception $e) {
            // success
        }
    }


    /**
     * test for reading the languagelist from locale
     * expected array
     */
    public function testLanguageList()
    {
        $data = Zend_Locale_Data::getContent('de','languagelist');
        $this->assertEquals('Deutsch',  $data['de'], "'Deutsch' instead of '" . $data['de']."' expected" );
        $this->assertEquals('Englisch', $data['en'], "'Englisch' instead of '" . $data['en']."' expected");

        $value = Zend_Locale_Data::getContent('de', 'language', 'de');
        $this->assertEquals('Deutsch', $value['de'], "'Deutsch' instead of '" . $value['de']."' expected");
    }

    /**
     * test for reading the scriptlist from locale
     * expected array
     */
    public function testScriptList()
    {
        $data = Zend_Locale_Data::getContent('de_AT', 'scriptlist');
        $this->assertEquals('Arabisch',   $data['Arab'], "'Arabisch' instead of '" . $data['Arab']."' expected"  );
        $this->assertEquals('Lateinisch', $data['Latn'], "'Lateinisch' instead of '" . $data['Latn']."' expected");

        $value = Zend_Locale_Data::getContent('de_AT', 'script', 'Arab');
        $this->assertEquals('Arabisch', $value['Arab'], "'Arabisch' instead of '" . $value['Arab']."' expected");
    }

    /**
     * test for reading the territorylist from locale
     * expected array
     */
    public function testTerritoryList()
    {
        $data = Zend_Locale_Data::getContent('de_AT', 'territorylist');
        $this->assertEquals('Österreich', $data['AT'], "'Österreich' instead of '" . $data['AT']."' expected");
        $this->assertEquals('Martinique', $data['MQ'], "'Martinique' instead of '" . $data['MQ']."' expected");

        $value = Zend_Locale_Data::getContent('de_AT', 'territory', 'AT');
        $this->assertEquals('Österreich', $value['AT'], "'Österreich' instead of '" . $value['AT']."' expected");
    }

    /**
     * test for reading the variantlist from locale
     * expected array
     */
    public function testVariantList()
    {
        $data = Zend_Locale_Data::getContent('de_AT', 'variantlist');
        $this->assertEquals('Boontling', $data['BOONT'], "'Boontling' instead of '" . $data['BOONT']."' expected");
        $this->assertEquals('Saho',      $data['SAAHO'], "'Saho' instead of '" . $data['SAAHO']."' expected"     );

        $value = Zend_Locale_Data::getContent('de_AT', 'variant', 'POSIX');
        $this->assertEquals('Posix', $value['POSIX'], "'Posix' instead of '" . $value['POSIX']."' expected");
    }

    /**
     * test for reading the keylist from locale
     * expected array
     */
    public function testKeyList()
    {
        $data = Zend_Locale_Data::getContent('de_AT', 'keylist');
        $this->assertEquals('Kalender',   $data['calendar'],  "'Kalender' instead of '" . $data['calendar']."' expected"   );
        $this->assertEquals('Sortierung', $data['collation'], "'Sortierung' instead of '" . $data['collation']."' expected");

        $value = Zend_Locale_Data::getContent('de_AT', 'key', 'collation');
        $this->assertEquals('Sortierung', $value['collation'], "'Sortierung' instead of '" . $value['collation']."' expected");
    }

    /**
     * test for reading the typelist from locale
     * expected array
     */
    public function testTypeList()
    {
        $data = Zend_Locale_Data::getContent('de_AT', 'typelist');
        $this->assertEquals('Chinesischer Kalender', $data['chinese'], "'Chinesischer Kalender' instead of '" . $data['chinese']."' expected");
        $this->assertEquals('Strichfolge',           $data['stroke'],  "'Strichfolge' instead of '" . $data['stroke']."' expected"           );
    }

    /**
     * test for reading the calendar from locale
     * expected array
     */
    public function testCalendar()
    {
        $data = Zend_Locale_Data::getContent('de_AT', 'type', 'calendar');
        $this->assertEquals('Chinesischer Kalender', $data['chinese'],  "'Chinesischer Kalender' instead of '" . $data['chinese']."' expected");
        $this->assertEquals('Japanischer Kalender',  $data['japanese'], "'Japanischer Kalender' instead of '" . $data['japanese']."' expected");

        $value = Zend_Locale_Data::getContent('de_AT', 'type', 'chinese');
        $this->assertEquals('Chinesischer Kalender', $value['chinese'], "'Chinesischer Kalender' instead of '" . $value['chinese']."' expected");
    }

    /**
     * test for reading orientation from locale
     * expected array
     */
    public function testLDMLReadingOrientation()
    {
        $layout = Zend_Locale_Data::getContent('es', 'layout');
        $this->assertEquals("", $layout['lines']);
        $this->assertEquals("", $layout['characters']);
        $this->assertEquals("titlecase-firstword", $layout['inList']);
        $this->assertEquals("lowercase-words",     $layout['currency']);
        $this->assertEquals("mixed",               $layout['dayWidth']);
        $this->assertEquals("lowercase-words",     $layout['fields']);
        $this->assertEquals("lowercase-words",     $layout['keys']);
        $this->assertEquals("lowercase-words",     $layout['languages']);
        $this->assertEquals("lowercase-words",     $layout['long']);
        $this->assertEquals("lowercase-words",     $layout['measurementSystemNames']);
        $this->assertEquals("mixed",               $layout['monthWidth']);
        $this->assertEquals("lowercase-words",     $layout['quarterWidth']);
        $this->assertEquals("lowercase-words",     $layout['scripts']);
        $this->assertEquals("mixed",               $layout['territories']);
        $this->assertEquals("lowercase-words",     $layout['types']);
        $this->assertEquals("mixed",               $layout['variants']);

        $char = Zend_Locale_Data::getContent('de', 'characters');
        $this->assertEquals("[a ä b-o ö p-s ß t u ü v-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ä ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ö ø ō œ ß ú ù ŭ û ü ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[\\$ £ ¥ ₤ ₧ € a-z]", $char['currencySymbol']);

        $quote = Zend_Locale_Data::getContent('de', 'delimiters');
        $this->assertEquals("„", $quote['quoteStart']);
        $this->assertEquals("“", $quote['quoteEnd']);
        $this->assertEquals("‚", $quote['quoteStartAlt']);
        $this->assertEquals("‘", $quote['quoteEndAlt']);

        $measure = Zend_Locale_Data::getContent('de', 'measurement');
        $this->assertEquals("001", $measure['metric']);
        $this->assertEquals("US",  $measure['US']);
        $this->assertEquals("001", $measure['A4']);
        $this->assertEquals("US",  $measure['US-Letter']);
    }


    /**
     * test for reading datechars from locale
     * expected array
     */
    public function testLDMLReadingDatechars()
    {
        $date = Zend_Locale_Data::getContent('de_AT', 'datechars');
        $this->assertEquals(array("chars" => "GjMtkHmsSEDFwWahKzJeugAZvcL"), $date);

        $date = Zend_Locale_Data::getContent('de_AT', 'defaultcalendar');
        $this->assertEquals(array("default" => "gregorian"), $date);

        $date = Zend_Locale_Data::getContent('de_AT', 'defaultmonth', 'gregorian');
        $this->assertEquals(array("context" => "format", "default" => "wide"), $date);

        $date = Zend_Locale_Data::getContent('de_AT', 'monthlist', array('gregorian', 'format', 'wide'));
        $this->assertEquals(array(1 => "Jänner"  , 2 => "Februar"   , 3 => "März"  , 4 => "April"    , 5 => "Mai",
                                  6 => "Juni"    , 7 => "Juli"      , 8 => "August", 9 => "September", 10=> "Oktober",
                                 11 => "November", 12 => "Dezember"), $date);

        $value = Zend_Locale_Data::getContent('de_AT', 'month', array('gregorian', 'format', 'wide', 12));
        $this->assertEquals('Dezember', $value['12']);

        $value = Zend_Locale_Data::getContent('ar', 'month', array('islamic', 'format', 'abbreviated', '1'));
        $this->assertEquals("Muharram", $value[1]);

        $date = Zend_Locale_Data::getContent('de_AT', 'defaultday', 'gregorian');
        $this->assertEquals(array("context" => "format", "default" => "wide"), $date);

        $date = Zend_Locale_Data::getContent('de_AT', 'daylist', array('gregorian', 'format', 'wide'));
        $this->assertEquals(array("sun" => "Sonntag" , "mon" => "Montag"    , "tue" => "Dienstag",
                                  "wed" => "Mittwoch", "thu" => "Donnerstag", "fri" => "Freitag" ,
                                  "sat" => "Samstag"), $date);

        $value = Zend_Locale_Data::getContent('de_AT', 'day', array('gregorian', 'format', 'wide', "mon"));
        $this->assertEquals('Montag', $value['mon']);

        $value = Zend_Locale_Data::getContent('de_AT', 'week');
        $this->assertEquals(array('minDays' => 4, 'firstDay' => 'mon', 'weekendStart' => 'sat',
                                  'weekendEnd' => 'sun'), $value);
        $value = Zend_Locale_Data::getContent('en_US', 'week');
        $this->assertEquals(array('minDays' => 1, 'firstDay' => 'sun', 'weekendStart' => 'sat',
                                  'weekendEnd' => 'sun'), $value);

        $value = Zend_Locale_Data::getContent('de_AT', 'daytime', 'gregorian');
        $this->assertEquals(array('am' => 'vorm.', 'pm' => 'nachm.'), $value);

        $value = Zend_Locale_Data::getContent('de_AT', 'era', array('gregorian', 'Abbr', 0));
        $this->assertEquals(array(0 => 'v. Chr.'), $value);

        $value = Zend_Locale_Data::getContent('de_AT', 'eralist', 'gregorian');
        $this->assertEquals(array('names' => array(0 => 'v. Chr.', 1 => 'n. Chr.'),
                                  'abbr'  => array(0 => 'v. Chr.', 1 => 'n. Chr.'),
                                  'narrow' => array()), $value);

        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'defdateformat', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'dateformat', array('gregorian', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'deftimeformat', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timeformat', array('gregorian', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'datetimeformat', array('gregorian', 'wide'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'calendarfields', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'relativedates', 'gregorian')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'relativedate', array('gregorian', 'day'))), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezones')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezone', 'Europe/Berlin')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonestandard')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonestandardshort')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonedaylight')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezonedaylightshort')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'timezoneformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'numbersymbols')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'decimalnumberformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'scientificnumberformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'percentnumberformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencyformat')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencynames')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencyname', 'EUR')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencysymbols')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'currencysymbol', 'EUR')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de_AT', 'questionstrings')), 'array expected');

        $this->assertTrue(is_array(Zend_Locale_Data::getContent('az_AZ', 'language', 'az')), 'array expected');
    }


    /**
     * test for supplemental data
     * expected array
     */
    public function testLDMLSupplementalData()
    {
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyfraction', 'JPY')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencydigitlist', 'JPY')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyroundinglist', 'JPY')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyforregion', '830')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'currencyforregionlist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'regionforterritory', '001')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'regionforterritorylist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'scriptforlanguage', 'az')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'scriptforlanguagelist')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'territoryforlanguage', 'az')), 'array expected');
        $this->assertTrue(is_array(Zend_Locale_Data::getContent('de', 'territoryforlanguagelist')), 'array expected');
    }
}