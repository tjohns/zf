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
class Zend_Translate_Adapter_XmlTmTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_Adapter_XmlTmTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_XmlTm);

        try {
            $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/nofile.xmltm', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('is not readable', $e->getMessage());
        }

        try {
            $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/failed.xmltm', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Mismatched tag at line', $e->getMessage());
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm');
        $this->assertEquals('XmlTm', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Mess1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.xmltm', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.xmltm', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('testoption' => 'testkey', 'clear' => false, 'scan' => null, 'locale' => 'en'), $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 5 (en)', $adapter->translate('Message 5'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.xmltm', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 5', $adapter->translate('Message 5'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
        try {
            $adapter->setLocale('ar');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('has to be added before it can be used', $e->getMessage());
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.xmltm', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertFalse($adapter->isAvailable('fr'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/testxmltm', 'de_AT', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $adapter->getList());
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
    }

    public function testOptionLocaleFilename()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/testxmltm', 'de_DE', array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $adapter->getList());
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
    }

    public function testZF3937()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en.xmltm', 'en');
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_empty.xmltm', 'de');

        $this->assertEquals('en', $adapter->getLocale());
        try {
            $adapter->setLocale('de');
            $this->fail('Empty translations should not be settable');
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('No translation for the language', $e->getMessage());
        }
    }

    public function testIsoEncoding()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_en3.xmltm', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 6', $adapter->_('Message 6'));
        $this->assertEquals(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel (en)'), $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel')));
    }

    public function testWithoutEncoding()
    {
        $adapter = new Zend_Translate_Adapter_XmlTm(dirname(__FILE__) . '/_files/translation_withoutencoding.xmltm', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }
}

// Call Zend_Translate_Adapter_XmlTmTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_Adapter_XmlTmTest::main") {
    Zend_Translate_Adapter_XmlTmTest::main();
}
