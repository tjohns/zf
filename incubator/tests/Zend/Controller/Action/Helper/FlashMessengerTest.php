<?php

require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

require_once 'Zend/Controller/Action/HelperBroker.php';

require_once 'Zend/Session.php';

class Zend_Controller_Action_Helper_FlashMessengerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $savePath = ini_get('session.save_path');
        if (strpos($savePath, ';')) {
            $savePath = explode(';', $savePath);
            $savePath = array_pop($savePath);
        }
        if (empty($savePath)) {
            $this->markTestSkipped('Cannot test FlashMessenger due to unavailable session save path');
        }

        Zend_Session::start();
    }
       
    public function testLoadFlashMessenger()
    {
        $controller = Zend_Controller_Front::getInstance();
        $controller->resetInstance();
        $controller->setControllerDirectory(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-flash-messenger/');
        $controller->setResponse(new Zend_Controller_Response_Cli());
        
        $controller->returnResponse(true);
        $response = $controller->dispatch($request);
        $this->assertEquals('Zend_Controller_Action_Helper_FlashMessenger123456', $response->getBody());
    }
}
