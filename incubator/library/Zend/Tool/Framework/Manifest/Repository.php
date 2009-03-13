<?php

class Zend_Tool_Framework_Manifest_Repository implements IteratorAggregate
{

    
    protected $_manifests = array();

    /**
     * @var Zend_Tool_Framework_Manifest_Metadata[]
     */
    protected $_metadatas = array();

    /**
     * Enter description here...
     *
     * @param Zend_Tool_Framework_Manifest_Interface $manifest
     * @return Zend_Tool_Framework_Manifest_Repository
     */
    public function addManifest(Zend_Tool_Framework_Manifest_Interface $manifest)
    {
        $index = count($this->_manifests);

        if (method_exists($manifest, 'getIndex')) {
            $index = $manifest->getIndex();
        }

        $clientRegistry = Zend_Tool_Framework_Registry::getInstance();
        $actionRepository   = $clientRegistry->getActionRepository();
        $providerRepository = $clientRegistry->getProviderRepository();
        

        // load providers if interface supports that method
        if (method_exists($manifest, 'getProviders')) {
            $providers = $manifest->getProviders();
            if (!is_array($providers)) {
                $providers = array($providers);
            }

            foreach ($providers as $provider) {
                $providerRepository->addProvider($provider);
            }

        }

        // load actions if interface supports that method
        if (method_exists($manifest, 'getActions')) {
            $actions = $manifest->getActions();
            if (!is_array($actions)) {
                $actions = array($actions);
            }

            foreach ($actions as $action) {
                $actionRepository->addAction($action);
            }
        }

        // @todo should we detect collisions?
        $this->_manifests[$index] = $manifest;
        
        return $this;
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
                    if (!$metadata instanceof Zend_Tool_Framework_Manifest_Metadata) {
                        require_once 'Zend/Tool/Framework/Manifest/Exception.php';
                        throw new Zend_Tool_Framework_Manifest_Exception(
                            'A Zend_Tool_Framework_Manifest_Metadata object was not found in manifest ' . get_class($manifest)
                        );
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

    protected function _addMetadata(Zend_Tool_Framework_Manifest_Metadata $metadata)
    {
        $this->_metadatas[] = $metadata;
        return $this;
    }

    public function __toString()
    {
        $metadatasByType = array();

        foreach ($this->_metadatas as $metadata) {
            if (!array_key_exists($metadata->getType(), $metadatasByType)) {
                $metadatasByType[$metadata->getType()] = array();
            }
            $metadatasByType[$metadata->getType()][] = $metadata;
        }

        $string = '';
        foreach ($metadatasByType as $type => $metadatas) {
            $string .= $type . PHP_EOL;
            foreach ($metadatas as $metadata) {
                $metadataString = $metadata->__toString();
                $metadataString = str_replace(PHP_EOL, PHP_EOL . '    ', $metadataString);
                $string .= $metadataString;
            }
        }

        return $string;
    }
    
    public function getIterator()
    {
        return new ArrayObject($this->_metadatas);
    }

}
