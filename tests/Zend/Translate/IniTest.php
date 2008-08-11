<?php
/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */

/**
 * Zend_Translate_Adapter_Ini
 */
require_once 'Zend/Translate/Adapter/Ini.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_IniTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_IniTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Ini);

        try {
            $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/nofile.ini', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini');
        $this->assertEquals('Ini', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en');
        $this->assertEquals('Nachricht 1 (de)', $adapter->translate('Message_1'));
        $this->assertEquals('Nachricht 1 (de)', $adapter->_(        'Message_1'));
        $this->assertEquals('Nachricht 2 (de)', $adapter->translate('Message_2'));

        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en', array('separator' => ','));
        $this->assertEquals('Nachricht 1 (de)', $adapter->translate('Message_1' ));
        $this->assertEquals('Nachricht 2 (de)', $adapter->translate('Message_2' ));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en_US');
        $this->assertTrue( $adapter->isTranslated('Message_1'             ));
        $this->assertFalse($adapter->isTranslated('Message_6'             ));
        $this->assertTrue( $adapter->isTranslated('Message_1', true       ));
        $this->assertFalse($adapter->isTranslated('Message_1', true,  'en'));
        $this->assertFalse($adapter->isTranslated('Message_1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en');
        $this->assertEquals('Nachricht 1 (de)', $adapter->translate('Message_1'         ));
        $this->assertEquals('Message_5',        $adapter->translate('Message_5'         ));
        $this->assertEquals('Message_2',        $adapter->translate('Message_2', 'ru'   ));
        $this->assertEquals('Message_1',        $adapter->translate('Message_1', 'xx'   ));
        $this->assertEquals('Nachricht 1 (de)', $adapter->translate('Message_1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation.ini', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('testoption' => 'testkey', 'clear' => false, 'scan' => null, 'locale' => 'en'), $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en');
        $this->assertEquals('Nachricht 1 (de)', $adapter->translate('Message_1'         ));
        $this->assertEquals('Message_5',        $adapter->translate('Message_5'         ));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation2.ini', 'en', array('clear' => true));
        $this->assertEquals('Message_1',        $adapter->translate('Message_1'));
        $this->assertEquals('Nachricht 5 (de)', $adapter->translate('Message_5'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en');
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
            $adapter->setLocale('de');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation.ini', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation.ini', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());

        $this->assertTrue( $adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue( $adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'   ));
    }
}

// Call Zend_Translate_IniTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_IniTest::main") {
    Zend_Translate_IniTest::main();
}
