<?php

class Zend_Tool_Framework_Registry //extends ArrayObject 
{

    /**
     * Registry object provides storage for shared objects.
     * @var Zend_Registry
     */
    protected static $_instance = null;

    /**
     * @var Zend_Tool_Framework_Loader_Abstract
     */
    protected $_loader = null;
    
    /**
     * @var Zend_Tool_Framework_Client_Abstract
     */
    protected $_client = null;
    
    /**
     * @var Zend_Tool_Framework_Action_Repository
     */
    protected $_actionRepository = null;
    
    /**
     * @var Zend_Tool_Framework_Provider_Repository
     */
    protected $_providerRepository = null;
    
    /**
     * @var Zend_Tool_Framework_Manifest_Repository
     */
    protected $_manifestRepository = null;
    
    /**
     * @var Zend_Tool_Framework_Client_Request
     */
    protected $_request = null;
    
    /**
     * @var Zend_Tool_Framework_Client_Response
     */
    protected $_response = null;
    
    /**
     * Retrieves the default registry instance.
     *
     * @return Zend_Tool_Framework_Registry
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    private function __construct()
    {
        // no instantiation from outside 
    }
    
    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Client_Abstract $client
     * @return Zend_Tool_Framework_Registry
     */
    public function setClient(Zend_Tool_Framework_Client_Abstract $client)
    {
        $this->_client = $client;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Framework_Client_Abstract
     */
    public function getClient()
    {
        return $this->_client;
    }
    
    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Loader_Abstract $loader
     * @return Zend_Tool_Framework_Registry
     */
    public function setLoader(Zend_Tool_Framework_Loader_Abstract $loader)
    {
        $this->_loader = $loader;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Framework_Loader_Abstract
     */
    public function getLoader()
    {
        return $this->_loader;
    }
    
    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Action_Repository $actionRepository
     * @return Zend_Tool_Framework_Registry
     */
    public function setActionRepository(Zend_Tool_Framework_Action_Repository $actionRepository)
    {
        $this->_actionRepository = $actionRepository;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Framework_Action_Repository
     */
    public function getActionRepository()
    {
        return $this->_actionRepository;
    }
    
    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Provider_Repository $providerRepository
     * @return Zend_Tool_Framework_Registry
     */
    public function setProviderRepository(Zend_Tool_Framework_Provider_Repository $providerRepository)
    {
        $this->_providerRepository = $providerRepository;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Framework_Provider_Repository
     */
    public function getProviderRepository()
    {
        return $this->_providerRepository;
    }
    
    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Manifest_Repository $manifestRepository
     * @return Zend_Tool_Framework_Registry
     */
    public function setManifestRepository(Zend_Tool_Framework_Manifest_Repository $manifestRepository)
    {
        $this->_manifestRepository = $manifestRepository;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Framework_Manifest_Repository
     */
    public function getManifestRepository()
    {
        return $this->_manifestRepository;
    }
    
    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Client_Request $request
     * @return Zend_Tool_Framework_Registry
     */
    public function setRequest(Zend_Tool_Framework_Client_Request $request)
    {
        $this->_request = $request;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Framework_Client_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Client_Response $response
     * @return Zend_Tool_Framework_Registry
     */
    public function setResponse(Zend_Tool_Framework_Client_Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, 'get' . $name)) {
            return $this->{'get' . $name}();
        } elseif (property_exists($this, '_' . $name)) {
            return $this->{'_' . $name};
        } else {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Property ' . $name . ' was not located in this registry.');
        }
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            $this->{'set' . $name}($value);
            return;
        } elseif (property_exists($this, '_' . $name)) {
            $this->{'_' . $name} = $value;
            return;
        } else {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Property ' . $name . ' was not located in this registry.');            
        }
    }
    
}
