<?php

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */


/**
 * Zend_Translate_Adapter_Gettext
 */
require_once 'Zend/Translate/Adapter/Gettext.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_Translate_GettextTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo');

        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Gettext);
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo');

        $this->assertEquals($adapter->toString(), 'Gettext');
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo');

        $this->assertEquals($adapter->translate('Message 1'), 'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'), 'Message 5');
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmsg_en.mo', 'en');

        $this->assertEquals($adapter->translate('Message 1'),       'Message 1 (en)');
        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'Message 2');

        $adapter->addTranslation('ru', dirname(__FILE__) . '/_files/testmsg_ru(koi8-r).mo');
        $this->assertEquals($adapter->translate('Message 2', 'ru'), 'óÏÏÂİÅÎÉÅ 2 (ru)');

        $this->assertEquals($adapter->translate('Message 5'),       'Message 5');
        $this->assertEquals($adapter->translate('Message 5', 'ru'), 'Message 5');
    }
}
