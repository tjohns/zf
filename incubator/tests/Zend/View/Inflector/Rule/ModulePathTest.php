<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Inflector_Rule_ModulePathTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View/Inflector/Rule/ModulePath.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';

/**
 * Test class for Zend_View_Inflector_Rule_ModulePath.
 */
class Zend_View_Inflector_Rule_ModulePathTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Inflector_Rule_ModulePath
     */
    public $rule;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Inflector_Rule_ModulePathTest");
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
        Zend_Controller_Front::getInstance()->resetInstance();
        $this->rule = new Zend_View_Inflector_Rule_ModulePath();
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

    /**
     * @return Zend_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return new Zend_Controller_Request_Http();
    }

    /**
     * @return void
     */
    public function testGetFrontControllerMultipleTimesRetrievesSameInstance()
    {
        $front    = Zend_Controller_Front::getInstance();
        $expected = $this->rule->getFrontController();
        $received = $this->rule->getFrontController();

        $this->assertSame($front, $expected);
        $this->assertSame($expected, $received);
    }

    /**
     * @return void
     */
    public function testGetRequestMultipleTimesRetrievesSameInstance()
    {
        $front   = $this->rule->getFrontController();
        $request = $this->_getRequest();
        $front->setRequest($request);

        $expected = $this->rule->getRequest();
        $received = $this->rule->getRequest();
        $this->assertSame($request, $expected);
        $this->assertSame($expected, $received);

    }

    /**
     * @return void
     */
    public function testGetDispatcherMultipleTimesRetrievesSameInstance()
    {
        $dispatcher = $this->rule->getDispatcher();
        $received   = $this->rule->getDispatcher();

        $this->assertSame($dispatcher, $received);
    }

    /**
     * @return void
     */
    public function testInflectTransformsName()
    {
        $request = $this->_getRequest();
        $request->setParams(array(
            'module'     => 'foo',
        ));
        $this->rule->getFrontController()->setRequest($request);

        $this->assertEquals('foo/views', $this->rule->inflect('foo'));
    }

    /**
     * @return void
     */
    public function testPassingParamsToInflectAffectsTransformation()
    {
        $request = $this->_getRequest();
        $request->setParams(array(
            'module'     => 'foobar',
            'controller' => 'baz',
        ));
        $this->rule->getFrontController()->setRequest($request);

        $this->assertEquals('foo/views', $this->rule->inflect('baz', array('module' => 'foo', 'bogus' => 'bogus')));
    }
}

if (PHPUnit_MAIN_METHOD == "Zend_View_Inflector_Rule_ModulePathTest::main") {
    Zend_View_Inflector_Rule_ModulePathTest::main();
}
