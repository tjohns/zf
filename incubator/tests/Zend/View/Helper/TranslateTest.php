<?php
// Call Zend_View_Helper_TranslateTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_TranslateTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_Translate */
require_once 'Zend/View/Helper/Translate.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Translate */
require_once 'Zend/Translate.php';
require_once 'Zend/Translate/Adapter/Array.php';

/**
 * Test class for Zend_View_Helper_Translate.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_TranslateTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_Translate
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_TranslateTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $regKey = 'Zend_Translate';
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testCreationWithSetRegistry()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        Zend_Registry::set('Zend_Translate', $trans);

        $this->helper = new Zend_View_Helper_Translate();
        $this->assertEquals('eins', $this->helper->translate('one'));
        $this->assertEquals('three', $this->helper->translate('three'));
    }

    public function testCreationManually()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $this->helper = new Zend_View_Helper_Translate($trans);
        $this->assertEquals('eins', $this->helper->translate('one'));
        $this->assertEquals('three', $this->helper->translate('three'));
    }

    public function testCreationAfterwards()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $this->helper = new Zend_View_Helper_Translate();
        $this->helper->setTranslate($trans);
        $this->assertEquals('eins', $this->helper->translate('one'));
        $this->assertEquals('three', $this->helper->translate('three'));
    }

    public function testCreationWithout()
    {
        $this->helper = new Zend_View_Helper_Translate();
        $this->assertEquals('one', $this->helper->translate('one'));
        $this->assertEquals('three', $this->helper->translate('three'));
    }

    public function testCreationException()
    {
        try {
            $this->helper = new Zend_View_Helper_Translate('something');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }

        try {
            $this->helper = new Zend_View_Helper_Translate();
            $this->helper->setTranslate('something');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }

        try {
            $this->helper = new Zend_View_Helper_Translate();
            $this->helper->getLocale();
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }

        try {
            $this->helper = new Zend_View_Helper_Translate();
            $this->helper->setLocale();
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testSetLocale()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');

        $this->helper = new Zend_View_Helper_Translate($trans);
        $this->assertEquals('eins', $this->helper->translate('one'));
        $new = $this->helper->setLocale('it');
        $this->assertTrue($new instanceof Zend_View_Helper_Translate);
        $this->assertEquals('it', $new->getLocale());
        $this->assertEquals('uno', $this->helper->translate('one'));
    }

    public function testSimulateCreationInView()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');

        $this->helper = new Zend_View_Helper_Translate();
        Zend_Registry::set('Zend_Translate', $trans);

        $this->assertEquals('eins', $this->helper->translate('one'));
    }

    public function testCreationWithFluidInterface()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');

        $this->helper = new Zend_View_Helper_Translate();
        $locale = $this->helper->translate()->setTranslate($trans)->getLocale();

        $this->assertEquals('de', $locale);
    }

    public function testTranslateWithOptions()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', "two %1\$s" => "zwei %1\$s",
            "three %1\$s %2\$s" => "drei %1\$s %2\$s"), 'de');
        $trans->addTranslation(array('one' => 'uno', "two %1\$s" => "duo %2\$s",
            "three %1\$s %2\$s" => "tre %1\$s %2\$s"), 'it');

        $this->helper = new Zend_View_Helper_Translate($trans);
        $this->assertEquals("drei 100 200", $this->helper->translate("three %1\$s %2\$s", "100", "200"));
        $this->assertEquals("tre 100 200", $this->helper->translate("three %1\$s %2\$s", "100", "200", 'it'));
        $this->assertEquals("drei 100 200", $this->helper->translate("three %1\$s %2\$s", array("100", "200")));
        $this->assertEquals("tre 100 200", $this->helper->translate("three %1\$s %2\$s", array("100", "200"), 'it'));
    }
}

// Call Zend_View_Helper_TranslateTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_TranslateTest::main") {
    Zend_View_Helper_TranslateTest::main();
}
