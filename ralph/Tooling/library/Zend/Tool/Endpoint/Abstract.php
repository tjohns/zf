<?php

abstract class Zend_Tool_Endpoint_Abstract
{
    /**
     * @var Zend_Tool_Manifest
     */
    protected $_manifest = null;
    
    /**
     * @var Zend_Tool_Endpoint_Inflector_Interface
     */
    protected $_inflector = null;
    
    /**
     * @var Zend_Tool_Endpoint_Request
     */
    protected $_request = null;
    
    /**
     * @var Zend_Tool_Endpoint_Response
     */
    protected $_response = null;
    
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
        
        if ($this->_inflector == null) {
            throw new Zend_Tool_Endpoint_Exception('An inflector for this endpoint was not registered in _init().');
        }
        
        if ($this->_request == null) {
            $this->_request = new Zend_Tool_Endpoint_Request();
        }
        
        if ($this->_response == null) {
            $this->_response = new Zend_Tool_Endpoint_Response();
        }
        
        $this->_request->setInflector($this->_inflector);
        
        
    }
    
    public function setManifest(Zend_Tool_Manifest $manifest)
    {
        $this->_manifest = $manifest;
        return $this;
    }
    
    public function getManifest()
    {
        if ($this->_manifest == null) {
            $this->setManifest(new Zend_Tool_Manifest());
        }
        
        return $this->_manifest;
    }
    
    public function setInflector(Zend_Tool_Endpoint_Inflector_Interface $inflector)
    {
        $this->_inflector = $inflector;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Endpoint_Inflector_Interface
     */
    public function getInflector()
    {
        return $this->_inflector;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Endpoint_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Endpoint_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
    /**
     * This method should be called in order to "handle" a Tooling Client
     * request that has come to the endpoint that has been implemented.
     */
    final public function handle()
    {
        $manifest = $this->getManifest();
        
        $this->_preHandle();
        
        if ($this->_request->getActionName() == null) {
            throw new Zend_Tool_Endpoint_Exception('Endpoint failed to setup the action name.');
        }

        if ($this->_request->getProviderName() == null) {
            throw new Zend_Tool_Endpoint_Exception('Endpoint failed to setup the provider name.');
        }
        
        ob_start();
        
        try {
            $provider = $manifest->getProvider($this->_request->getProviderName());
            $provider->setRequest($this->_request)
                ->setResponse($this->_response)
                ->execute($this->_request->getActionName(), $this->_request->getProviderSpecialty());
                
        } catch (Exception $e) {
            //@todo implement some sanity here
            $this->_response->setContent($e->getMessage());
            return;
        }
        
        if (($content = ob_get_clean()) != '') {
            $this->_response->setContent($content);
        }
        
        $this->_postHandle();
    }
    
    protected function _handleDispatch()
    {
        
    }

}