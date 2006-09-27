<?php
/**
 * @package    Zend_Locale
 * @subpackage UnitTests
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
    public function testLDMLNoLocale()
    {
        $value = Zend_Locale_Data::getContent('','languagelist');
        $this->assertTrue(is_array($value),'array expected');
    }


    /**
     * test for reading without locale
     * expected empty array
     */
    public function testLDMLUnknownLocale()
    {
        try {
            $value = Zend_Locale_Data::getContent('nolocale','languagelist');
            $this->assertTrue(false,'locale should throw exception');
        } catch (Exception $e) {
            return true;
        }
    }


    /**
     * test for reading without type
     * expected empty array
     */
    public function testLDMLNoType()
    {
        $value = Zend_Locale_Data::getContent('de','');
        $this->assertTrue(empty($value),'empty array expected');
    }


    /**
     * test for reading without type
     * expected empty array
     */
    public function testLDMLUnknownType()
    {
        $value = Zend_Locale_Data::getContent('de','xxxxxxx');
        $this->assertTrue(empty($value),'empty array expected');
    }


    /**
     * test for reading the languagelist from locale
     * expected array
     */
    public function testLDMLReadingLanguageList()
    {
        $value = Zend_Locale_Data::getContent('de','languagelist');
        $this->assertTrue(is_array($value),'array expected');
    }
    
    
    /**
     * test for reading one language from locale
     * expected array
     */
    public function testLDMLReadingLanguage()
    {
        $value = Zend_Locale_Data::getContent('de', 'language', 'de');
        $this->assertEquals($value['de'], 'Deutsch', 'wrong content');
    }


    /**
     * test for reading the scriptlist from locale
     * expected array
     */
    public function testLDMLReadingScriptList()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'scriptlist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading one script from locale
     * expected array
     */
    public function testLDMLReadingScript()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'script', 'Arab');
        $this->assertEquals($value['Arab'], 'Arabisch', 'wrong content');
    }


    /**
     * test for reading the variantlist from locale
     * expected array
     */
    public function testLDMLReadingVariantList()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'variantlist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading one variant from locale
     * expected array
     */
    public function testLDMLReadingVariant()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'variant', 'POSIX');
        $this->assertEquals($value['POSIX'], 'Posix', 'wrong content');
    }


    /**
     * test for reading the typelist from locale
     * expected array
     */
    public function testLDMLReadingKeyList()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'keylist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading one key from locale
     * expected array
     */
    public function testLDMLReadingKey()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'key', 'collation');
        $this->assertEquals($value['collation'], 'Sortierung', 'wrong content');
    }


    /**
     * test for reading the keylist from locale
     * expected array
     */
    public function testLDMLReadingTypeList()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'typelist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading one key from locale
     * expected array
     */
    public function testLDMLReadingType()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'type', 'calendar');
        $this->assertEquals(is_array($value), 'array expected');
    }


    /**
     * test for reading one key from locale
     * expected array
     */
    public function testLDMLReadingType2()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'type', 'chinese');
        $this->assertEquals($value['chinese'], 'Chinesischer Kalender', 'wrong value');
    }


    /**
     * test for reading orientation from locale
     * expected array
     */
    public function testLDMLReadingOrientation()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'orientation');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading casing from locale
     * expected array
     */
    public function testLDMLReadingCasing()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'casing');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading characters from locale
     * expected array
     */
    public function testLDMLReadingCharacters()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'characters');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading delimiters from locale
     * expected array
     */
    public function testLDMLReadingDelimiters()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'delimiters');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading measurement from locale
     * expected array
     */
    public function testLDMLReadingMeasurement()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'measurement');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading papersize from locale
     * expected array
     */
    public function testLDMLReadingPapersize()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'papersize');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading datechars from locale
     * expected array
     */
    public function testLDMLReadingDatechars()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'datechars');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading defcalendarformat from locale
     * expected array
     */
    public function testLDMLReadingDefCalendarFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'defcalendarformat');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading defmonthformat from locale
     * expected array
     */
    public function testLDMLReadingDefMonthFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'defmonthformat', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading monthlist from locale
     * expected array
     */
    public function testLDMLReadingMonthlistFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'monthlist', array('gregorian', 'wide'));
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading one month from locale
     * expected array
     */
    public function testLDMLReadingMonth()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'month', array('gregorian', 'format', 'wide', 12));
        $this->assertEquals($value['12'], 'Dezember', 'wrong value');
    }


    /**
     * test for reading defdayformat from locale
     * expected array
     */
    public function testLDMLReadingDefDayFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'defdayformat', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading daylist from locale
     * expected array
     */
    public function testLDMLReadingDaylistFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'daylist', array('gregorian', 'wide'));
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading one day from locale
     * expected array
     */
    public function testLDMLReadingDay()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'day', array('gregorian', 'wide', 'mon'));
        $this->assertEquals($value['mon'], 'Montag', 'wrong value');
    }


    /**
     * test for reading week from locale
     * expected array
     */
    public function testLDMLReadingWeek()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'week', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading weekend from locale
     * expected array
     */
    public function testLDMLReadingWeekend()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'weekend', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading daytime from locale
     * expected array
     */
    public function testLDMLReadingDaytime()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'daytime', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading erashortlist from locale
     * expected array
     */
    public function testLDMLReadingEraShortList()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'erashortlist', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading erashort from locale
     * expected array
     */
    public function testLDMLReadingEraShort()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'erashort', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading eralist from locale
     * expected array
     */
    public function testLDMLReadingEraList()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'eralist', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading era from locale
     * expected array
     */
    public function testLDMLReadingEra()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'era', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading defdateformat from locale
     * expected array
     */
    public function testLDMLReadingDefDateFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'defdateformat', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading dateformat from locale
     * expected array
     */
    public function testLDMLReadingDateFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'dateformat', array('gregorian', 'wide'));
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading deftimeformat from locale
     * expected array
     */
    public function testLDMLReadingDefTimeFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'deftimeformat', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timeformat from locale
     * expected array
     */
    public function testLDMLReadingTimeFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timeformat', array('gregorian', 'wide'));
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading datetimeformat from locale
     * expected array
     */
    public function testLDMLReadingDateTimeFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'datetimeformat', array('gregorian', 'wide'));
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading calendarfield from locale
     * expected array
     */
    public function testLDMLReadingCalendarFields()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'calendarfields', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading relativedates from locale
     * expected array
     */
    public function testLDMLReadingRelativeDates()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'relativedates', 'gregorian');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading relativedate from locale
     * expected array
     */
    public function testLDMLReadingRelativeDate()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'relativedate', array('gregorian', 'day'));
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timezones from locale
     * expected array
     */
    public function testLDMLReadingTimeZones()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timezones');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timezone from locale
     * expected array
     */
    public function testLDMLReadingTimeZone()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timezone', 'Europe/Berlin');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timezonestandard from locale
     * expected array
     */
    public function testLDMLReadingTimeZoneStandard()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timezonestandard');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timezonestandardshort from locale
     * expected array
     */
    public function testLDMLReadingTimeZoneStandardShort()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timezonestandardshort');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timezonestandard from locale
     * expected array
     */
    public function testLDMLReadingTimeZoneDayLight()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timezonedaylight');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timezonedaylightshort from locale
     * expected array
     */
    public function testLDMLReadingTimeZoneDayLightShort()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timezonedaylightshort');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading timezoneformat from locale
     * expected array
     */
    public function testLDMLReadingTimeZoneFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'timezoneformat');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading decimalnumberformat from locale
     * expected array
     */
    public function testLDMLReadingDecimalNumberFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'decimalnumberformat');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading scientificnumberformat from locale
     * expected array
     */
    public function testLDMLReadingScientificNumberFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'scientificnumberformat');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading percentnumberformat from locale
     * expected array
     */
    public function testLDMLReadingPercentNumberFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'percentnumberformat');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencyformat from locale
     * expected array
     */
    public function testLDMLReadingCurrencyFormat()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'currencyformat');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencynames from locale
     * expected array
     */
    public function testLDMLReadingCurrencyNames()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'currencynames');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencysymbols from locale
     * expected array
     */
    public function testLDMLReadingCurrencySymbols()
    {
        $value = Zend_Locale_Data::getContent('de_AT', 'currencysymbols');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencyfraction from locale
     * expected array
     */
    public function testLDMLReadingCurrencyFraction()
    {
        $value = Zend_Locale_Data::getContent('', 'currencyfraction', 'JPY');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencydigitlist from locale
     * expected array
     */
    public function testLDMLReadingCurrencyDigitList()
    {
        $value = Zend_Locale_Data::getContent('', 'currencydigitlist', 'JPY');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencyroundinglist from locale
     * expected array
     */
    public function testLDMLReadingCurrencyRoundingList()
    {
        $value = Zend_Locale_Data::getContent('', 'currencyroundinglist', 'JPY');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencyforregion from locale
     * expected array
     */
    public function testLDMLReadingCurrencyForRegion()
    {
        $value = Zend_Locale_Data::getContent('', 'currencyforregion', '830');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading currencyforregionlist from locale
     * expected array
     */
    public function testLDMLReadingCurrencyForRegionList()
    {
        $value = Zend_Locale_Data::getContent('', 'currencyforregionlist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading regionforterritory from locale
     * expected array
     */
    public function testLDMLReadingRegionForTerritory()
    {
        $value = Zend_Locale_Data::getContent('', 'regionforterritory', '001');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading regionforterritorylist from locale
     * expected array
     */
    public function testLDMLReadingRegionForTerritoryList()
    {
        $value = Zend_Locale_Data::getContent('', 'regionforterritorylist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading scriptforlanguage from locale
     * expected array
     */
    public function testLDMLReadingScriptForLanguage()
    {
        $value = Zend_Locale_Data::getContent('', 'scriptforlanguage', 'az');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading scriptforlanguagelist from locale
     * expected array
     */
    public function testLDMLReadingScriptForLanguageList()
    {
        $value = Zend_Locale_Data::getContent('', 'scriptforlanguagelist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading territoryforlanguage from locale
     * expected array
     */
    public function testLDMLReadingTerritoryForLanguage()
    {
        $value = Zend_Locale_Data::getContent('', 'territoryforlanguage', 'az');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for reading territoryforlanguagelist from locale
     * expected array
     */
    public function testLDMLReadingTerritoryForLanguageList()
    {
        $value = Zend_Locale_Data::getContent('', 'territoryforlanguagelist');
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for alias in LDML
     * expected array
     */
    public function testLDMLReadingAlias()
    {
        $value = Zend_Locale_Data::getContent('ar', 'month', array('islamic', 'format', 'abbreviated', '1'));
        $this->assertTrue(is_array($value), 'array expected');
    }


    /**
     * test for rerouting in LDML
     * expected array
     */
    public function testLDMLReadingReRouting()
    {
        $value = Zend_Locale_Data::getContent('az_AZ', 'language', 'az');
        $this->assertTrue(is_array($value), 'array expected');
    }
}