<?php
// Call Zend_View_Helper_FormSubmitTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormSubmitTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View/Helper/FormSubmit.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_FormSubmit.
 */
class Zend_View_Helper_FormSubmitTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormSubmitTest");
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
        $this->helper = new Zend_View_Helper_FormSubmit();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper, $this->view);
    }

    public function testRendersSubmitInput()
    {
        $html = $this->helper->formSubmit(array(
            'name'    => 'foo',
            'value'   => 'Submit!',
        ));
        $this->assertRegexp('/<input[^>]*?(type="submit")/', $html);
    }

    /**
     * ZF-2254
     */
    public function testCanDisableSubmitButton()
    {
        $html = $this->helper->formSubmit(array(
            'name'    => 'foo',
            'value'   => 'Submit!',
            'attribs' => array('disable' => true)
        ));
        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * ZF-2239
     */
    public function testValueAttributeIsAlwaysRendered()
    {
        $html = $this->helper->formSubmit(array(
            'name'    => 'foo',
            'value'   => '',
        ));
        $this->assertRegexp('/<input[^>]*?(value="")/', $html);
    }
}

// Call Zend_View_Helper_FormSubmitTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormSubmitTest::main") {
    Zend_View_Helper_FormSubmitTest::main();
}
