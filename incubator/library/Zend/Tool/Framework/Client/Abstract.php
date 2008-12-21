<?php

require_once 'Zend/Tool/Framework/Client/Registry.php';
require_once 'Zend/Tool/Framework/Client/Dispatcher.php';

abstract class Zend_Tool_Framework_Client_Abstract
{

    /**
     * @var Zend_Tool_Framework_Client_Request
     */
    protected $_request = null;

    /**
     * @var Zend_Tool_Framework_Client_Response
     */
    protected $_response = null;
    
    /**
     * @var Zend_Tool_Framework_Loader_Interface
     */
    protected $_loader = null;

    /**
     * @var Zend_Tool_Framework_Provider_Registry
     */
    protected $_providerRegistry = null;

    /**
     * @var Zend_Tool_Framework_Manifest_Registry
     */
    protected $_manifestRegistry = null;
    
    /**
     * This method should be implemented by the client implementation to 
     * construct and set custom inflectors, request and response objects. 
     */
    abstract protected function _init();
    
    /**
     * This method should be implemented by the client implementation to
     * parse out and setup the request objects action, provider and parameter
     * information.
     */
    abstract protected function _preHandle();
    
    /**
     * This method should be implemented by the client implementation to
     * take the output of the response object and return it (in an client
     * specific way) back to the Tooling Client.
     */
    abstract protected function _postHandle();
    
    final public function __construct()
    {
        $this->_init();
        
        if ($this->_loader == null) {
            require_once 'Zend/Tool/Framework/Loader/IncludePathLoader.php';
            $this->_loader = new Zend_Tool_Framework_Loader_IncludePathLoader();
        }

        $this->_loader->load();
        
        Zend_Tool_Framework_Provider_Registry::getInstance()->process();
        Zend_Tool_Framework_Manifest_Registry::getInstance()->process();
        
        $clientRegistry = Zend_Tool_Framework_Client_Registry::getInstance();
        
        if (!isset($clientRegistry->request)) {
            require_once 'Zend/Tool/Framework/Client/Request.php';
            $clientRegistry->request = new Zend_Tool_Framework_Client_Request();
            $this->_request = $clientRegistry->request;
        }
        
        if (!isset($clientRegistry->response)) {
            require_once 'Zend/Tool/Framework/Client/Response.php';
            $clientRegistry->response = new Zend_Tool_Framework_Client_Response();
            $this->_response = $clientRegistry->response;
        }
        
    }
    
    /**
     * This method should be called in order to "handle" a Tooling Client
     * request that has come to the client that has been implemented.
     */
    final public function handle()
    {

        try {
        
            $this->_preHandle();

            if ($this->_request->isDispatchable()) {

	            if ($this->_request->getActionName() == null) {
	                throw new Zend_Tool_Framework_Client_Exception('Endpoint failed to setup the action name.');
	            }
	    
	            if ($this->_request->getProviderName() == null) {
	                throw new Zend_Tool_Framework_Client_Exception('Endpoint failed to setup the provider name.');
	            }
	            
	            ob_start();

	            $dispatcher = new Zend_Tool_Framework_Client_Dispatcher();
	            $dispatcher->setRequest($this->_request)
	                ->setResponse($this->_response)
	                ->dispatch();

            }

        } catch (Exception $exception) {
            $this->_response->setException($exception);
        }
        
        if (ob_get_level() && (($content = ob_get_clean()) != '')) {
            $this->_response->setContent($content);
        }
        
        $this->_postHandle();
    }
    
}
