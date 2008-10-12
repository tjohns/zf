<?php
/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Tmx
 */
require_once 'Zend/Translate/Adapter/Tmx.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_TmxTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Tmx);

        try {
            $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/nofile.tmx', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
        try {
            $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/failed.tmx', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx');
        $this->assertEquals('Tmx', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)',         $adapter->translate('Message 1'        ));
        $this->assertEquals('Message 1 (en)',         $adapter->_('Message 1'                ));
        $this->assertEquals('Message 1 (it)',         $adapter->translate('Message 1', 'it'  ));
        $this->assertEquals('Message 5 (en)',         $adapter->translate('Message 5'        ));
        $this->assertEquals('Küchen Möbel (en)',      $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'     ));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertTrue( $adapter->isTranslated('Message 1'             ));
        $this->assertFalse($adapter->isTranslated('Message 6'             ));
        $this->assertTrue( $adapter->isTranslated('Message 1', true       ));
        $this->assertTrue( $adapter->isTranslated('Message 1', true,  'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'         ));
        $this->assertEquals('Message 5 (en)', $adapter->translate('Message 5'         ));
        $this->assertEquals('Message 2',      $adapter->translate('Message 2', 'ru'   ));
        $this->assertEquals('Message 1',      $adapter->translate('Message 1', 'xx'   ));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.tmx', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('testoption' => 'testkey', 'clear' => false, 'scan' => null, 'locale' => 'en'), $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
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
            $adapter->setLocale('fr');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals(array('en' => 'en', 'de' => 'de', 'it' => 'it'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue( $adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'   ));
    }

    public function testIsoEncoding()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en3.tmx', 'en');
        $this->assertEquals('Message 1 (en)',         $adapter->translate('Message 1'        ));
        $this->assertEquals('Message 1 (en)',         $adapter->_('Message 1'                ));
        $this->assertEquals('Message 1 (it)',         $adapter->translate('Message 1', 'it'  ));
        $this->assertEquals('Message 5 (en)',         $adapter->translate('Message 5'        ));
        $this->assertEquals(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel (en)'), $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel')));
    }
}
