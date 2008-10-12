<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Qt
 */
require_once 'Zend/Translate/Adapter/Qt.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_QtTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Qt);

        try {
            $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/nofile.ts', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts');
        $this->assertEquals('Qt', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 1', $adapter->_(        'Message 1'));
        $this->assertEquals('Message 5',   $adapter->translate('Message 5'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de_AT');
        $this->assertTrue( $adapter->isTranslated('Message 1'             ));
        $this->assertFalse($adapter->isTranslated('Message 6'             ));
        $this->assertTrue( $adapter->isTranslated('Message 1', true       ));
        $this->assertFalse($adapter->isTranslated('Message 1', true,  'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'      ));
        $this->assertEquals('Message 5',   $adapter->translate('Message 5'      ));
        $this->assertEquals('Message 2',   $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1',   $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1', 'de'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_de.ts', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('testoption' => 'testkey', 'clear' => false, 'scan' => null, 'locale' => 'de'), $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');
        $this->assertEquals('de', $adapter->getLocale());
        $locale = new Zend_Locale('de');
        $adapter->setLocale($locale);
        $this->assertEquals('de', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
        try {
            $adapter->setLocale('en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de.ts', 'de');
        $this->assertEquals(array('de' => 'de'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_de.ts', 'en');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('en'));
        $locale = new Zend_Locale('de');
        $this->assertTrue( $adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'   ));
    }

    public function testIsoEncoding()
    {
        $adapter = new Zend_Translate_Adapter_Qt(dirname(__FILE__) . '/_files/translation_de2.ts', 'de');
        $this->assertEquals('Nachricht 1',         $adapter->translate('Message 1'        ));
        $this->assertEquals('Nachricht 1',         $adapter->_('Message 1'                ));
        $this->assertEquals(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel (en)'), $adapter->translate('Cooking Furniture'));
        $this->assertEquals('Cooking Furniture (en)', $adapter->translate(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel')));
    }
}
