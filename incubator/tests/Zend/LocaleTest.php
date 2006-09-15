<?php
/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */


/**
 * Zend_Locale
 */
require_once 'Zend.php';
Zend::loadClass('Zend_Locale');

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


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
        $value = new Zend_Locale(Zend_Locale::AUTOSEARCH_ENV);
        $this->assertTrue($value instanceof Zend_Locale,'Zend_Locale Object not returned');
    }


    /**
     * test for initialisation with browser search
     * expected instance
     */
    public function testInitSearchHTTP()
    {
        $value = new Zend_Locale(Zend_Locale::AUTOSEARCH_HTTP);
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
        $default = $value->getDefault(Zend_Locale::AUTOSEARCH_HTTP);
        $this->assertTrue(is_array($default), 'No Default Locale found');
    }


    /**
     * test getDefaultEnvironment
     * expected true
     */
    public function testDefaultEnvironment()
    {
        $value = new Zend_Locale();
        $default = $value->getDefault(Zend_Locale::AUTOSEARCH_ENV);
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
     * test getLocale
     * expected true
     */
    public function testgetLocale()
    {
        $value = new Zend_Locale('de_DE');
        $default = $value->getLocale();
        $this->assertEquals($default->toString(), 'de_DE', 'Environment Locale failed');
    }


    /**
     * test setLocale
     * expected true
     */
    public function testsetLocale()
    {
        $value = new Zend_Locale('de_DE');
        $value->setLocale('en_US');
        $this->assertEquals($value->getLocale()->toString(), 'en_US', 'Environment Locale not set');
    }


    /**
     * test setLocaleFailed
     * expected true
     */
    public function testsetLocaleFailed()
    {
        $value = new Zend_Locale('de_DE');
        $value->setLocale('en_AA');
        $this->assertEquals($value->getLocale()->toString(), 'en', 'Environment Locale not set');
    }

    /**
     * test setLocaleFailedRoot
     * expected true
     */
    public function testsetLocaleFailedRoot()
    {
        $value = new Zend_Locale();
        $value->setLocale('xx_AA');
        $this->assertEquals($value->getLocale()->toString(), 'root', 'Environment Locale not set');
    }


    /**
     * test getList
     * expected true
     */
    public function testgetList()
    {
        $value = new Zend_Locale();
        $list = $value->getList();
        $this->assertTrue(is_array($list), 'Environment Locale not found');
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
}