<?php

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

require_once 'Zend/Controller/Plugin/ErrorHandler.php';

class Zend_Layout_FunctionalTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->frontController->setControllerDirectory(dirname(__FILE__) . '/_files/functional-test-app/controllers/');

        // create an instance of the ErrorHandler so we can make sure it will point to our specially named ErrorController
        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $plugin->setErrorHandlerController('ZendLayoutFunctionalTestError')
               ->setErrorHandlerAction('error');
        $this->frontController->registerPlugin($plugin, 100);

        Zend_Layout::startMvc(dirname(__FILE__) . '/_files/functional-test-app/layouts/');
    }

    public function testTest()
    {
        // go to the test controller for this funcitonal test
        $this->dispatch('/zend-layout-functional-test-test/missing-view-script');
        $this->assertEquals($this->response->getBody(), "[DEFAULT_LAYOUT]\n(ErrorController::errorAction output)[DEFAULT_LAYOUT]");
    }
    
}
