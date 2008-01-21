<?php
// Call Zend_Controller_Action_Helper_AutoCompleteTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_AutoCompleteTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Action/Helper/AutoCompleteDojo.php';
require_once 'Zend/Controller/Action/Helper/AutoCompleteScriptaculous.php';

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Layout.php';


/**
 * Test class for Zend_Controller_Action_Helper_AutoComplete.
 */
class Zend_Controller_Action_Helper_AutoCompleteTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_AutoCompleteTest");
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
        Zend_Controller_Action_Helper_AutoCompleteTest_LayoutOverride::$_mvcInstance = null;
        Zend_Controller_Action_HelperBroker::resetHelpers();

        $this->request = new Zend_Controller_Request_Http();
        $this->response = new Zend_Controller_Response_Cli();
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setRequest($this->request)->setResponse($this->response);

        $this->layout = Zend_Layout::startMvc();
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

    public function testConcreteImplementationsDeriveFromAutoCompleteBaseClass()
    {
        $this->markTestIncomplete();
    }

    public function testEncodeJsonProxiesToJsonActionHelper()
    {
        $this->markTestIncomplete();
    }

    public function testDojoHelperThrowsExceptionOnInvalidDataFormat()
    {
        $this->markTestIncomplete();
    }

    public function testDojoHelperEncodesToJson()
    {
        $this->markTestIncomplete();
    }

    public function testScriptaculousHelperThrowsExceptionOnInvalidDataFormat()
    {
        $this->markTestIncomplete();
    }

    public function testScriptaculousHelperCreatesHtmlMarkup()
    {
        $this->markTestIncomplete();
    }

    public function testPassingTrueSendParameterToDirectSendsResponse()
    {
        $this->markTestIncomplete();
    }
}

class Zend_Controller_Action_Helper_AutoCompleteTest_LayoutOverride extends Zend_Layout
{
    public static $_mvcInstance;
}

// Call Zend_Controller_Action_Helper_AutoCompleteTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_AutoCompleteTest::main") {
    Zend_Controller_Action_Helper_AutoCompleteTest::main();
}
