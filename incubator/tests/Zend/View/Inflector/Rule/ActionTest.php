<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Inflector_Rule_ActionTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View/Inflector/Rule/Action.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';

/**
 * Test class for Zend_View_Inflector_Rule_Action.
 */
class Zend_View_Inflector_Rule_ActionTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Inflector_Rule_Action
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Inflector_Rule_ActionTest");
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
        $this->rule = new Zend_View_Inflector_Rule_Action();
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
            'action'     => 'baz'
        ));
        $this->rule->getFrontController()->setRequest($request);

        $this->assertEquals('bar.phtml', $this->rule->inflect('bar'));
    }

    /**
     * @return void
     */
    public function testPassingParamsToInflectAffectsTransformation()
    {
        $request = $this->_getRequest();
        $request->setParams(array(
            'action'     => 'baz'
        ));
        $this->rule->getFrontController()->setRequest($request);

        $this->assertEquals('baz.php', $this->rule->inflect('bar', array('suffix' => 'php', 'action' => 'baz', 'bogus' => 'bogus')));
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
}

if (PHPUnit_MAIN_METHOD == "Zend_View_Inflector_Rule_ActionTest::main") {
    Zend_View_Inflector_Rule_ActionTest::main();
}
