<?php
// Call Zend_Form_Decorator_FileTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_FileTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Decorator/File.php';

require_once 'Zend/Form/Element/File.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_Errors
 */
class Zend_Form_Decorator_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_FileTest");
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
        $this->decorator = new Zend_Form_Decorator_File();
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

    public function testRenderReturnsInitialContentIfNoViewPresentInElement()
    {
        $element = new Zend_Form_Element_File('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function setupSingleElement()
    {
        $element = new Zend_Form_Element_File('foo');
        $element->addValidator('Count', 1)
                ->setView($this->getView());
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function setupMultiElement()
    {
        $element = new Zend_Form_Element_File('foo');
        $element->addValidator('Count', 1)
                ->setMultiFile(2)
                ->setView($this->getView());
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function testRenderSingleFiles()
    {
        $this->setupSingleElement();
        $test = $this->decorator->render(null);
        $this->assertRegexp('#foo#s', $test);
    }

    public function testRenderMultiFiles()
    {
        $this->setupMultiElement();
        $test = $this->decorator->render(null);
        $this->assertRegexp('#foo\[\]#s', $test);
    }

    public function setupElementWithMaxFileSize()
    {
        $element = new Zend_Form_Element_File('foo');
        $element->addValidator('Count', 1)
                ->setView($this->getView())
                ->setMaxFileSize(3000);
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function testRenderMaxFileSize()
    {
        $this->setupElementWithMaxFileSize();
        $test = $this->decorator->render(null);
        $this->assertRegexp('#MAX_FILE_SIZE#s', $test);
        $this->assertRegexp('#3000#s', $test);
    }
}

// Call Zend_Form_Decorator_FileTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_FileTest::main") {
    Zend_Form_Decorator_FileTest::main();
}
