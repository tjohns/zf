<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_XmlTm
 */
require_once 'Zend/Translate/Adapter/XmlTm.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_XmlTmTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml');

        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_XmlTm);

        try {
            $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/nofile.xml', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }

        try {
            $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/failed.xml', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml');

        $this->assertEquals($adapter->toString(), 'XmlTm');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');

        $this->assertEquals($adapter->translate('Message 1'), 'Message 1');
        $this->assertEquals($adapter->_('Message 1'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5');
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');

        $this->assertEquals($adapter->isTranslated('Mess1'), true);
        $this->assertEquals($adapter->isTranslated('Mess6'), false);
        $this->assertEquals($adapter->isTranslated('Mess1', true), true);
        $this->assertEquals($adapter->isTranslated('Mess1', true, 'en'), true);
        $this->assertEquals($adapter->isTranslated('Mess1', false, 'es'), false);
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');

        $this->assertEquals($adapter->translate('Message 1'),       'Message 1');
        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'Message 2');

        $this->assertEquals($adapter->translate('Message 1', 'xx'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 1', 'en_US'), 'Message 1');

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'xx');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/XmlTm_test_de.xml', 'de', array('clear' => true));
        $this->assertEquals($adapter->translate('Message 1'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 8'), 'Message 8');
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions(), array('testoption' => 'testkey', 'clear' => false));
        $this->assertEquals($adapter->getOptions('testoption'), 'testkey');
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');

        $this->assertEquals($adapter->getLocale(), 'en');
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals($adapter->getLocale(), 'en');
        try {
            $adapter->setLocale('nolocale');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
        try {
            $adapter->setLocale('ar');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');
        $this->assertEquals($adapter->getList(), array('en' => 'en'));

        $this->assertFalse($adapter->isAvailable('fr'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
    }
}
