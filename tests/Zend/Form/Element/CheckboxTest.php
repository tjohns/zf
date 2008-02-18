<?php
// Call Zend_Form_Element_CheckboxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_CheckboxTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Element/Checkbox.php';

/**
 * Test class for Zend_Form_Element_Checkbox
 */
class Zend_Form_Element_CheckboxTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_CheckboxTest");
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
        $this->element = new Zend_Form_Element_Checkbox('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testCheckboxElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testCheckboxElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testCheckboxElementUsesCheckboxHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formCheckbox', $helper);
    }

    public function testCheckedFlagIsFalseByDefault()
    {
        $this->assertFalse($this->element->checked);
    }

    public function testCheckedAttributeNotRenderedByDefault()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $html = $this->element->render($view);
        $this->assertNotContains('checked="checked"', $html);
    }

    public function testCheckedAttributeRenderedWhenCheckedFlagTrue()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $this->element->checked = true;
        $html = $this->element->render($view);
        $this->assertContains('checked="checked"', $html);
    }

    public function testValueInitiallyZero()
    {
        $this->assertEquals(0, $this->element->getValue());
    }

    public function testSettingNonNullValueSetsValueToOne()
    {
        $this->testValueInitiallyZero();
        $this->element->setValue('');
        $this->assertEquals(1, $this->element->getValue());
        $this->element->setValue('foo');
        $this->assertEquals(1, $this->element->getValue());
    }

    public function testSettingNullValueSetsValueToZero()
    {
        $this->testSettingNonNullValueSetsValueToOne();
        $this->element->setValue(null);
        $this->assertEquals(0, $this->element->getValue());
    }

    public function testCheckedFlagTogglesWithValue()
    {
        $this->testCheckedFlagIsFalseByDefault();
        $this->testSettingNonNullValueSetsValueToOne();
        $this->assertTrue($this->element->checked);
        $this->element->setValue(null);
        $this->assertFalse($this->element->checked);
    }
}

// Call Zend_Form_Element_CheckboxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_CheckboxTest::main") {
    Zend_Form_Element_CheckboxTest::main();
}
