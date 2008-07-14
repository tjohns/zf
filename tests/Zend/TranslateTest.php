<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'message1'), 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testDefaultLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'message1'));
        $defaultLocale = new Zend_Locale();
        $this->assertEquals($defaultLocale->toString(), $lang->getLocale());
    }

    public function testGetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY , array(), 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);

        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/_files/testmsg_en.mo', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Gettext);

        $lang = new Zend_Translate(Zend_Translate::AN_TMX , dirname(__FILE__) . '/Translate/_files/translation_en.tmx', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Tmx);

        $lang = new Zend_Translate(Zend_Translate::AN_CSV , dirname(__FILE__) . '/Translate/_files/translation_en.csv', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Csv);

        $lang = new Zend_Translate(Zend_Translate::AN_XLIFF , dirname(__FILE__) . '/Translate/_files/translation_en.xliff', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Xliff);

        $lang = new Zend_Translate('qt' , dirname(__FILE__) . '/Translate/_files/translation_de.ts', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Qt);

        $lang = new Zend_Translate('xmltm' , dirname(__FILE__) . '/Translate/_files/XmlTm_test_en.xml', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_XmlTm);

        $lang = new Zend_Translate('tbx' , dirname(__FILE__) . '/Translate/_files/translation_en.tbx', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Tbx);
    }

    public function testSetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/_files/testmsg_en.mo', 'en');
        $lang->setAdapter(Zend_Translate::AN_ARRAY, array());
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);

        try {
            $lang->xxxFunction();
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testAddTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');

        $this->assertEquals('msg2', $lang->_('msg2'));

        $lang->addTranslation(array('msg2' => 'Message 2'), 'en');
        $this->assertEquals('Message 2', $lang->_('msg2'));
        $this->assertEquals('msg3',      $lang->_('msg3'));

        $lang->addTranslation(array('msg3' => 'Message 3'), 'en', array('clear' => true));
        $this->assertEquals('msg2',      $lang->_('msg2'));
        $this->assertEquals('Message 3', $lang->_('msg3'));
    }

    public function testGetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testSetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('en');
        $this->assertEquals('en', $lang->getLocale());

        $lang->setLocale('ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('ru_RU');
        $this->assertEquals('ru', $lang->getLocale());
    }

    public function testSetLanguage()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testGetLanguageList()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals(2, count($lang->getList()));
        $this->assertTrue(in_array('en', $lang->getList()));
        $this->assertTrue(in_array('ru', $lang->getList()));
    }

    public function testIsAvailable()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertTrue( $lang->isAvailable('en'));
        $this->assertTrue( $lang->isAvailable('ru'));
        $this->assertFalse($lang->isAvailable('fr'));
    }

    public function testTranslate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('Message 1 (en)', $lang->_('msg1', 'en'        ));
        $this->assertEquals('Message 1 (ru)', $lang->_('msg1'              ));
        $this->assertEquals('msg2',           $lang->_('msg2', 'en'        ));
        $this->assertEquals('msg2',           $lang->_('msg2'              ));
        $this->assertEquals('Message 1 (en)', $lang->translate('msg1', 'en'));
        $this->assertEquals('Message 1 (ru)', $lang->translate('msg1'      ));
        $this->assertEquals('msg2',           $lang->translate('msg2', 'en'));
        $this->assertEquals('msg2',           $lang->translate('msg2'      ));
    }

    public function testIsTranslated()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en_US');
        $this->assertTrue( $lang->isTranslated('msg1'             ));
        $this->assertFalse($lang->isTranslated('msg2'             ));
        $this->assertFalse($lang->isTranslated('msg1', false, 'en'));
        $this->assertFalse($lang->isTranslated('msg1', true,  'en'));
        $this->assertFalse($lang->isTranslated('msg1', false, 'ru'));
    }

    public function testWithOption()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV , dirname(__FILE__) . '/Translate/_files/translation_en2.csv', 'en', array('delimiter' => ','));
        $this->assertEquals('Message 1 (en)',  $lang->translate('Message 1' ));
        $this->assertEquals('Message 4 (en)',  $lang->translate('Message 4,'));
        $this->assertEquals('Message 5, (en)', $lang->translate('Message 5' ));
    }

    public function testDirectorySearch()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/_files/test', null, array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(2, count($lang->getList()));
        $this->assertTrue(in_array('de_AT', $lang->getList()));
        $this->assertTrue(in_array('en_GB', $lang->getList()));
    }

    public function testFileSearch()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/_files/test2', null, array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(2, count($lang->getList()));
        $this->assertTrue(in_array('de_AT', $lang->getList()));
        $this->assertTrue(in_array('de_DE', $lang->getList()));
    }
}
