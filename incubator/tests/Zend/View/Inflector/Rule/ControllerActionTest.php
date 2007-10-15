<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Inflector_Rule_ControllerActionTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View/Inflector/Rule/ControllerAction.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';

/**
 * Test class for Zend_View_Inflector_Rule_ControllerAction.
 */
class Zend_View_Inflector_Rule_ControllerActionTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Inflector_Rule_ControllerAction
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Inflector_Rule_ControllerActionTest");
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
        $this->rule = new Zend_View_Inflector_Rule_ControllerAction();
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
            'controller' => 'foo',
            'action'     => 'baz'
        ));
        $this->rule->getFrontController()->setRequest($request);

        $this->assertEquals('foo/bar.phtml', $this->rule->inflect('bar'));
    }

    /**
     * @return void
     */
    public function testPassingParamsToInflectAffectsTransformation()
    {
        $request = $this->_getRequest();
        $request->setParams(array(
            'controller' => 'foo',
            'action'     => 'baz'
        ));
        $this->rule->getFrontController()->setRequest($request);

        $this->assertEquals('bar/baz.php', $this->rule->inflect('bar', array('suffix' => 'php', 'controller' => 'bar', 'action' => 'baz', 'bogus' => 'bogus')));
    }

    /**
     * @return void
     */
    public function testDefaultSuffixIsPhtml()
    {
        $this->assertEquals('phtml', $this->rule->getSuffix());
    }

    /**
     * @return void
     */
    public function testSuffixAccessorsSetAndRetrieveSuffix()
    {
        $this->rule->setSuffix('foo');
        $this->assertEquals('foo', $this->rule->getSuffix());
    }

    /**
     * @return void
     */
    public function testDefaultPathSpecAccessible()
    {
        $this->assertEquals(':controller/:action.:suffix', $this->rule->getPathSpec());
    }

    /**
     * @return void
     */
    public function testPathSpecIsModifiableViaAccessors()
    {
        $spec = 'foo/views/:controller/:action.php';
        $this->rule->setPathSpec($spec);
        $this->assertEquals($spec, $this->rule->getPathSpec());
    }
}

if (PHPUnit_MAIN_METHOD == "Zend_View_Inflector_Rule_ControllerActionTest::main") {
    Zend_View_Inflector_Rule_ControllerActionTest::main();
}
