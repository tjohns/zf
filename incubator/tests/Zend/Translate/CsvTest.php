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
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');

        $this->assertEquals($adapter->toString(), 'Csv');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');

        $this->assertSame($adapter->translate('Message 1'), 'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5');
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');

        $this->assertEquals($adapter->translate('Message 1'),       'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'Message 2');
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');

        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions(), array('testoption' => 'testkey'));
        $this->assertEquals($adapter->getOptions('testoption'), 'testkey');
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');

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
            $adapter->setLocale('de');
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }
}
