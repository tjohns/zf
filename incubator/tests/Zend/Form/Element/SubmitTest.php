<?php
// Call Zend_Form_Element_SubmitTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_SubmitTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Element/Submit.php';
require_once 'Zend/Translate.php';

/**
 * Test class for Zend_Form_Element_Submit
 */
class Zend_Form_Element_SubmitTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_SubmitTest");
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
        $this->element = new Zend_Form_Element_Submit('foo');
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

    public function testSubmitElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testSubmitElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testSubmitElementUsesSubmitHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $options = $decorator->getOptions();
        $this->assertEquals('formSubmit', $options['helper']);
    }

    public function testGetValueReturnsNameIfNoValuePresent()
    {
        $this->assertEquals($this->element->getName(), $this->element->getValue());
    }

    public function testGetValueReturnsTranslatedValueIfTranslatorIsRegistered()
    {
        $translations = include dirname(__FILE__) . '/../_files/locale/array.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $this->element->setTranslator($translate->getAdapter())
                      ->setValue('submit');
        $test = $this->element->getValue();
        $this->assertEquals($translations['submit'], $test);
    }
}

// Call Zend_Form_Element_SubmitTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_SubmitTest::main") {
    Zend_Form_Element_SubmitTest::main();
}
