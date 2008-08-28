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
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }

        try {
            $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/failed.xml', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml');
        $this->assertEquals('XmlTm', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');
        $this->assertEquals('Message 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1', $adapter->_('Message 1'));
        $this->assertEquals('Message 5', $adapter->translate('Message 5'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');
        $this->assertTrue( $adapter->isTranslated('Mess1'));
        $this->assertFalse($adapter->isTranslated('Mess6'));
        $this->assertTrue( $adapter->isTranslated('Mess1', true));
        $this->assertTrue( $adapter->isTranslated('Mess1', true,  'en'));
        $this->assertFalse($adapter->isTranslated('Mess1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');
        $this->assertEquals('Message 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 5', $adapter->translate('Message 5'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'   ));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'   ));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/XmlTm_test_de.xml', 'de', array('clear' => true));
        $this->assertEquals('Message 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('testoption' => 'testkey', 'clear' => false, 'scan' => null, 'locale' => 'en'), $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
        try {
            $adapter->setLocale('ar');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en.xml', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $this->assertFalse($adapter->isAvailable('fr'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
    }

    public function testIsoEncoding()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/XmlTm_test_en2.xml', 'en');
        $this->assertEquals('Message 1 (en)',         $adapter->translate('Mess1'        ));
        $this->assertEquals('Message 1 (en)',         $adapter->_('Mess1'                ));
        $this->assertEquals(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel (en)'), $adapter->translate('Cooking Furniture'));
        $this->assertEquals('Cooking Furniture (en)', $adapter->translate(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel')));
    }
}
