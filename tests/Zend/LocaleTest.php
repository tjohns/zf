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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

/**
 * Zend_Locale
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Cache.php';


// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_LocaleTest extends PHPUnit_Framework_TestCase
{
    private $_cache = null;

    public function setUp()
    {
        require_once 'Zend/Cache.php';
        $this->_cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Locale::setCache($this->_cache);
    }

    public function tearDown()
    {
        $this->_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }

    /**
     * test for object creation
     * expected object instance
     */
    public function testObjectCreation()
    {
        $this->assertTrue(is_string(Zend_Locale::isLocale('de')), 'true expected');

        $this->assertTrue(new Zend_Locale() instanceof Zend_Locale);
        $this->assertTrue(new Zend_Locale('root') instanceof Zend_Locale);
        $this->assertTrue(new Zend_Locale(Zend_Locale::ENVIRONMENT) instanceof Zend_Locale);
        $this->assertTrue(new Zend_Locale(Zend_Locale::BROWSER) instanceof Zend_Locale);

        $locale = new Zend_Locale('de');
        $this->assertTrue(new Zend_Locale($locale) instanceof Zend_Locale);

        $locale = new Zend_Locale('auto');
        $this->assertTrue(new Zend_Locale($locale) instanceof Zend_Locale);
    }

    /**
     * test for serialization
     * expected string
     */
    public function testSerialize()
    {
        $value = new Zend_Locale('de_DE');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial));

        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue));
    }

    /**
     * test toString
     * expected string
     */
    public function testToString()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals('de_DE', $value->toString());
        $this->assertEquals('de_DE', $value->__toString());
    }

    /**
     * test getDefault
     * expected true
     */
    public function testgetDefault()
    {
        Zend_Locale::setDefault('de');
        $value = new Zend_Locale();
        $default = $value->getDefault();
        $this->assertTrue(array_key_exists('de', $default));

        $default = $value->getDefault();
        $this->assertTrue(is_array($default));

        $default = $value->getDefault(Zend_Locale::BROWSER);
        $this->assertTrue(is_array($default));

        $default = $value->getDefault(Zend_Locale::ENVIRONMENT);
        $this->assertTrue(is_array($default));

        $default = $value->getDefault(Zend_Locale::FRAMEWORK);
        $this->assertTrue(is_array($default));
    }

    /**
     * test getEnvironment
     * expected true
     */
    public function testLocaleDetail()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals('de', $value->getLanguage());
        $this->assertEquals('AT', $value->getRegion());

        $value = new Zend_Locale('en_US');
        $this->assertEquals('en', $value->getLanguage());
        $this->assertEquals('US', $value->getRegion());

        $value = new Zend_Locale('en');
        $this->assertEquals('en', $value->getLanguage());
        $this->assertFalse($value->getRegion());
    }

    /**
     * test getEnvironment
     * expected true
     */
    public function testEnvironment()
    {
        $value = new Zend_Locale();
        $default = $value->getEnvironment();
        $this->assertTrue(is_array($default));
    }

    /**
     * test getBrowser
     * expected true
     */
    public function testBrowser()
    {
        $value = new Zend_Locale();
        $default = $value->getBrowser();
        $this->assertTrue(is_array($default));
    }

    /**
     * test clone
     * expected true
     */
    public function testCloning()
    {
        $value = new Zend_Locale('de_DE');
        $newvalue = clone $value;
        $this->assertEquals($value->toString(), $newvalue->toString());
    }

    /**
     * test setLocale
     * expected true
     */
    public function testsetLocale()
    {
        $value = new Zend_Locale('de_DE');
        $value->setLocale('en_US');
        $this->assertEquals('en_US', $value->toString());

        $value->setLocale('en_AA');
        $this->assertEquals('en', $value->toString());

        $value->setLocale('xx_AA');
        $this->assertEquals('root', $value->toString());

        $value->setLocale('auto');
        $this->assertTrue(is_string($value->toString()));

        $value->setLocale('browser');
        $this->assertTrue(is_string($value->toString()));

        $value->setLocale('environment');
        $this->assertTrue(is_string($value->toString()));
    }

    /**
     * test getLanguageTranslationList
     * expected true
     */
    public function testgetLanguageTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getLanguageTranslationList();
        $this->assertTrue(is_array($list));
        $list = $value->getLanguageTranslationList('de');
        $this->assertTrue(is_array($list));
    }

    /**
     * test getLanguageTranslation
     * expected true
     */
    public function testgetLanguageTranslation()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals('Deutsch', $value->getLanguageTranslation('de'));
        $this->assertEquals('German',  $value->getLanguageTranslation('de', 'en'));
        $this->assertFalse($value->getLanguageTranslation('xyz'));
        $this->assertTrue(is_string($value->getLanguageTranslation('de', 'auto')));
    }

    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getScriptTranslationList();
        $this->assertTrue(is_array($list));

        $list = $value->getScriptTranslationList('de');
        $this->assertTrue(is_array($list));
    }

    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslation()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals('Arabisch', $value->getScriptTranslation('Arab'));
        $this->assertEquals('Arabic',   $value->getScriptTranslation('Arab', 'en'));
        $this->assertFalse($value->getScriptTranslation('xyz'));
    }

    /**
     * test getCountryTranslationList
     * expected true
     */
    public function testgetCountryTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getCountryTranslationList();
        $this->assertTrue(is_array($list));

        $list = $value->getCountryTranslationList('de');
        $this->assertEquals("Vereinigte Staaten", $list['US']);
    }

    /**
     * test getCountryTranslation
     * expected true
     */
    public function testgetCountryTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals('Deutschland', $value->getCountryTranslation('DE'));
        $this->assertEquals('Germany',     $value->getCountryTranslation('DE', 'en'));
        $this->assertFalse($value->getCountryTranslation('xyz'));
    }

    /**
     * test getTerritoryTranslationList
     * expected true
     */
    public function testgetTerritoryTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getTerritoryTranslationList();
        $this->assertTrue(is_array($list));

        $list = $value->getTerritoryTranslationList('de');
        $this->assertTrue(is_array($list));
    }

    /**
     * test getTerritoryTranslation
     * expected true
     */
    public function testgetTerritoryTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals('Afrika', $value->getTerritoryTranslation('002'));
        $this->assertEquals('Africa', $value->getTerritoryTranslation('002', 'en'));
        $this->assertFalse($value->getTerritoryTranslation('xyz'));
        $this->assertTrue(is_string($value->getTerritoryTranslation('002', 'auto')));
    }

    /**
     * test getTranslation
     * expected true
     */
    public function testgetTranslation()
    {
        $value = new Zend_Locale('de_DE');
        try {
            $temp = $value->getTranslation('xx');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('Unknown detail (', $e->getMessage());
        }

        $this->assertEquals('Deutsch', $value->getTranslation('de', 'language'));
        $this->assertEquals('German',  $value->getTranslation('de', 'language', 'en'));
        $this->assertFalse($value->getTranslation('xx', 'language'));

        $this->assertEquals('Lateinisch', $value->getTranslation('Latn', 'script'));
        $this->assertEquals('Latin',      $value->getTranslation('Latn', 'script', 'en'));
        $this->assertFalse($value->getTranslation('xyxy', 'script'));

        $this->assertEquals('Österreich', $value->getTranslation('AT', 'country'));
        $this->assertEquals('Austria',    $value->getTranslation('AT', 'country', 'en'));
        $this->assertFalse($value->getTranslation('xx', 'country'));

        $this->assertEquals('Afrika', $value->getTranslation('002', 'territory'));
        $this->assertEquals('Africa', $value->getTranslation('002', 'territory', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'territory'));

        $this->assertEquals('Januar',  $value->getTranslation('1', 'month'));
        $this->assertEquals('January', $value->getTranslation('1', 'month', 'en'));
        $this->assertFalse($value->getTranslation('x', 'month'));

        $this->assertEquals('Jan', $value->getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month'));
        $this->assertEquals('Jan', $value->getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month', 'en'));
        $this->assertFalse($value->getTranslation(array('gregorian', 'format', 'abbreviated', 'x'), 'month'));

        $this->assertEquals('J', $value->getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month'));
        $this->assertEquals('J', $value->getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month', 'en'));
        $this->assertFalse($value->getTranslation(array('gregorian', 'stand-alone', 'narrow', 'x'), 'month'));

        $this->assertEquals('Sonntag', $value->getTranslation('sun', 'day'));
        $this->assertEquals('Sunday',  $value->getTranslation('sun', 'day', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'day'));

        $this->assertEquals('So',  $value->getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day'));
        $this->assertEquals('Sun', $value->getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day', 'en'));
        $this->assertFalse($value->getTranslation(array('gregorian', 'format', 'abbreviated', 'xxx'), 'day'));

        $this->assertEquals('S', $value->getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day'));
        $this->assertEquals('S', $value->getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day', 'en'));
        $this->assertFalse($value->getTranslation(array('gregorian', 'stand-alone', 'narrow', 'xxx'), 'day'));

        $this->assertEquals('EEEE, d. MMMM yyyy', $value->getTranslation('full', 'date'));
        $this->assertEquals('EEEE, MMMM d, yyyy', $value->getTranslation('full', 'date', 'en'));
        $this->assertFalse($value->getTranslation('xxxx', 'date'));

        $this->assertEquals("HH:mm:ss v",  $value->getTranslation('full', 'time'));
        $this->assertEquals('h:mm:ss a v', $value->getTranslation('full', 'time', 'en'));
        $this->assertFalse($value->getTranslation('xxxx', 'time'));

        $this->assertEquals('Wien',       $value->getTranslation('Europe/Vienna', 'citytotimezone'));
        $this->assertEquals('St. John’s', $value->getTranslation('America/St_Johns', 'citytotimezone', 'en'));
        $this->assertFalse($value->getTranslation('xxxx', 'citytotimezone'));

        $this->assertEquals('Euro', $value->getTranslation('EUR', 'nametocurrency'));
        $this->assertEquals('Euro', $value->getTranslation('EUR', 'nametocurrency', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'nametocurrency'));

        $this->assertEquals('EUR', $value->getTranslation('Euro', 'currencytoname'));
        $this->assertEquals('EUR', $value->getTranslation('Euro', 'currencytoname', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'currencytoname'));

        $this->assertEquals('SFr.', $value->getTranslation('CHF', 'currencysymbol'));
        $this->assertEquals('SwF',  $value->getTranslation('CHF', 'currencysymbol', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'currencysymbol'));

        $this->assertEquals('EUR', $value->getTranslation('AT', 'currencytoregion'));
        $this->assertEquals('EUR', $value->getTranslation('AT', 'currencytoregion', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'currencytoregion'));

        $this->assertEquals('011 014 015 017 018', $value->getTranslation('002', 'regiontoterritory'));
        $this->assertEquals('011 014 015 017 018', $value->getTranslation('002', 'regiontoterritory', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'regiontoterritory'));

        $this->assertEquals('AT BE CH DE LI LU', $value->getTranslation('de', 'territorytolanguage'));
        $this->assertEquals('AT BE CH DE LI LU', $value->getTranslation('de', 'territorytolanguage', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'territorytolanguage'));
    }

    /**
     * test getTranslationList
     * expected true
     */
    public function testgetTranslationList()
    {
        $value = new Zend_Locale('de_DE');
        try {
            $temp = $value->getTranslationList();
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('Unknown list (', $e->getMessage());
        }

        $this->assertTrue(in_array('Deutsch', $value->getTranslationList('language')));
        $this->assertTrue(in_array('German', $value->getTranslationList('language', 'en')));

        $this->assertTrue(in_array('Lateinisch', $value->getTranslationList('script')));
        $this->assertTrue(in_array('Latin', $value->getTranslationList('script', 'en')));

        $this->assertTrue(in_array('Afrika', $value->getTranslationList('territory')));
        $this->assertTrue(in_array('Africa', $value->getTranslationList('territory', 'en')));

        $this->assertTrue(in_array('Chinesischer Kalender', $value->getTranslationList('type', null, 'calendar')));
        $this->assertTrue(in_array('Chinese Calendar', $value->getTranslationList('type', 'en', 'calendar')));

        $this->assertTrue(in_array('Januar', $value->getTranslationList('month')));
        $this->assertTrue(in_array('January', $value->getTranslationList('month', 'en')));

        $this->assertTrue(in_array('Jan', $value->getTranslationList('month', null, array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Jan', $value->getTranslationList('month', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('J', $value->getTranslationList('month', null, array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('J', $value->getTranslationList('month', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('Sonntag', $value->getTranslationList('day')));
        $this->assertTrue(in_array('Sunday', $value->getTranslationList('day', 'en')));

        $this->assertTrue(in_array('So', $value->getTranslationList('day', null, array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Sun', $value->getTranslationList('day', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('S', $value->getTranslationList('day', null, array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('S', $value->getTranslationList('day', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('EEEE, d. MMMM yyyy', $value->getTranslationList('date')));
        $this->assertTrue(in_array('EEEE, MMMM d, yyyy', $value->getTranslationList('date', 'en')));

        $this->assertTrue(in_array("HH:mm:ss v", $value->getTranslationList('time')));
        $this->assertTrue(in_array("h:mm:ss a z", $value->getTranslationList('time', 'en')));

        $this->assertTrue(in_array('Wien', $value->getTranslationList('citytotimezone')));
        $this->assertTrue(in_array("St. John’s", $value->getTranslationList('citytotimezone', 'en')));

        $this->assertTrue(in_array('Euro', $value->getTranslationList('nametocurrency')));
        $this->assertTrue(in_array('Euro', $value->getTranslationList('nametocurrency', 'en')));

        $this->assertTrue(in_array('EUR', $value->getTranslationList('currencytoname')));
        $this->assertTrue(in_array('EUR', $value->getTranslationList('currencytoname', 'en')));

        $this->assertTrue(in_array('SFr.', $value->getTranslationList('currencysymbol')));
        $this->assertTrue(in_array('SwF', $value->getTranslationList('currencysymbol', 'en')));

        $this->assertTrue(in_array('EUR', $value->getTranslationList('currencytoregion')));
        $this->assertTrue(in_array('EUR', $value->getTranslationList('currencytoregion', 'en')));

        $this->assertTrue(in_array('AU NF NZ', $value->getTranslationList('regiontoterritory')));
        $this->assertTrue(in_array('AU NF NZ', $value->getTranslationList('regiontoterritory', 'en')));

        $this->assertTrue(in_array('CZ', $value->getTranslationList('territorytolanguage')));
        $this->assertTrue(in_array('CZ', $value->getTranslationList('territorytolanguage', 'en')));

        $char = $value->getTranslationList('characters');
        $this->assertEquals("[a ä b-o ö p-s ß t u ü v-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ä ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ö ø ō œ ß ú ù ŭ û ü ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[\\$ £ ¥ ₤ ₧ € a-z]", $char['currencySymbol']);
        $char = $value->getTranslationList('characters', 'en');
        $this->assertEquals("[a-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ä ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ö ø ō œ ß ú ù ŭ û ü ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[a-z]", $char['currencySymbol']);
    }

    /**
     * test for equality
     * expected string
     */
    public function testEquals()
    {
        $value = new Zend_Locale('de_DE');
        $serial = new Zend_Locale('de_DE');
        $serial2 = new Zend_Locale('de_AT');
        $this->assertTrue($value->equals($serial));
        $this->assertFalse($value->equals($serial2));
    }

    /**
     * test getQuestion
     * expected true
     */
    public function testgetQuestion()
    {
        $value = new Zend_Locale();
        $list = $value->getQuestion();
        $this->assertTrue(isset($list['yes']));

        $list = $value->getQuestion('de');
        $this->assertEquals('ja', $list['yes']);

        $this->assertTrue(is_array($value->getQuestion('auto')));
        $this->assertTrue(is_array($value->getQuestion('browser')));
        $this->assertTrue(is_array($value->getQuestion('environment')));
    }

    /**
     * test getBrowser
     * expected true
     */
    public function testgetBrowser()
    {
        Zend_LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_LANGUAGE=,de,en-UK-US;q=0.5,fr_FR;q=0.2");
        $value = new Zend_LocaleTestHelper();
        $list = $value->getBrowser();
        $this->assertTrue(isset($list['de']));
    }

    /**
     * test getHttpCharset
     * expected true
     */
    public function testgetHttpCharset()
    {
        Zend_LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_CHARSET=");
        $value = new Zend_LocaleTestHelper();
        $list = $value->getHttpCharset();
        $this->assertTrue(empty($list));

        Zend_LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_CHARSET=,iso-8859-1, utf-8, utf-16, *;q=0.1");
        $value = new Zend_LocaleTestHelper();
        $list = $value->getHttpCharset();
        $this->assertTrue(isset($list['utf-8']));
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testIsLocale()
    {
        $locale = new Zend_Locale('ar');
        $this->assertEquals('ar',    Zend_Locale::isLocale($locale));
        $this->assertEquals('de',    Zend_Locale::isLocale('de'));
        $this->assertEquals('de_AT', Zend_Locale::isLocale('de_AT'));
        $this->assertEquals('de',    Zend_Locale::isLocale('de_xx'));
        $this->assertFalse(Zend_Locale::isLocale('yy'));
        $this->assertFalse(Zend_Locale::isLocale(1234));
        $locale = Zend_Locale::isLocale('', true);
        $this->assertTrue(is_string($locale));
        $this->assertTrue(is_string(Zend_Locale::isLocale('auto')));
        $this->assertTrue(is_string(Zend_Locale::isLocale('browser')));
        $this->assertTrue(is_string(Zend_Locale::isLocale('environment')));
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testGetLocaleList()
    {
        $this->assertTrue(is_array(Zend_Locale::getLocaleList()));
    }

    /**
     * test setDefault
     * expected true
     */
    public function testsetDefault()
    {
        try {
            Zend_Locale::setDefault('auto');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains("full qualified locale", $e->getMessage());
        }
        try {
            Zend_Locale::setDefault('de_XX');
            $locale = new Zend_Locale();
            $this->assertTrue($locale instanceof Zend_Locale); // should defer to 'de' or any other standard locale
        } catch (Zend_Locale_Exception $e) {
            $this->fail(); // de_XX should automatically degrade to 'de'
        }
        try {
            Zend_Locale::setDefault('xy_ZZ');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains("Unknown locale", $e->getMessage());
        }
    }
}

class Zend_LocaleTestHelper extends Zend_Locale
{
    public static $_auto;
    public static $_environment;
    public static $_browser;

    public static function resetObject()
    {
        self::$_auto        = null;
        self::$_environment = null;
        self::$_browser     = null;
    }
}
