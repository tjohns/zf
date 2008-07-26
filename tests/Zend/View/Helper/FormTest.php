<?php
// Call Zend_FormTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View/Helper/Form.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_Form
 */
class Zend_View_Helper_FormTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormTest");
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
        $this->view   = new Zend_View();
        $this->helper = new Zend_View_Helper_Form();
        $this->helper->setView($this->view);
        ob_start();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        ob_end_clean();
    }

    public function testFormHelperCreatesFormWithProvidedContent()
    {
        $html = $this->helper->form('foo', null, 'foobar');
        $this->assertRegexp('#<form[^>]+id="foo".*?>#', $html);
        $this->assertContains('</form>', $html);
        $this->assertContains('foobar', $html);
    }

    public function testFormHelperOmitsIdAndNamePropertiesIfBlank()
    {
        $html = $this->helper->form('', 'foobar');
        $this->assertNotRegexp('/id="/', $html);
    }

    public function testPassingBooleanFalseContentRendersOnlyOpeningTag()
    {
        $html = $this->helper->form('login', false);
        $this->assertContains('<form', $html);
        $this->assertNotContains('</form>', $html);
    }

    public function testFormShouldNotRenderNameAttribute()
    {
        $html = $this->helper->form('foo', null, 'foobar');
        $this->assertNotRegexp('#<form[^>]+name="foo".*?>#', $html);
    }
}

// Call Zend_View_Helper_FormTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormTest::main") {
    Zend_View_Helper_FormTest::main();
}
