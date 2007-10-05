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

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

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
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_HeadTitle();
        Zend_Controller_Front::getInstance()->resetInstance();
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

    public function testHelperIsHeadTitle()
    {
        $this->assertEquals(get_class($this->helper), 'Zend_View_Helper_HeadTitle');
    }
    
    /**
     * @return void
     */
    public function testToStringASingleValue()
    {
        $this->helper['foo'] = 'bar';
        $this->helper['bar'] = 'baz';
        $this->assertEquals('bar', $this->helper['foo']);
        $this->assertEquals('baz', $this->helper['bar']);

        $this->helper->set('foo');
        $this->assertEquals(1, count($this->helper));
        $this->assertEquals('<title>foo</title>', $this->helper->toString());
    }
    
    public function testToStringCalledFromHelperMethod()
    {
        $this->helper->headTitle('my title');
        $this->assertEquals('<title>my title</title>', $this->helper->toString());
    }
    
    public function testToStringPrefixPostfixSeparatorCalledFromHelperMethod()
    {
        $this->helper->headTitle('my title', 'NAME OF SITE', 'SITE.COM', ' >> ');
        $this->assertEquals('<title>NAME OF SITE >> my title >> SITE.COM</title>', $this->helper->toString());
    }
    
    
}

// Call Zend_View_Helper_HeadTitleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HeadTitleTest::main") {
    Zend_View_Helper_HeadTitleTest::main();
}
?>
