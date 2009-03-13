<?php

require_once 'Zend/Tool/Framework/Provider/Signature.php';

class Zend_Tool_Framework_Provider_Repository implements IteratorAggregate
{
    /**
     * @var bool
     */
    protected $_processOnAdd = false;
    
    /**
     * @var Zend_Tool_Framework_Provider_Interface[]
     */
    protected $_unprocessedProviders = array();
    
    /**
     * @var Zend_Tool_Framework_Provider_Signature[]
     */
    protected $_providerSignatures = array();

    /**
     * Set the ProcessOnAdd flag
     *
     * @param unknown_type $processOnAdd
     * @return unknown
     */
    public function setProcessOnAdd($processOnAdd = true)
    {
        $this->_processOnAdd = (bool) $processOnAdd;
        return $this;
    }
    
    /**
     * Add a provider to the repository for processing
     *
     * @param Zend_Tool_Framework_Provider_Interface $provider
     * @return Zend_Tool_Framework_Provider_Repository
     */
    public function addProvider(Zend_Tool_Framework_Provider_Interface $provider)
    {
        $this->_unprocessedProviders[] = $provider;
        
        // if process has already been called
        if ($this->_processOnAdd) {
            $this->process();
        }
        
        return $this;
    }

    /**
     * Process all of the unprocessed providers
     *
     */
    public function process()
    {
        // the following while loop allows this repository to continually process 
        // new providers if they are added
        while ($provider = array_shift($this->_unprocessedProviders)) {
            $providerSignature = new Zend_Tool_Framework_Provider_Signature($provider);
            $this->_providerSignatures[$providerSignature->getName()] = $providerSignature;
        }
    }

    public function getProviders()
    {
        $providers = array();
        foreach ($this->_providerSignatures as $providerSignature) {
            $providers[] = $providerSignature->getProvider();
        }
        return $providers;
    }

    public function getProviderSignatures()
    {
        return $this->_providerSignatures;
    }

    /**
     * Enter description here...
     *
     * @param string $providerName
     * @return Zend_Tool_Framework_Provider_Signature
     */
    public function getProviderSignature($providerName)
    {
        return $this->_providerSignatures[$providerName];
    }

    public function getProvider($providerName)
    {
        return $this->_providerSignatures[$providerName]->getProvider();
    }

    public function getIterator()
    {
        return $this->getProviders();
    }

}
