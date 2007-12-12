<?php
// Call Zend_View_Helper_HeadTitleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HeadTitleTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_HeadTitle */
require_once 'Zend/View/Helper/HeadTitle.php';

/** Zend_View */
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_HeadTitle.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_HeadTitleTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HeadTitle
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HeadTitleTest");
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
        $regKey = Zend_View_Helper_Placeholder::REGISTRY_KEY;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_HeadTitle();
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

    public function testNamespaceRegisteredInPlaceholderAfterInstantiation()
    {
        $registry = Zend_Registry::get(Zend_View_Helper_Placeholder::REGISTRY_KEY);
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadTitle'));
    }

    public function testHeadTitleReturnsPlaceholderContainer()
    {
        $placeholder = $this->helper->headTitle();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_Placeholder_Container_Abstract);
    }

    public function testCanSetTitleViaHeadTitle()
    {
        $placeholder = $this->helper->headTitle('Foo Bar', 'SET');
        $this->assertContains('Foo Bar', $placeholder->toString());
    }

    public function testCanAppendTitleViaHeadTitle()
    {
        $placeholder = $this->helper->headTitle('Foo');
        $placeholder = $this->helper->headTitle('Bar');
        $this->assertContains('FooBar', $placeholder->toString());
    }

    public function testCanSetSeparatorViaHeadTitle()
    {
        $placeholder = $this->helper->headTitle('Foo');
        $placeholder = $this->helper->headTitle('Bar', 'APPEND', ' :: ');
        $this->assertContains('Foo :: Bar', $placeholder->toString());
    }

    public function testReturnedPlaceholderToStringContainsFullTitleElement()
    {
        $placeholder = $this->helper->headTitle('Foo');
        $placeholder = $this->helper->headTitle('Bar', 'APPEND', ' :: ');
        $this->assertEquals('<title>Foo :: Bar</title>', $placeholder->toString());
    }
}

// Call Zend_View_Helper_HeadTitleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HeadTitleTest::main") {
    Zend_View_Helper_HeadTitleTest::main();
}
