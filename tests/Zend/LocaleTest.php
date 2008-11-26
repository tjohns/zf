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

require_once dirname(__FILE__) . '/../TestHelper.php';

// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

/**
 * Zend_Locale
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Cache.php';

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_LocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_LocaleTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    private $_cache = null;

    public function setUp()
    {
        require_once 'Zend/Cache.php';
        $this->_cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Locale::setCache($this->_cache);

        // compatibilityMode is true until 1.8 therefor we have to change it
        Zend_Locale::$compatibilityMode = false;
        putenv("HTTP_ACCEPT_LANGUAGE=,de,en-UK-US;q=0.5,fr_FR;q=0.2");
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
        $this->assertTrue(Zend_Locale::isLocale('de'));

        $this->assertTrue(new Zend_Locale() instanceof Zend_Locale);
        $this->assertTrue(new Zend_Locale('root') instanceof Zend_Locale);
        $this->assertTrue(new Zend_Locale(Zend_Locale::ENVIRONMENT) instanceof Zend_Locale);
        $this->assertTrue(new Zend_Locale(Zend_Locale::BROWSER) instanceof Zend_Locale);

        $locale = new Zend_Locale('de');
        $this->assertTrue(new Zend_Locale($locale) instanceof Zend_Locale);

        $locale = new Zend_Locale('auto');
        $this->assertTrue(new Zend_Locale($locale) instanceof Zend_Locale);

        // compatibility tests
        set_error_handler(array($this, 'errorHandlerIgnore'));
        Zend_Locale::$compatibilityMode = true;
        $this->assertEquals('de', Zend_Locale::isLocale('de'));
        restore_error_handler();
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
     * test getOrder
     * expected true
     */
    public function testgetOrder()
    {
        Zend_Locale::setDefault('de');
        $value = new Zend_Locale();
        $default = $value->getOrder();
        $this->assertTrue(array_key_exists('de', $default));

        $default = $value->getOrder(Zend_Locale::BROWSER);
        $this->assertTrue(is_array($default));

        $default = $value->getOrder(Zend_Locale::ENVIRONMENT);
        $this->assertTrue(is_array($default));

        $default = $value->getOrder(Zend_Locale::ZFDEFAULT);
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
        $list = Zend_Locale::getLanguageTranslationList();
        $this->assertTrue(is_array($list));
        $list = Zend_Locale::getLanguageTranslationList('de');
        $this->assertTrue(is_array($list));
    }

    /**
     * test getLanguageTranslation
     * expected true
     */
    public function testgetLanguageTranslation()
    {
        $this->assertEquals('Deutsch', Zend_Locale::getLanguageTranslation('de', 'de_AT'));
        $this->assertEquals('German',  Zend_Locale::getLanguageTranslation('de', 'en'));
        $this->assertFalse(Zend_Locale::getLanguageTranslation('xyz'));
        $this->assertTrue(is_string(Zend_Locale::getLanguageTranslation('de', 'auto')));
    }

    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslationList()
    {
        $list = Zend_Locale::getScriptTranslationList();
        $this->assertTrue(is_array($list));

        $list = Zend_Locale::getScriptTranslationList('de');
        $this->assertTrue(is_array($list));
    }

    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslation()
    {
        $this->assertEquals('Arabisch', Zend_Locale::getScriptTranslation('Arab', 'de_AT'));
        $this->assertEquals('Arabic', Zend_Locale::getScriptTranslation('Arab', 'en'));
        $this->assertFalse(Zend_Locale::getScriptTranslation('xyz'));
    }

    /**
     * test getCountryTranslationList
     * expected true
     */
    public function testgetCountryTranslationList()
    {
        $list = Zend_Locale::getCountryTranslationList();
        $this->assertTrue(is_array($list));

        $list = Zend_Locale::getCountryTranslationList('de');
        $this->assertEquals("Vereinigte Staaten", $list['US']);
    }

    /**
     * test getCountryTranslation
     * expected true
     */
    public function testgetCountryTranslation()
    {
        $this->assertEquals('Deutschland', Zend_Locale::getCountryTranslation('DE', 'de_DE'));
        $this->assertEquals('Germany', Zend_Locale::getCountryTranslation('DE', 'en'));
        $this->assertFalse(Zend_Locale::getCountryTranslation('xyz'));
    }

    /**
     * test getTerritoryTranslationList
     * expected true
     */
    public function testgetTerritoryTranslationList()
    {
        $list = Zend_Locale::getTerritoryTranslationList();
        $this->assertTrue(is_array($list));

        $list = Zend_Locale::getTerritoryTranslationList('de');
        $this->assertTrue(is_array($list));
    }

    /**
     * test getTerritoryTranslation
     * expected true
     */
    public function testgetTerritoryTranslation()
    {
        $this->assertEquals('Afrika', Zend_Locale::getTerritoryTranslation('002', 'de_AT'));
        $this->assertEquals('Africa', Zend_Locale::getTerritoryTranslation('002', 'en'));
        $this->assertFalse(Zend_Locale::getTerritoryTranslation('xyz'));
        $this->assertTrue(is_string(Zend_Locale::getTerritoryTranslation('002', 'auto')));
    }

    /**
     * test getTranslation
     * expected true
     */
    public function testgetTranslation()
    {
        try {
            $temp = Zend_Locale::getTranslation('xx');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('Unknown detail (', $e->getMessage());
        }

        $this->assertEquals('Deutsch', Zend_Locale::getTranslation('de', 'language', 'de_DE'));
        $this->assertEquals('German', Zend_Locale::getTranslation('de', 'language', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xx', 'language'));

        $this->assertEquals('Lateinisch', Zend_Locale::getTranslation('Latn', 'script', 'de_DE'));
        $this->assertEquals('Latin', Zend_Locale::getTranslation('Latn', 'script', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xyxy', 'script'));

        $this->assertEquals('Österreich', Zend_Locale::getTranslation('AT', 'country', 'de_DE'));
        $this->assertEquals('Austria', Zend_Locale::getTranslation('AT', 'country', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xx', 'country'));

        $this->assertEquals('Afrika', Zend_Locale::getTranslation('002', 'territory', 'de_DE'));
        $this->assertEquals('Africa', Zend_Locale::getTranslation('002', 'territory', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'territory'));

        $this->assertEquals('Januar', Zend_Locale::getTranslation('1', 'month', 'de_DE'));
        $this->assertEquals('January', Zend_Locale::getTranslation('1', 'month', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('x', 'month'));

        $this->assertEquals('Jan', Zend_Locale::getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month', 'de_DE'));
        $this->assertEquals('Jan', Zend_Locale::getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation(array('gregorian', 'format', 'abbreviated', 'x'), 'month'));

        $this->assertEquals('J', Zend_Locale::getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month', 'de_DE'));
        $this->assertEquals('J', Zend_Locale::getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'x'), 'month'));

        $this->assertEquals('Sonntag', Zend_Locale::getTranslation('sun', 'day', 'de_DE'));
        $this->assertEquals('Sunday', Zend_Locale::getTranslation('sun', 'day', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'day'));

        $this->assertEquals('So.', Zend_Locale::getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day', 'de_DE'));
        $this->assertEquals('Sun', Zend_Locale::getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation(array('gregorian', 'format', 'abbreviated', 'xxx'), 'day'));

        $this->assertEquals('S', Zend_Locale::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day', 'de_DE'));
        $this->assertEquals('S', Zend_Locale::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'xxx'), 'day'));

        $this->assertEquals('EEEE, d. MMMM yyyy', Zend_Locale::getTranslation('full', 'date', 'de_DE'));
        $this->assertEquals('EEEE, MMMM d, yyyy', Zend_Locale::getTranslation('full', 'date', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxxx', 'date'));

        $this->assertEquals("HH:mm:ss v", Zend_Locale::getTranslation('full', 'time', 'de_DE'));
        $this->assertEquals('h:mm:ss a v', Zend_Locale::getTranslation('full', 'time', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxxx', 'time'));

        $this->assertEquals('Wien', Zend_Locale::getTranslation('Europe/Vienna', 'citytotimezone', 'de_DE'));
        $this->assertEquals("St. John's", Zend_Locale::getTranslation('America/St_Johns', 'citytotimezone', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxxx', 'citytotimezone'));

        $this->assertEquals('Euro', Zend_Locale::getTranslation('EUR', 'nametocurrency', 'de_DE'));
        $this->assertEquals('Euro', Zend_Locale::getTranslation('EUR', 'nametocurrency', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'nametocurrency'));

        $this->assertEquals('EUR', Zend_Locale::getTranslation('Euro', 'currencytoname', 'de_DE'));
        $this->assertEquals('EUR', Zend_Locale::getTranslation('Euro', 'currencytoname', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'currencytoname'));

        $this->assertEquals('SFr.', Zend_Locale::getTranslation('CHF', 'currencysymbol', 'de_DE'));
        $this->assertEquals('Fr.',  Zend_Locale::getTranslation('CHF', 'currencysymbol', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'currencysymbol'));

        $this->assertEquals('EUR', Zend_Locale::getTranslation('AT', 'currencytoregion', 'de_DE'));
        $this->assertEquals('EUR', Zend_Locale::getTranslation('AT', 'currencytoregion', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'currencytoregion'));

        $this->assertEquals('011 014 015 017 018', Zend_Locale::getTranslation('002', 'regiontoterritory', 'de_DE'));
        $this->assertEquals('011 014 015 017 018', Zend_Locale::getTranslation('002', 'regiontoterritory', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'regiontoterritory'));

        $this->assertEquals('AT BE CH DE LI LU', Zend_Locale::getTranslation('de', 'territorytolanguage', 'de_DE'));
        $this->assertEquals('AT BE CH DE LI LU', Zend_Locale::getTranslation('de', 'territorytolanguage', 'en'));
        $this->assertFalse(Zend_Locale::getTranslation('xxx', 'territorytolanguage'));
    }

    /**
     * test getTranslationList
     * expected true
     */
    public function testgetTranslationList()
    {
        try {
            $temp = Zend_Locale::getTranslationList();
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('Unknown list (', $e->getMessage());
        }

        $this->assertTrue(in_array('Deutsch', Zend_Locale::getTranslationList('language', 'de_DE')));
        $this->assertTrue(in_array('German', Zend_Locale::getTranslationList('language', 'en')));

        $this->assertTrue(in_array('Lateinisch', Zend_Locale::getTranslationList('script', 'de_DE')));
        $this->assertTrue(in_array('Latin', Zend_Locale::getTranslationList('script', 'en')));

        $this->assertTrue(in_array('Afrika', Zend_Locale::getTranslationList('territory', 'de_DE')));
        $this->assertTrue(in_array('Africa', Zend_Locale::getTranslationList('territory', 'en')));

        $this->assertTrue(in_array('Chinesischer Kalender', Zend_Locale::getTranslationList('type', 'de_DE', 'calendar')));
        $this->assertTrue(in_array('Chinese Calendar', Zend_Locale::getTranslationList('type', 'en', 'calendar')));

        $this->assertTrue(in_array('Januar', Zend_Locale::getTranslationList('month', 'de_DE')));
        $this->assertTrue(in_array('January', Zend_Locale::getTranslationList('month', 'en')));

        $this->assertTrue(in_array('Jan', Zend_Locale::getTranslationList('month', 'de_DE', array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Jan', Zend_Locale::getTranslationList('month', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('J', Zend_Locale::getTranslationList('month', 'de_DE', array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('J', Zend_Locale::getTranslationList('month', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('Sonntag', Zend_Locale::getTranslationList('day', 'de_DE')));
        $this->assertTrue(in_array('Sunday', Zend_Locale::getTranslationList('day', 'en')));

        $this->assertTrue(in_array('So.', Zend_Locale::getTranslationList('day', 'de_DE', array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Sun', Zend_Locale::getTranslationList('day', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('S', Zend_Locale::getTranslationList('day', 'de_DE', array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('S', Zend_Locale::getTranslationList('day', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('EEEE, d. MMMM yyyy', Zend_Locale::getTranslationList('date', 'de_DE')));
        $this->assertTrue(in_array('EEEE, MMMM d, yyyy', Zend_Locale::getTranslationList('date', 'en')));

        $this->assertTrue(in_array("HH:mm:ss v", Zend_Locale::getTranslationList('time', 'de_DE')));
        $this->assertTrue(in_array("h:mm:ss a z", Zend_Locale::getTranslationList('time', 'en')));

        $this->assertTrue(in_array('Wien', Zend_Locale::getTranslationList('citytotimezone', 'de_DE')));
        $this->assertTrue(in_array("St. John's", Zend_Locale::getTranslationList('citytotimezone', 'en')));

        $this->assertTrue(in_array('Euro', Zend_Locale::getTranslationList('nametocurrency', 'de_DE')));
        $this->assertTrue(in_array('Euro', Zend_Locale::getTranslationList('nametocurrency', 'en')));

        $this->assertTrue(in_array('EUR', Zend_Locale::getTranslationList('currencytoname', 'de_DE')));
        $this->assertTrue(in_array('EUR', Zend_Locale::getTranslationList('currencytoname', 'en')));

        $this->assertTrue(in_array('SFr.', Zend_Locale::getTranslationList('currencysymbol', 'de_DE')));
        $this->assertTrue(in_array('Fr.', Zend_Locale::getTranslationList('currencysymbol', 'en')));

        $this->assertTrue(in_array('EUR', Zend_Locale::getTranslationList('currencytoregion', 'de_DE')));
        $this->assertTrue(in_array('EUR', Zend_Locale::getTranslationList('currencytoregion', 'en')));

        $this->assertTrue(in_array('AU NF NZ', Zend_Locale::getTranslationList('regiontoterritory', 'de_DE')));
        $this->assertTrue(in_array('AU NF NZ', Zend_Locale::getTranslationList('regiontoterritory', 'en')));

        $this->assertTrue(in_array('CZ', Zend_Locale::getTranslationList('territorytolanguage', 'de_DE')));
        $this->assertTrue(in_array('CZ', Zend_Locale::getTranslationList('territorytolanguage', 'en')));

        $char = Zend_Locale::getTranslationList('characters', 'de_DE');
        $this->assertEquals("[a ä b-o ö p-s ß t u ü v-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ä ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ö ø ō œ ß ú ù ŭ û ü ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[a-z]", $char['currencySymbol']);

        $char = Zend_Locale::getTranslationList('characters', 'en');
        $this->assertEquals("[a-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ä ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ö ø ō œ ß ú ù ŭ û ü ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[a-c č d-l ł m-z]", $char['currencySymbol']);
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
        $list = Zend_Locale::getQuestion();
        $this->assertTrue(isset($list['yes']));

        $list = Zend_Locale::getQuestion('de');
        $this->assertEquals('ja', $list['yes']);

        $this->assertTrue(is_array(Zend_Locale::getQuestion('auto')));
        $this->assertTrue(is_array(Zend_Locale::getQuestion('browser')));
        $this->assertTrue(is_array(Zend_Locale::getQuestion('environment')));
    }

    /**
     * test getBrowser
     * expected true
     */
    public function testgetBrowser()
    {
        Zend_LocaleTestHelper::resetObject();
        $value = new Zend_LocaleTestHelper();
        $list = $value->getBrowser();
        $this->assertTrue(isset($list['de']));
        $this->assertEquals(array('de' => 1, 'en_UK' => 0.5, 'en_US' => 0.5,
                                  'en' => 0.5, 'fr_FR' => 0.2, 'fr' => 0.2), $list);
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
        $this->assertTrue(Zend_Locale::isLocale($locale));
        $this->assertTrue(Zend_Locale::isLocale('de'));
        $this->assertTrue(Zend_Locale::isLocale('de_AT'));
        $this->assertTrue(Zend_Locale::isLocale('de_xx'));
        $this->assertFalse(Zend_Locale::isLocale('yy'));
        $this->assertFalse(Zend_Locale::isLocale(1234));
        $this->assertFalse(Zend_Locale::isLocale('', true));
        $this->assertFalse(Zend_Locale::isLocale('', false));
        $this->assertTrue(Zend_Locale::isLocale('auto'));
        $this->assertTrue(Zend_Locale::isLocale('browser'));
        $this->assertTrue(Zend_Locale::isLocale('environment'));

        set_error_handler(array($this, 'errorHandlerIgnore'));
        Zend_Locale::$compatibilityMode = true;
        $this->assertEquals('ar', Zend_Locale::isLocale($locale));
        $this->assertEquals('de', Zend_Locale::isLocale('de'));
        $this->assertEquals('de_AT', Zend_Locale::isLocale('de_AT'));
        $this->assertEquals('de', Zend_Locale::isLocale('de_xx'));
        $this->assertFalse(Zend_Locale::isLocale('yy'));
        $this->assertFalse(Zend_Locale::isLocale(1234));
        $this->assertFalse(Zend_Locale::isLocale('', true));
        $this->assertFalse(Zend_Locale::isLocale('', false));
        $this->assertTrue(is_string(Zend_Locale::isLocale('auto')));
        $this->assertTrue(is_string(Zend_Locale::isLocale('browser')));
        $this->assertTrue(is_string(Zend_Locale::isLocale('environment')));
        restore_error_handler();
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

    /**
     * Test getDefault
     */
    public function testgetDefault() {
        Zend_Locale::setDefault('de');
        $this->assertTrue(array_key_exists('de', Zend_Locale::getDefault()));

        // compatibility tests
        set_error_handler(array($this, 'errorHandlerIgnore'));
        Zend_Locale::$compatibilityMode = true;
        $this->assertTrue(array_key_exists('de', Zend_Locale::getDefault(Zend_Locale::BROWSER)));
        restore_error_handler();
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testZF3617() {
        $value = new Zend_Locale('en-US');
        $this->assertEquals('en_US', $value->toString());
    }

    /**
     * @ZF4963
     */
    public function testZF4963() {
        $value = new Zend_Locale();
        $locale = $value->toString();
        $this->assertTrue(!empty($locale));

        $this->assertTrue(Zend_Locale::isLocale(null));

        $value = new Zend_Locale(0);
        $value = $value->toString();
        $this->assertTrue(!empty($value));

        $this->assertFalse(Zend_Locale::isLocale(0));
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->_errorOccurred = true;
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

// Call Zend_LocaleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_LocaleTest::main") {
    Zend_LocaleTest::main();
}
