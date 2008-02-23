<?php
// Call Zend_Form_Element_MultiCheckboxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_MultiCheckboxTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Form/Element/MultiCheckbox.php';

/**
 * Test class for Zend_Form_Element_MultiCheckbox
 */
class Zend_Form_Element_MultiCheckboxTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_MultiCheckboxTest");
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
        $this->element = new Zend_Form_Element_MultiCheckbox('foo');
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

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function testMultiCheckboxElementSubclassesMultiElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Multi);
    }

    public function testMultiCheckboxElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testMultiCheckboxElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testHelperAttributeSetToFormMultiCheckboxByDefault()
    {
        $this->assertEquals('formMultiCheckbox', $this->element->getAttrib('helper'));
    }

    public function testMultiCheckboxElementUsesMultiCheckboxHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formMultiCheckbox', $helper);
    }

    public function testCanDisableIndividualMultiCheckboxOptions()
    {
        $this->element->setMultiOptions(array(
                'foo'  => 'Foo',
                'bar'  => 'Bar',
                'baz'  => 'Baz',
                'bat'  => 'Bat',
                'test' => 'Test',
            ))
            ->setAttrib('disable', array('baz', 'test'));
        $html = $this->element->render($this->getView());
        foreach (array('baz', 'test') as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching disabled option for ' . $test);
            }
            $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $m[1]);
        }
        foreach (array('foo', 'bar', 'bat') as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching option for ' . $test);
            }
            $this->assertNotRegexp('/<input[^>]*?(disabled="disabled")/', $m[1], var_export($m, 1));
        }
    }

    public function testSpecifiedSeparatorIsUsedWhenRendering()
    {
        $this->element->setMultiOptions(array(
                'foo'  => 'Foo',
                'bar'  => 'Bar',
                'baz'  => 'Baz',
                'bat'  => 'Bat',
                'test' => 'Test',
            ))
            ->setSeparator('--FooBarFunSep--');
        $html = $this->element->render($this->getView());
        $this->assertContains($this->element->getSeparator(), $html);
        $count = substr_count($html, $this->element->getSeparator());
        $this->assertEquals(4, $count);
    }
}

// Call Zend_Form_Element_MultiCheckboxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_MultiCheckboxTest::main") {
    Zend_Form_Element_MultiCheckboxTest::main();
}
