<?php

class ZendL_Tool_Rpc_Manifest_Registry //implements IteratorAggregate 
{

    
    protected static $_instance = null;
    
    protected $_manifests = array();
    
    protected $_metadatas = array();
    
    /**
     * Enter description here...
     *
     * @return ZendL_Tool_Rpc_Manifest_Registry
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    protected function __construct()
    {
    }
    
    public function addManifest(ZendL_Tool_Rpc_Manifest_Interface $manifest)
    {
        $index = count($this->_manifests);
        
        if (method_exists($manifest, 'getIndex')) {
            $index = $manifest->getIndex();
        }
        
        $providerRegistry = ZendL_Tool_Rpc_Provider_Registry::getInstance();
        
        // load providers if interface supports that method
        if (method_exists($manifest, 'getProviders')) {
            $providers = $manifest->getProviders();
            if (!is_array($providers)) {
                $providers = array($providers);
            }
            
            foreach ($providers as $provider) {
                $providerRegistry->addProvider($provider);
            }
            
        }
        
        // load actions if interface supports that method
        if (method_exists($manifest, 'getActions')) {
            $actions = $manifest->getActions();
            if (!is_array($actions)) {
                $actions = array($actions);
            }
            
            foreach ($actions as $action) {
                $providerRegistry->addAction($action);
            }
        }
        
        $this->_manifests[$index] = $manifest;
    }
    
    public function process()
    {
        ksort($this->_manifests);
        
        foreach ($this->_manifests as $manifest) {
            if (method_exists($manifest, 'getMetadata')) {
                $metadatas = $manifest->getMetadata();
                if (!is_array($metadatas)) {
                    $metadatas = array($metadatas);
                }
                
                foreach ($metadatas as $metadata) {
                    if (!$metadata instanceof ZendL_Tool_Rpc_Manifest_Metadata) {
                        throw new ZendL_Tool_Rpc_Manifest_Exception('A ZendL_Tool_Rpc_Manifest_Metadata object was not found in manifest ' . get_class($manifest));
                    }
                    
                    $this->_addMetadata($metadata);
                }
                
            }
        }
        
    }
    
    public function getMetadatas(Array $searchProperties = array(), $includeNonExistentProperties = true)
    {
        
        $returnMetadatas = array();
        
        foreach ($this->_metadatas as $metadata) {
            
            foreach ($searchProperties as $searchPropertyName => $searchPropertyValue) {
                if (method_exists($metadata, 'get' . $searchPropertyName)) {
                    if ($metadata->{'get' . $searchPropertyName}() != $searchPropertyValue) {
                        continue 2;
                    }
                } elseif (!$includeNonExistentProperties) {
                    continue 2;
                }
            }
            
            $returnMetadatas[] = $metadata;
            
        }
        
        return $returnMetadatas;
    }
    
    public function getMetadata(Array $searchProperties = array(), $includeNonExistentProperties = true)
    {
        $metadatas = $this->getMetadatas($searchProperties, $includeNonExistentProperties);
        
        return array_shift($metadatas);
    }
    
    protected function _addMetadata(ZendL_Tool_Rpc_Manifest_Metadata $metadata)
    {
        $this->_metadatas[] = $metadata;
        return $this;
    }
    
}
