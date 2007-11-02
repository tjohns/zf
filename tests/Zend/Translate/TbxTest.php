<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Tbx
 */
require_once 'Zend/Translate/Adapter/Tbx.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_TbxTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx');

        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Tbx);

        try {
            $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/nofile.tbx', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }

        try {
            $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/failed.tbx', 'en');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx');

        $this->assertEquals($adapter->toString(), 'Tbx');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');

        $this->assertEquals($adapter->translate('Message 1'), 'Message 1');
        $this->assertEquals($adapter->_('Message 1'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 1', 'fr'), 'Message 1 (fr)');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5');
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');

        $this->assertEquals($adapter->isTranslated('Message 1'), true);
        $this->assertEquals($adapter->isTranslated('Message 6'), false);
        $this->assertEquals($adapter->isTranslated('Message 1', true), true);
        $this->assertEquals($adapter->isTranslated('Message 1', true, 'en'), true);
        $this->assertEquals($adapter->isTranslated('Message 1', false, 'es'), false);
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');

        $this->assertEquals($adapter->translate('Message 1'),       'Message 1');
        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'Message 2');

        $this->assertEquals($adapter->translate('Message 1', 'xx'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 1', 'en_US'), 'Message 1');

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.tbx', 'xx');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.tbx', 'de', array('clear' => true));
        $this->assertEquals($adapter->translate('Message 1'), 'Message 1');
        $this->assertEquals($adapter->translate('Message 8'), 'Message 8');
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions(), array('testoption' => 'testkey', 'clear' => false));
        $this->assertEquals($adapter->getOptions('testoption'), 'testkey');
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');

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
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');
        $this->assertEquals($adapter->getList(), array('en' => 'en', 'fr' => 'fr'));

        $this->assertTrue($adapter->isAvailable('fr'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }
}