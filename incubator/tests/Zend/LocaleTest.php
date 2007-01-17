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

error_reporting( E_ALL | E_STRICT ); // now required for each test suite
// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

/**
 * Zend_Locale
 */
require_once 'Zend.php';
require_once 'Zend/Locale.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";

/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */
class Zend_LocaleTest extends PHPUnit_Framework_TestCase
{


    /**
     * test for initialisation without parameter
     * expected instance
     */
    public function testInit()
    {
        $value = new Zend_Locale();
        $this->assertTrue($value instanceof Zend_Locale,'Zend_Locale Object not returned');
    }


    /**
     * test for initialisation with locale parameter
     * expected instance
     */
    public function testInitLocale()
    {
        $value = new Zend_Locale('root');
        $this->assertTrue($value instanceof Zend_Locale,'Zend_Locale Object not returned');
    }


    /**
     * test for initialisation with environment search
     * expected instance
     */
    public function testInitSearchEnv()
    {
        $value = new Zend_Locale(Zend_Locale::ENVIRONMENT);
        $this->assertTrue($value instanceof Zend_Locale,'Zend_Locale Object not returned');
    }


    /**
     * test for initialisation with browser search
     * expected instance
     */
    public function testInitSearchHTTP()
    {
        $value = new Zend_Locale(Zend_Locale::BROWSER);
        $this->assertTrue($value instanceof Zend_Locale,'Zend_Locale Object not returned');
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
    }


    /**
     * test for unserialization
     * expected object
     */
    public function testLocaleUnSerialize()
    {
        $value = new Zend_Locale('de_DE');
        $serial = $value->serialize();
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
        $this->assertEquals($value->toString(), 'de_DE', 'Locale de_DE expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function test_ToString()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals($value->__toString(), 'de_DE', 'Value de_DE expected');
    }


    /**
     * test getDefault
     * expected true
     */
    public function testDefault()
    {
        $value = new Zend_Locale();
        $default = $value->getDefault();
        $this->assertTrue(is_array($default), 'No Default Locale found');
    }


    /**
     * test getDefaultBrowser
     * expected true
     */
    public function testDefaultBrowser()
    {
        $value = new Zend_Locale();
        $default = $value->getDefault(Zend_Locale::BROWSER);
        $this->assertTrue(is_array($default), 'No Default Locale found');
    }


    /**
     * test getDefaultEnvironment
     * expected true
     */
    public function testDefaultEnvironment()
    {
        $value = new Zend_Locale();
        $default = $value->getDefault(Zend_Locale::ENVIRONMENT);
        $this->assertTrue(is_array($default), 'No Default Locale found');
    }


    /**
     * test getDefaultFramework
     * expected true
     */
    public function testDefaultFramework()
    {
        $value = new Zend_Locale();
        $default = $value->getDefault(Zend_Locale::FRAMEWORK);
        $this->assertTrue(is_array($default), 'No Default Locale found');
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
        $this->assertEquals($value->toString(), 'en_US', 'Environment Locale not set');
    }


    /**
     * test setLocaleFailed
     * expected true
     */
    public function testsetLocaleFailed()
    {
        $value = new Zend_Locale('de_DE');
        $value->setLocale('en_AA');
        $this->assertEquals($value->toString(), 'en', 'Environment Locale not set');
    }

    /**
     * test setLocaleFailedRoot
     * expected true
     */
    public function testsetLocaleFailedRoot()
    {
        $value = new Zend_Locale();
        $value->setLocale('xx_AA');
        $this->assertEquals($value->toString(), 'root', 'Environment Locale not set');
    }


    /**
     * test getLanguageList
     * expected true
     */
    public function testgetLanguageList()
    {
        $value = new Zend_Locale();
        $list = $value->getLanguageList();
        $this->assertTrue(is_array($list), 'Language List not returned');
    }


    /**
     * test getLanguageListLocale
     * expected true
     */
    public function testgetLanguageListLocale()
    {
        $value = new Zend_Locale();
        $list = $value->getLanguageList('de');
        $this->assertTrue(is_array($list), 'Language List not returned');
    }


    /**
     * test getLanguageDisplay
     * expected true
     */
    public function testgetLanguageDisplay()
    {
        $value = new Zend_Locale('de_AT');
        $list = $value->getLanguageDisplay('de');
        $this->assertEquals($list, 'Deutsch', 'Language Display not returned');
    }


    /**
     * test getLanguageDisplay
     * expected true
     */
    public function testgetLanguageDisplayLocale()
    {
        $value = new Zend_Locale('de_AT');
        $list = $value->getLanguageDisplay('de','en');
        $this->assertEquals($list, 'German', 'Language Display not returned');
    }
    

    /**
     * test getLanguageDisplayFalse
     * expected false
     */
    public function testgetLanguageDisplayFalse()
    {
        $value = new Zend_Locale();
        $list = $value->getLanguageDisplay('xyz');
        $this->assertFalse(is_string($list), 'Language Display should be false');
    }


    /**
     * test getScriptList
     * expected true
     */
    public function testgetScriptList()
    {
        $value = new Zend_Locale();
        $list = $value->getScriptList();
        $this->assertTrue(is_array($list), 'Script List not returned');
    }


    /**
     * test getScriptList
     * expected true
     */
    public function testgetScriptListLocale()
    {
        $value = new Zend_Locale();
        $list = $value->getScriptList('de');
        $this->assertTrue(is_array($list), 'Script List not returned');
    }


    /**
     * test getScriptDisplay
     * expected true
     */
    public function testgetScriptDisplay()
    {
        $value = new Zend_Locale('de_AT');
        $list = $value->getScriptDisplay('Arab');
        $this->assertEquals($list, 'Arabisch', 'Script Display not returned');
    }


    /**
     * test getScriptDisplay
     * expected true
     */
    public function testgetScriptDisplayLocale()
    {
        $value = new Zend_Locale('de_AT');
        $list = $value->getScriptDisplay('Arab', 'en');
        $this->assertEquals($list, 'Arabic', 'Script Display not returned');
    }
    

    /**
     * test getScriptDisplayFalse
     * expected false
     */
    public function testgetScriptDisplayFalse()
    {
        $value = new Zend_Locale();
        $list = $value->getScriptDisplay('xyz');
        $this->assertFalse(is_string($list), 'Script Display should be false');
    }


    /**
     * test getRegionList
     * expected true
     */
    public function testgetRegionList()
    {
        $value = new Zend_Locale();
        $list = $value->getRegionList();
        $this->assertTrue(is_array($list), 'Region List not returned');
    }


    /**
     * test getRegionList
     * expected true
     */
    public function testgetRegionListLocale()
    {
        $value = new Zend_Locale();
        $list = $value->getRegionList('de');
        $this->assertTrue(is_array($list), 'Region List not returned');
    }
    

    /**
     * test getRegionDisplay
     * expected true
     */
    public function testgetRegionDisplay()
    {
        $value = new Zend_Locale('de_AT');
        $list = $value->getRegionDisplay('DE');
        $this->assertEquals($list, 'Deutschland', 'Region Display not returned');
    }
    

    /**
     * test getRegionDisplay
     * expected true
     */
    public function testgetRegionDisplayLocale()
    {
        $value = new Zend_Locale('de_AT');
        $list = $value->getRegionDisplay('DE','en');
        $this->assertEquals($list, 'Germany', 'Region Display not returned');
    }
    

    /**
     * test getRegionDisplayFalse
     * expected false
     */
    public function testgetRegionDisplayFalse()
    {
        $value = new Zend_Locale();
        $list = $value->getRegionDisplay('xyz');
        $this->assertFalse(is_string($list), 'Region Display should be false');
    }


    /**
     * test getCalendarList
     * expected true
     */
    public function testgetCalendarList()
    {
        $value = new Zend_Locale();
        $list = $value->getCalendarList();
        $this->assertTrue(is_array($list), 'Calendar List not returned');
    }


    /**
     * test getCalendarList
     * expected true
     */
    public function testgetCalendarListLocale()
    {
        $value = new Zend_Locale();
        $list = $value->getCalendarList('de');
        $this->assertTrue(is_array($list), 'Calendar List not returned');
    }


    /**
     * test getCalendarDisplay
     * expected true
     */
    public function testgetCalendarDisplay()
    {
        $value = new Zend_Locale('de');
        $list = $value->getCalendarDisplay('chinese');
        $this->assertEquals($list, 'Chinesischer Kalender', 'Calendar Display not returned');
    }


    /**
     * test getCalendarDisplay
     * expected true
     */
    public function testgetCalendarDisplayLocale()
    {
        $value = new Zend_Locale('de');
        $list = $value->getCalendarDisplay('chinese','en');
        $this->assertEquals($list, 'Chinese Calendar', 'Calendar Display not returned');
    }
    

    /**
     * test getCalendarFalse
     * expected false
     */
    public function testgetCalendarDisplayFalse()
    {
        $value = new Zend_Locale();
        $list = $value->getCalendarDisplay('xyz');
        $this->assertFalse(is_string($list), 'Calendar Display should be false');
    }


    /**
     * test getLanguage
     * expected true
     */
    public function testgetLanguage()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals($value->getLanguage(), 'de', 'No language found');
    }


    /**
     * test getRegion
     * expected true
     */
    public function testgetRegion()
    {
        $value = new Zend_Locale('de_DE');
        $this->assertEquals($value->getRegion(), 'DE', 'No region found');
    }


    /**
     * test getRegionEmpty
     * expected true
     */
    public function testgetRegionEmpty()
    {
        $value = new Zend_Locale('de');
        $this->assertEquals($value->getRegion(), '', 'No region found');
    }


    /**
     * test for equality
     * expected string
     */
    public function testEquals()
    {
        $value = new Zend_Locale('de_DE');
        $serial = new Zend_Locale('de_DE');
        $this->assertTrue($value->equals($serial),'Zend_Locale not equals');
    }


    /**
     * test for non equality
     * expected string
     */
    public function testNonEquals()
    {
        $value = new Zend_Locale('de_DE');
        $serial = new Zend_Locale('de_AT');
        $this->assertFalse($value->equals($serial),'Zend_Locale equal ?');
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
    }


    /**
     * test getQuestion
     * expected true
     */
    public function testgetQuestionLocale()
    {
        $value = new Zend_Locale();
        $list = $value->getQuestion('de');
        $this->assertTrue(isset($list['yes']), 'Question not returned');
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
        putenv("HTTP_ACCEPT_CHARSET=,iso-8859-1, utf-8, utf-16, *;q=0.1");
        $value = new Zend_Locale();
        $list = $value->getHTTPCharset();
        $this->assertTrue(isset($list['utf-8']), 'language array not returned');
    }


    /**
     * test getHttpCharset
     * expected true
     */
    public function testgetEmptyHttpCharset()
    {
        putenv("HTTP_ACCEPT_CHARSET=");
        $value = new Zend_Locale();
        $list = $value->getHTTPCharset();
        $this->assertTrue(empty($list), 'language array must be empty');
    }
}