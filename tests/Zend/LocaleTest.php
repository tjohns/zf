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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_LocaleTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Locale::setCache($cache);
    }

    /**
     * test for object creation
     * expected object instance
     */
    public function testObjectCreation()
    {
        $this->assertTrue(is_string(Zend_Locale::isLocale('de')), 'true expected');

        $this->assertTrue(new Zend_Locale() instanceof Zend_Locale,'Zend_Locale Object not returned');
        $this->assertTrue(new Zend_Locale('root') instanceof Zend_Locale,'Zend_Locale Object not returned');
        $this->assertTrue(new Zend_Locale(Zend_Locale::ENVIRONMENT) instanceof Zend_Locale,'Zend_Locale Object not returned');
        $this->assertTrue(new Zend_Locale(Zend_Locale::BROWSER) instanceof Zend_Locale,'Zend_Locale Object not returned');

        $locale = new Zend_Locale('de');
        $this->assertTrue(new Zend_Locale($locale) instanceof Zend_Locale,'Zend_Locale Object not returned');

        $locale = new Zend_Locale('auto');
        $this->assertTrue(new Zend_Locale($locale) instanceof Zend_Locale,'Zend_Locale Object not returned');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testSerialize()
    {
        $value = new Zend_Locale('de_DE');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Locale not serialized');

        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Locale not unserialized');
    }


    /**
     * test toString
     * expected string
     */
    public function testToString()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals('de_DE', $value->toString(), 'Locale de_DE expected' );
        $this->assertEquals('de_DE', $value->__toString(), 'Value de_DE expected');
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
        $this->assertTrue(array_key_exists('de', $default), 'No default locale found');

        $default = $value->getDefault();
        $this->assertTrue(is_array($default), 'No Default Locale found');

        $default = $value->getDefault(Zend_Locale::BROWSER);
        $this->assertTrue(is_array($default), 'No Default Locale found');

        $default = $value->getDefault(Zend_Locale::ENVIRONMENT);
        $this->assertTrue(is_array($default), 'No Default Locale found');

        $default = $value->getDefault(Zend_Locale::FRAMEWORK);
        $this->assertTrue(is_array($default), 'No Default Locale found');
    }


    /**
     * test getEnvironment
     * expected true
     */
    public function testLocaleDetail()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals('de', $value->getLanguage());
        $this->assertEquals('AT', $value->getRegion()  );

        $value = new Zend_Locale('en_US');
        $this->assertEquals('en', $value->getLanguage());
        $this->assertEquals('US', $value->getRegion()  );

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
        $this->assertTrue(is_array($default), 'No Environment Locale found');
    }


    /**
     * test getBrowser
     * expected true
     */
    public function testBrowser()
    {
        $value = new Zend_Locale();
        $default = $value->getBrowser();
        $this->assertTrue(is_array($default), 'No Environment Locale found');
    }


    /**
     * test clone
     * expected true
     */
    public function testCloneing()
    {
        $value = new Zend_Locale('de_DE');
        $newvalue = clone $value;
        $this->assertEquals($value->toString(), $newvalue->toString(), 'Cloning Locale failed');
    }


    /**
     * test setLocale
     * expected true
     */
    public function testsetLocale()
    {
        $value = new Zend_Locale('de_DE');
        $value->setLocale('en_US');
        $this->assertEquals('en_US', $value->toString(), 'Environment Locale not set');

        $value->setLocale('en_AA');
        $this->assertEquals('en', $value->toString(), 'Environment Locale not set');

        $value->setLocale('xx_AA');
        $this->assertEquals('root', $value->toString(), 'Environment Locale not set');

        $value->setLocale('auto');
        $this->assertTrue(is_string($value->toString()), 'Automatic Locale not found');

        $value->setLocale('browser');
        $this->assertTrue(is_string($value->toString()), 'Browser Locale not found');

        $value->setLocale('environment');
        $this->assertTrue(is_string($value->toString()), 'Environment Locale not found');
    }


    /**
     * test getLanguageTranslationList
     * expected true
     */
    public function testgetLanguageTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getLanguageTranslationList();
        $this->assertTrue(is_array($list), 'Language List not returned');
        $list = $value->getLanguageTranslationList('de');
        $this->assertTrue(is_array($list), 'Language List not returned');
    }


    /**
     * test getLanguageTranslation
     * expected true
     */
    public function testgetLanguageTranslation()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals('Deutsch', $value->getLanguageTranslation('de'),       'Language Display not returned');
        $this->assertEquals('German',  $value->getLanguageTranslation('de', 'en'), 'Language Display not returned');
        $this->assertFalse($value->getLanguageTranslation('xyz'), 'Language Display should be false');
        $this->assertTrue(is_string($value->getLanguageTranslation('de', 'auto')), 'Language Display not returned');
    }


    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getScriptTranslationList();
        $this->assertTrue(is_array($list), 'Script List not returned');
        
        $list = $value->getScriptTranslationList('de');
        $this->assertTrue(is_array($list), 'Script List not returned');
    }


    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslation()
    {
        $value = new Zend_Locale('de_AT');
        $this->assertEquals('Arabisch', $value->getScriptTranslation('Arab'),       'Script Display not returned');
        $this->assertEquals('Arabic',   $value->getScriptTranslation('Arab', 'en'), 'Script Display not returned');
        $this->assertFalse($value->getScriptTranslation('xyz'), 'Script Display should be false');
    }


    /**
     * test getCountryTranslationList
     * expected true
     */
    public function testgetCountryTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getCountryTranslationList();
        $this->assertTrue(is_array($list), 'Region List not returned');

        $list = $value->getCountryTranslationList('de');
        $this->assertTrue(is_array($list), 'Region List not returned');
    }


    /**
     * test getCountryTranslation
     * expected true
     */
    public function testgetCountryTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals('Deutschland', $value->getCountryTranslation('DE'),       'No country found');
        $this->assertEquals('Germany',     $value->getCountryTranslation('DE', 'en'), 'No country found');
        $this->assertFalse($value->getCountryTranslation('xyz'), 'Country Display should be false');
    }


    /**
     * test getTerritoryTranslationList
     * expected true
     */
    public function testgetTerritoryTranslationList()
    {
        $value = new Zend_Locale();
        $list = $value->getTerritoryTranslationList();
        $this->assertTrue(is_array($list), 'Territory List not returned');

        $list = $value->getTerritoryTranslationList('de');
        $this->assertTrue(is_array($list), 'Territory List not returned');
    }


    /**
     * test getTerritoryTranslation
     * expected true
     */
    public function testgetTerritoryTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals('Afrika', $value->getTerritoryTranslation('002'), 'No territory found');
        $this->assertEquals('Africa', $value->getTerritoryTranslation('002', 'en'), 'No territory found');
        $this->assertFalse($value->getTerritoryTranslation('xyz'), 'Territory Display should be false');
        $this->assertTrue(is_string($value->getTerritoryTranslation('002', 'auto')), 'No territory found');
    }


    /**
     * test getTranslation
     * expected true
     */
    public function testgetTranslation()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertTrue(is_array($value->getTranslation('xx')));
        $this->assertTrue(in_array('currency_sign', $value->getTranslation('xx')));

        $this->assertEquals('Deutsch', $value->getTranslation('de', 'language'      ));
        $this->assertEquals('German',  $value->getTranslation('de', 'language', 'en'));
        $this->assertFalse($value->getTranslation('xx', 'language'));

        $this->assertEquals('Lateinisch', $value->getTranslation('Latn', 'script'      ));
        $this->assertEquals('Latin',      $value->getTranslation('Latn', 'script', 'en'));
        $this->assertFalse($value->getTranslation('xyxy', 'script'));

        $this->assertEquals('Österreich', $value->getTranslation('AT', 'country'      ));
        $this->assertEquals('Austria',    $value->getTranslation('AT', 'country', 'en'));
        $this->assertFalse($value->getTranslation('xx', 'country'));

        $this->assertEquals('Afrika', $value->getTranslation('002', 'territory'      ));
        $this->assertEquals('Africa', $value->getTranslation('002', 'territory', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'territory'));

        $this->assertEquals('Chinesischer Kalender', $value->getTranslation('chinese', 'calendar'      ));
        $this->assertEquals('Chinese Calendar',      $value->getTranslation('chinese', 'calendar', 'en'));
        $this->assertFalse($value->getTranslation('xxxxxxx', 'calendar'));

        $this->assertEquals('Januar',  $value->getTranslation('1', 'month'      ));
        $this->assertEquals('January', $value->getTranslation('1', 'month', 'en'));
        $this->assertFalse($value->getTranslation('x', 'month'));

        $this->assertEquals('Jan', $value->getTranslation('1', 'month_short'      ));
        $this->assertEquals('Jan', $value->getTranslation('1', 'month_short', 'en'));
        $this->assertFalse($value->getTranslation('x', 'month_short'));

        $this->assertEquals('J', $value->getTranslation('1', 'month_narrow'      ));
        $this->assertEquals('J', $value->getTranslation('1', 'month_narrow', 'en'));
        $this->assertFalse($value->getTranslation('x', 'month_narrow'));

        $this->assertEquals('Sonntag', $value->getTranslation('sun', 'day'      ));
        $this->assertEquals('Sunday',  $value->getTranslation('sun', 'day', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'day'));

        $this->assertEquals('So',  $value->getTranslation('sun', 'day_short'      ));
        $this->assertEquals('Sun', $value->getTranslation('sun', 'day_short', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'day_short'));

        $this->assertEquals('S', $value->getTranslation('sun', 'day_narrow'      ));
        $this->assertEquals('S', $value->getTranslation('sun', 'day_narrow', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'day_narrow'));

        $this->assertEquals('EEEE, d. MMMM yyyy', $value->getTranslation('full', 'dateformat'      ));
        $this->assertEquals('EEEE, MMMM d, yyyy', $value->getTranslation('full', 'dateformat', 'en'));
        $this->assertFalse($value->getTranslation('xxxx', 'dateformat'));

        $this->assertEquals("HH:mm:ss v",  $value->getTranslation('full', 'timeformat'      ));
        $this->assertEquals('h:mm:ss a v', $value->getTranslation('full', 'timeformat', 'en'));
        $this->assertFalse($value->getTranslation('xxxx', 'timeformat'));

        $this->assertEquals('Wien',       $value->getTranslation('Europe/Vienna', 'timezone'         ));
        $this->assertEquals('St. John’s', $value->getTranslation('America/St_Johns', 'timezone', 'en'));
        $this->assertFalse($value->getTranslation('xxxx', 'timezone'));

        $this->assertEquals('Euro', $value->getTranslation('EUR', 'currency'      ));
        $this->assertEquals('Euro', $value->getTranslation('EUR', 'currency', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'currency'));

        $this->assertEquals('SFr.', $value->getTranslation('CHF', 'currency_sign'      ));
        $this->assertEquals('SwF',  $value->getTranslation('CHF', 'currency_sign', 'en'));
        $this->assertFalse($value->getTranslation('xxx', 'currency_sign'));

        $this->assertTrue(array_key_exists('EUR', $value->getTranslation('AT', 'currency_detail')));
        $this->assertTrue(array_key_exists('EUR', $value->getTranslation('AT', 'currency_detail', 'en')));
        $this->assertFalse($value->getTranslation('xxx', 'currency_detail'));

        $this->assertTrue(in_array('014', $value->getTranslation('002', 'territory_detail')));
        $this->assertTrue(in_array('014', $value->getTranslation('002', 'territory_detail', 'en')));
        $this->assertFalse($value->getTranslation('xxx', 'territory_detail'));

        $this->assertTrue(in_array('DE', $value->getTranslation('de', 'language_detail')));
        $this->assertTrue(in_array('DE', $value->getTranslation('de', 'language_detail', 'en')));
        $this->assertFalse($value->getTranslation('xxx', 'language_detail'));

        $this->assertEquals('[a-z]', $value->getTranslation(null, 'characters', 'en'));

        $this->assertTrue(in_array('014', $value->getTranslation('002', 'territory_detail', 'auto')));
        $this->assertTrue(in_array('014', $value->getTranslation('002', 'territory_detail', 'browser')));
        $this->assertTrue(in_array('014', $value->getTranslation('002', 'territory_detail', 'environment')));
    }


    /**
     * test getTranslationList
     * expected true
     */
    public function testgetTranslationList()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertTrue(is_array($value->getTranslationList()));
        $this->assertTrue(in_array('language', $value->getTranslationList()));

        $this->assertTrue(in_array('Deutsch', $value->getTranslationList('language')));
        $this->assertTrue(in_array('German', $value->getTranslationList('language', 'en')));

        $this->assertTrue(in_array('Lateinisch', $value->getTranslationList('script')));
        $this->assertTrue(in_array('Latin', $value->getTranslationList('script', 'en')));

        $this->assertTrue(in_array('Österreich', $value->getTranslationList('country')));
        $this->assertTrue(in_array('Austria', $value->getTranslationList('country', 'en')));

        $this->assertTrue(in_array('Afrika', $value->getTranslationList('territory')));
        $this->assertTrue(in_array('Africa', $value->getTranslationList('territory', 'en')));

        $this->assertTrue(in_array('Chinesischer Kalender', $value->getTranslationList('calendar')));
        $this->assertTrue(in_array('Chinese Calendar', $value->getTranslationList('calendar', 'en')));

        $this->assertTrue(in_array('Januar', $value->getTranslationList('month')));
        $this->assertTrue(in_array('January', $value->getTranslationList('month', 'en')));

        $this->assertTrue(in_array('Jan', $value->getTranslationList('month_short')));
        $this->assertTrue(in_array('Jan', $value->getTranslationList('month_short', 'en')));

        $this->assertTrue(in_array('J', $value->getTranslationList('month_narrow')));
        $this->assertTrue(in_array('J', $value->getTranslationList('month_narrow', 'en')));

        $this->assertTrue(in_array('Sonntag', $value->getTranslationList('day')));
        $this->assertTrue(in_array('Sunday', $value->getTranslationList('day', 'en')));

        $this->assertTrue(in_array('So', $value->getTranslationList('day_short')));
        $this->assertTrue(in_array('Sun', $value->getTranslationList('day_short', 'en')));

        $this->assertTrue(in_array('S', $value->getTranslationList('day_narrow')));
        $this->assertTrue(in_array('S', $value->getTranslationList('day_narrow', 'en')));

        $this->assertTrue(in_array('EEEE, d. MMMM yyyy', $value->getTranslationList('dateformat')));
        $this->assertTrue(in_array('EEEE, MMMM d, yyyy', $value->getTranslationList('dateformat', 'en')));

        $this->assertTrue(in_array("HH:mm:ss v", $value->getTranslationList('timeformat')));
        $this->assertTrue(in_array("h:mm:ss a z", $value->getTranslationList('timeformat', 'en')));

        $this->assertTrue(in_array('Wien', $value->getTranslationList('timezone')));
        $this->assertTrue(in_array("St. John’s", $value->getTranslationList('timezone', 'en')));

        $this->assertTrue(in_array('Euro', $value->getTranslationList('currency')));
        $this->assertTrue(in_array('Euro', $value->getTranslationList('currency', 'en')));

        $this->assertTrue(in_array('SFr.', $value->getTranslationList('currency_sign')));
        $this->assertTrue(in_array('SwF', $value->getTranslationList('currency_sign', 'en')));

        $this->assertTrue(in_array('EUR', $value->getTranslationList('currency_detail')));
        $this->assertTrue(in_array('EUR', $value->getTranslationList('currency_detail', 'en')));

        $this->assertTrue(in_array('AU NF NZ', $value->getTranslationList('territory_detail')));
        $this->assertTrue(in_array('AU NF NZ', $value->getTranslationList('territory_detail', 'en')));

        $this->assertTrue(in_array('CZ', $value->getTranslationList('language_detail')));
        $this->assertTrue(in_array('CZ', $value->getTranslationList('language_detail', 'en')));

        $this->assertTrue(in_array('currency', $value->getTranslationList('xxx')));
        $this->assertTrue(in_array('currency', $value->getTranslationList()));

        $this->assertTrue(in_array('[a-z]', $value->getTranslationList('characters')));
        $this->assertTrue(in_array('[a-z]', $value->getTranslationList('characters', 'en')));

        $this->assertTrue(in_array('CZ', $value->getTranslationList('language_detail', 'auto')));
        $this->assertTrue(in_array('CZ', $value->getTranslationList('language_detail', 'browser')));
        $this->assertTrue(in_array('CZ', $value->getTranslationList('language_detail', 'environment')));
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
        $this->assertTrue($value->equals($serial),'Zend_Locale not equals');
        $this->assertFalse($value->equals($serial2),'Zend_Locale equal ?');
    }


    /**
     * test getQuestion
     * expected true
     */
    public function testgetQuestion()
    {
        $value = new Zend_Locale();
        $list = $value->getQuestion();
        $this->assertTrue(isset($list['yes']), 'Question not returned');

        $list = $value->getQuestion('de');
        $this->assertTrue(isset($list['yes']), 'Question not returned');

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
        putenv("HTTP_ACCEPT_LANGUAGE=,de,en-UK-US;q=0.5,fr_FR;q=0.2");
        $value = new Zend_Locale();
        $list = $value->getBrowser();
        $this->assertTrue(isset($list['de']), 'language array not returned');
    }


    /**
     * test getHttpCharset
     * expected true
     */
    public function testgetHttpCharset()
    {
        putenv("HTTP_ACCEPT_CHARSET=");
        $value = new Zend_Locale();
        $list = $value->getHttpCharset();
        $this->assertTrue(empty($list), 'language array must be empty');

        putenv("HTTP_ACCEPT_CHARSET=,iso-8859-1, utf-8, utf-16, *;q=0.1");
        $value = new Zend_Locale();
        $list = $value->getHttpCharset();
        $this->assertTrue(isset($list['utf-8']), 'language array not returned');
    }


    /**
     * test isLocale
     * expected boolean
     */
    public function testIsLocale()
    {
        $locale = new Zend_Locale('ar');
        $this->assertEquals('ar',    Zend_Locale::isLocale($locale), "ar expected"   );
        $this->assertEquals('de',    Zend_Locale::isLocale('de'),    "de expected"   );
        $this->assertEquals('de_AT', Zend_Locale::isLocale('de_AT'), "de_AT expected");
        $this->assertEquals('de',    Zend_Locale::isLocale('de_xx'), "de expected"   );
        $this->assertFalse(Zend_Locale::isLocale('yy'), "false expected");
        $this->assertFalse(Zend_Locale::isLocale(1234), "false expected");
        $locale = Zend_Locale::isLocale('', true);
        $this->assertTrue(is_string($locale), "true expected");
        $this->assertTrue(is_string(Zend_Locale::isLocale('auto')), "true expected");
        $this->assertTrue(is_string(Zend_Locale::isLocale('browser')), "true expected");
        $this->assertTrue(is_string(Zend_Locale::isLocale('environment')), "true expected");
    }


    /**
     * test isLocale
     * expected boolean
     */
    public function testGetLocaleList()
    {
        $this->assertTrue(is_array(Zend_Locale::getLocaleList()));
    }
}
