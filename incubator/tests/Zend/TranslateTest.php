<?php

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 */


/**
 * Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 */
class Zend_TranslateTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array());
        $this->assertTrue($lang instanceof Zend_Translate);
    }

    public function testLocaleInitialization()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array(), 'en');
        $this->assertEquals($lang->getLocale(), new Zend_Locale('en'));
        $this->assertEquals($lang->getLanguage(), 'en');
    }

    public function testDefaultLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array());

        $defaultLocale = new Zend_Locale();
        $this->assertEquals($lang->getLocale(), $defaultLocale);
        $this->assertEquals($lang->getLanguage(), $defaultLocale->toString());
    }

    public function testGetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/_files/testmsg_en.mo', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Gettext);
    }

    public function testSetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/_files/testmsg_en.mo', 'en');

        $lang->setAdapter(Zend_Translate::AN_ARRAY, array());
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);
    }

    public function testAddTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');

        $this->assertEquals($lang->_('msg2'), 'msg2');

        $lang->addTranslation('en', array('msg2' => 'Message 2'));
        $this->assertEquals($lang->_('msg2'), 'Message 2');

        $lang->addTranslation('en', array('msg3' => 'Message 3'), true);
        $this->assertEquals($lang->_('msg2'), 'msg2');
        $this->assertEquals($lang->_('msg3'), 'Message 3');
    }

    public function testGetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals($lang->getLocale(), new Zend_Locale('en'));
    }

    public function testGetLanguage()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals($lang->getLanguage(), 'en');
    }

    public function testSetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation('ru', array('msg1' => 'Message 1 (ru)'));

        $this->assertEquals($lang->getLocale(), new Zend_Locale('en'));

        $lang->setLocale('ru');
        $this->assertEquals($lang->getLocale(), new Zend_Locale('ru'));
    }

    public function testSetLanguage()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation('ru', array('msg1' => 'Message 1 (ru)'));

        $this->assertEquals($lang->getLanguage(), 'en');

        $lang->setLanguage('ru');
        $this->assertEquals($lang->getLanguage(), 'ru');
    }

    public function testGetLanguageList()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation('ru', array('msg1' => 'Message 1 (ru)'));

        $this->assertEquals(count($lang->getLanguageList()), 2);
        $this->assertTrue(in_array('en', $lang->getLanguageList()));
        $this->assertTrue(in_array('ru', $lang->getLanguageList()));
    }

    public function testIsAvailable()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation('ru', array('msg1' => 'Message 1 (ru)'));

        $this->assertTrue($lang->isAvaiable('en'));
        $this->assertTrue($lang->isAvaiable('ru'));
        $this->assertFalse($lang->isAvaiable('fr'));
    }

    public function testTranslate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en');
        $lang->addTranslation('ru', array('msg1' => 'Message 1 (ru)'));

        $this->assertEquals($lang->_('msg1'), 'Message 1 (en)');
        $this->assertEquals($lang->_('msg1', 'ru'), 'Message 1 (ru)');

        $this->assertEquals($lang->_('msg2'), 'msg2');
        $this->assertEquals($lang->_('msg2', 'ru'), 'msg2');

        $this->assertEquals($lang->translate('msg1'), 'Message 1 (en)');
        $this->assertEquals($lang->translate('msg1', 'ru'), 'Message 1 (ru)');

        $this->assertEquals($lang->translate('msg2'), 'msg2');
        $this->assertEquals($lang->translate('msg2', 'ru'), 'msg2');
    }
}
