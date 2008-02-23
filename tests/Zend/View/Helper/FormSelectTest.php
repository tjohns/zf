<?php
// Call Zend_View_Helper_FormSelectTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormSelectTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View/Helper/FormSelect.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_FormSelect.
 */
class Zend_View_Helper_FormSelectTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormSelectTest");
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
        $this->helper = new Zend_View_Helper_FormSelect();
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

    public function testFormSelectWithNameOnlyCreatesEmptySelect()
    {
        $html = $this->helper->formSelect('foo');
        $this->assertRegExp('#<select[^>]+name="foo"#', $html);
        $this->assertContains('</select>', $html);
        $this->assertNotContains('<option', $html);
    }

    public function testFormSelectWithOptionsCreatesPopulatedSelect()
    {
        $html = $this->helper->formSelect('foo', null, null, array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<select[^>]+name="foo"#', $html);
        $this->assertContains('</select>', $html);
        $this->assertRegExp('#<option[^>]+value="foo".*?>Foobar</option>#', $html);
        $this->assertRegExp('#<option[^>]+value="baz".*?>Bazbat</option>#', $html);
        $this->assertEquals(2, substr_count($html, '<option'));
    }

    public function testFormSelectWithOptionsAndValueSelectsAppropriateValue()
    {
        $html = $this->helper->formSelect('foo', 'baz', null, array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="baz"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    public function testFormSelectWithMultipleAttributeCreatesMultiSelect()
    {
        $html = $this->helper->formSelect('foo', null, array('multiple' => true), array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<select[^>]+name="foo\[\]"#', $html);
        $this->assertRegExp('#<select[^>]+multiple="multiple"#', $html);
    }

    public function testFormSelectWithMultipleAttributeAndValuesCreatesMultiSelectWithSelectedValues()
    {
        $html = $this->helper->formSelect('foo', array('foo', 'baz'), array('multiple' => true), array('foo' => 'Foobar', 'baz' => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="foo"[^>]*selected.*?>Foobar</option>#', $html);
        $this->assertRegExp('#<option[^>]+value="baz"[^>]*selected.*?>Bazbat</option>#', $html);
    }

    /**
     * ZF-1930
     * @return void
     */
    public function testFormSelectWithZeroValueSelectsValue()
    {
        $html = $this->helper->formSelect('foo', 0, null, array('foo' => 'Foobar', 0 => 'Bazbat'));
        $this->assertRegExp('#<option[^>]+value="0"[^>]*selected.*?>Bazbat</option>#', $html);
    }
}

// Call Zend_View_Helper_FormSelectTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormSelectTest::main") {
    Zend_View_Helper_FormSelectTest::main();
}
