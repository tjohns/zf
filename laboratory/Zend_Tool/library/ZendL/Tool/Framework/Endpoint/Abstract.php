<?php

abstract class ZendL_Tool_Framework_Endpoint_Abstract
{

    /**
     * @var ZendL_Tool_Framework_Endpoint_Request
     */
    protected $_request = null;

    /**
     * @var ZendL_Tool_Framework_Endpoint_Response
     */
    protected $_response = null;
    
    /**
     * @var ZendL_Tool_Framework_Loader_Interface
     */
    protected $_loader = null;

    /**
     * @var ZendL_Tool_Framework_Provider_Registry
     */
    protected $_providerRegistry = null;

    /**
     * @var ZendL_Tool_Framework_Manifest_Registry
     */
    protected $_manifestRegistry = null;
    
    /**
     * This method should be implemented by the endpoint implementation to 
     * construct and set custom inflectors, request and response objects. 
     */
    abstract protected function _init();
    
    /**
     * This method should be implemented by the endpoint implementation to
     * parse out and setup the request objects action, provider and parameter
     * information.
     */
    abstract protected function _preHandle();
    
    /**
     * This method should be implemented by the endpoint implementation to
     * take the output of the response object and return it (in an endpoint
     * specific way) back to the Tooling Client.
     */
    abstract protected function _postHandle();
    
    final public function __construct()
    {
        $this->_init();
        
        if ($this->_loader == null) {
            $this->_loader = new ZendL_Tool_Framework_Loader_IncludePathLoader();
        }
        
        $this->_loader->load();
        
        ZendL_Tool_Framework_Provider_Registry::getInstance()->process();
        ZendL_Tool_Framework_Manifest_Registry::getInstance()->process();
        
        $endpointRegistry = ZendL_Tool_Framework_Endpoint_Registry::getInstance();
        
        if (!isset($endpointRegistry->request)) {
            $endpointRegistry->request = new ZendL_Tool_Framework_Endpoint_Request();
            $this->_request = $endpointRegistry->request;
        }
        
        if (!isset($endpointRegistry->response)) {
            $endpointRegistry->response = new ZendL_Tool_Framework_Endpoint_Response();
            $this->_response = $endpointRegistry->response;
        }

    }
    
    /**
     * This method should be called in order to "handle" a Tooling Client
     * request that has come to the endpoint that has been implemented.
     */
    final public function handle()
    {

        try {
        
            $this->_preHandle();

            if ($this->_request->isDispatchable()) {

	            if ($this->_request->getActionName() == null) {
	                throw new ZendL_Tool_Framework_Endpoint_Exception('Endpoint failed to setup the action name.');
	            }
	    
	            if ($this->_request->getProviderName() == null) {
	                throw new ZendL_Tool_Framework_Endpoint_Exception('Endpoint failed to setup the provider name.');
	            }
	            
	            ob_start();

	            $dispatcher = new ZendL_Tool_Framework_Endpoint_Dispatcher();
	            $dispatcher->setRequest($this->_request)
	                ->setResponse($this->_response)
	                ->dispatch();

            }

        } catch (Exception $exception) {
            $this->_response->setException($exception);
        }
        
        if (ob_get_level() && ($content = ob_get_clean() != '')) {
            $this->_response->setContent($content);
        }
        
        $this->_postHandle();
    }
    
}
