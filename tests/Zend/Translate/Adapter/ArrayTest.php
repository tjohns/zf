<?php
/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */

/**
 * Zend_Translate_Adapter_Array
 */
require_once 'Zend/Translate/Adapter/Array.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_Translate_Adapter_ArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * Error flag
     *
     * @var boolean
     */
    protected $_errorOccurred = false;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_Adapter_ArrayTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testCreate()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Zend_Translate_Adapter_Array(array());
        restore_error_handler();
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);

        try {
            $adapter = new Zend_Translate_Adapter_Array('hastofail', 'en');
            $this->fail('Exception expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Error including array or file', $e->getMessage());
        }

        try {
            $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/failed.php', 'en');
            $this->fail('Exception expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Error including array or file', $e->getMessage());
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Array(array('msg1' => 'Message 1 (en)', 'msg2' => 'Message 2 (en)', 'msg3' => 'Message 3 (en)'));
        $this->assertEquals('Array', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.php', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.php', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('testoption' => 'testkey', 'clear' => false, 'scan' => null, 'locale' => 'en', 'ignore' => '.', 'disableNotices' => false), $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.php', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php', 'en');
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

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('de');
        restore_error_handler();
        $this->assertEquals('de', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(array('msg1'), 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/testarray', 'de_AT', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/testarray', 'de_DE', array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testLoadArrayFile()
    {
        $adapter = new Zend_Translate_Adapter_Array(dirname(__FILE__) . '/_files/translation_en.php');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);
    }

    public function testDisablingNotices()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Zend_Translate_Adapter_Array(array());
        $this->assertTrue($this->_errorOccurred);
        restore_error_handler();
        $this->_errorOccurred = false;
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Zend_Translate_Adapter_Array(array(), 'en', array('disableNotices' => true));
        $this->assertFalse($this->_errorOccurred);
        restore_error_handler();
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);
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

// Call Zend_Translate_Adapter_ArrayTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_Adapter_ArrayTest::main") {
    Zend_Translate_Adapter_ArrayTest::main();
}
