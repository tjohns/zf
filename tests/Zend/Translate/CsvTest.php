<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Csv
 */
require_once 'Zend/Translate/Adapter/Csv.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_CsvTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Csv);

        try {
            $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/nofile.csv', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');
        $this->assertEquals('Csv', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_(        'Message 1'));
        $this->assertEquals('Message 5 (en)', $adapter->translate('Message 5'));

        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en2.csv', 'en', array('delimiter' => ','));
        $this->assertEquals('Message 1 (en)',  $adapter->translate('Message 1' ));
        $this->assertEquals('Message 4 (en)',  $adapter->translate('Message 4,'));
        $this->assertEquals('Message 5, (en)', $adapter->translate('Message 5' ));
        $this->assertEquals('Message 6,addon (en)', $adapter->translate('Message 6,addon,' ));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en_US');
        $this->assertTrue( $adapter->isTranslated('Message 1'             ));
        $this->assertFalse($adapter->isTranslated('Message 8'             ));
        $this->assertTrue( $adapter->isTranslated('Message 1', true       ));
        $this->assertFalse($adapter->isTranslated('Message 1', true,  'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'         ));
        $this->assertEquals('Message 5 (en)', $adapter->translate('Message 5'         ));
        $this->assertEquals('Message 2',      $adapter->translate('Message 2', 'ru'   ));
        $this->assertEquals('Message 1',      $adapter->translate('Message 1', 'xx'   ));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.csv', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('delimiter' => ';', 'testoption' => 'testkey', 'clear' => false,
                                  'scan' => null, 'locale' => 'en', 'length' => 0, 'enclosure' => '"'),
                                  $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
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
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.csv', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());

        $this->assertTrue( $adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue( $adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'   ));
    }
}
