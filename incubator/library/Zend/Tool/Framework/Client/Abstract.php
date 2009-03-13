<?php

require_once 'Zend/Tool/Framework/Registry.php';

abstract class Zend_Tool_Framework_Client_Abstract
{

    /**
     * @var Zend_Tool_Framework_Registry
     */
    protected $_clientRegistry = null;
    
    protected $_interactiveCallback = null;

    final public function __construct()
    {
        $this->_clientRegistry = Zend_Tool_Framework_Registry::getInstance();
        
        $this->_init();
        
        $this->_clientRegistry->setClient($this);
        
        // setup the loader
        if ($this->_clientRegistry->getLoader() == null) {
            require_once 'Zend/Tool/Framework/Loader/IncludePathLoader.php';
            $this->_clientRegistry->setLoader(new Zend_Tool_Framework_Loader_IncludePathLoader());                
        }

        // setup the action repository
        if ($this->_clientRegistry->getActionRepository() == null) {
            require_once 'Zend/Tool/Framework/Action/Repository.php';
            $this->_clientRegistry->setActionRepository(new Zend_Tool_Framework_Action_Repository());
        }
        
        // setup the provider repository
        if ($this->_clientRegistry->getProviderRepository() == null) {
            require_once 'Zend/Tool/Framework/Provider/Repository.php';
            $this->_clientRegistry->setProviderRepository(new Zend_Tool_Framework_Provider_Repository());
        }
        
        // setup the manifest repository
        if ($this->_clientRegistry->getManifestRepository() == null) {
            require_once 'Zend/Tool/Framework/Manifest/Repository.php';
            $this->_clientRegistry->setManifestRepository(new Zend_Tool_Framework_Manifest_Repository());
        }
        
        // setup the request object
        if ($this->_clientRegistry->getRequest() == null) {
            require_once 'Zend/Tool/Framework/Request.php';
            $this->_clientRegistry->setRequest(new Zend_Tool_Framework_Client_Request());
        }

        // setup the request object
        if ($this->_clientRegistry->getResponse() == null) {
            require_once 'Zend/Tool/Framework/Response.php';
            $this->_clientRegistry->setResponse(new Zend_Tool_Framework_Client_Response());
        }

        // let the loader load, then the repositories process whats been loaded
        $this->_clientRegistry->getLoader()->load();
        $this->_clientRegistry->getActionRepository()->process();
        $this->_clientRegistry->getProviderRepository()->process();
        $this->_clientRegistry->getManifestRepository()->process();
        
        if ($this instanceof Zend_Tool_Framework_Client_Interactive_InputInterface) {
            require_once 'Zend/Tool/Framework/Client/Interactive/InputHandler.php';
        }
        
        if ($this instanceof Zend_Tool_Framework_Client_Interactive_OutputInterface) {
            $this->_clientRegistry->getResponse()->setContentCallback(array($this, 'handleInteractiveOutput'));
        }
        
    }


    /**
     * This method should be implemented by the client implementation to
     * construct and set custom inflectors, request and response objects.
     */
    protected function _init()
    {
    }

    /**
     * This method should be implemented by the client implementation to
     * parse out and setup the request objects action, provider and parameter
     * information.
     */
    protected function _preDispatch()
    {
    }

    /**
     * This method should be implemented by the client implementation to
     * take the output of the response object and return it (in an client
     * specific way) back to the Tooling Client.
     */
    protected function _postDispatch()
    {
    }
    
    final public function hasInteractiveInput()
    {
        return ($this instanceof Zend_Tool_Framework_Client_Interactive_InputInterface);
    }
    
    final public function promptInteractiveInput($inputRequest)
    {
        if (!$this->hasInteractiveInput()) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('promptInteractive() cannot be called on a non-interactive client.');
        }
        
        $inputHandler = new Zend_Tool_Framework_Client_Interactive_InputHandler();
        $inputHandler->setClient($this);
        $inputHandler->setInputRequest($inputRequest);
        return $inputHandler->handle();

    }
    
    /**
     * This method should be called in order to "handle" a Tooling Client
     * request that has come to the client that has been implemented.
     */
    final public function dispatch()
    {

        try {

            $this->_preDispatch();

            if ($this->_clientRegistry->getRequest()->isDispatchable()) {

                if ($this->_clientRegistry->getRequest()->getActionName() == null) {
                    require_once 'Zend/Tool/Framework/Client/Exception.php';
                    throw new Zend_Tool_Framework_Client_Exception('Endpoint failed to setup the action name.');
                }

                if ($this->_clientRegistry->getRequest()->getProviderName() == null) {
                    require_once 'Zend/Tool/Framework/Client/Exception.php';
                    throw new Zend_Tool_Framework_Client_Exception('Endpoint failed to setup the provider name.');
                }

                $this->_handleDispatch();

            }

        } catch (Exception $exception) {
            $this->_clientRegistry->getResponse()->setException($exception);
        }

        $this->_postDispatch();
    }
    
    public function convertToClientNaming($string)
    {
        return $string;
    }
    
    public function convertFromClientNaming($string)
    {
        return $string;
    }
    
    final protected function _handleDispatch()
    {
        // get the provider repository
        $providerRepository = $this->_clientRegistry->getProviderRepository();
        
        $request = $this->_clientRegistry->getRequest();
        
        // get the dispatchable provider signature
        $providerSignature = $providerRepository->getProviderSignature($request->getProviderName());
        
        // get the actual provider
        $provider = $providerSignature->getProvider();

        // ensure that we can pretend if this is a pretend request
        if ($request->isPretend() && (!$provider instanceof Zend_Tool_Project_Provider_Pretendable)) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Dispatcher error - provider does not support pretend');  
        }
        
        // get the action name
        $actionName = $this->_clientRegistry->getRequest()->getActionName();

        if (!$actionableMethod = $providerSignature->getActionableMethod($actionName)) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Dispatcher error - actionable method not found');         
        }
        
        // get the actual method and param information
        $methodName       = $actionableMethod['methodName'];
        $methodParameters = $actionableMethod['parameterInfo'];

        // get the provider params
        $requestParameters = $this->_clientRegistry->getRequest()->getProviderParameters();
        
        // @todo This seems hackish, determine if there is a better way
        $callParameters = array();
        foreach ($methodParameters as $methodParameterName => $methodParameterValue) {
            $callParameters[] = (array_key_exists($methodParameterName, $requestParameters)) ? $requestParameters[$methodParameterName] : $methodParameterValue['default'];
        }
        
        if (($specialtyName = $this->_clientRegistry->getRequest()->getSpecialtyName()) != '_Global') {
            $methodName .= $specialtyName;
        }
        
        if (method_exists($provider, $methodName)) {
            call_user_func_array(array($provider, $methodName), $callParameters);
        } elseif (method_exists($provider, $methodName . 'Action')) {
            call_user_func_array(array($provider, $methodName . 'Action'), $callParameters);
        } else {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Not a supported method.');
        }
    }

}
