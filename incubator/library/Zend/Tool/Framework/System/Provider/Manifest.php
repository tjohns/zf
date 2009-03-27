<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';

class Zend_Tool_Framework_System_Provider_Manifest implements Zend_Tool_Framework_Provider_Interface
{
    
    public function getName()
    {
        return 'Manifest';
    }
    
    public function show()
    {
        $clientRegistry = Zend_Tool_Framework_Registry::getInstance();
        
        $manifestRepository = $clientRegistry->getManifestRepository();
        $response           = $clientRegistry->getResponse();
        
        $metadataTree = array();
        
        $longestAttrNameLen = 50;
        
        foreach ($manifestRepository as $metadata) {
            
            $metadataType  = $metadata->getType();
            $metadataName  = $metadata->getName();
            $metadataAttrs = $metadata->getAttributes('attributesParent');

            if (!$metadataAttrs) {
                $metadataAttrs = '(None)';
            } else {
                $metadataAttrs = urldecode(http_build_query($metadataAttrs, null, ', ')); 
            }
            
            if (!array_key_exists($metadataType, $metadataTree)) {
                $metadataTree[$metadataType] = array();
            }
            
            if (!array_key_exists($metadataName, $metadataTree[$metadataType])) {
                $metadataTree[$metadataType][$metadataName] = array();
            }
            
            if (!array_key_exists($metadataAttrs, $metadataTree[$metadataType][$metadataName])) {
                $metadataTree[$metadataType][$metadataName][$metadataAttrs] = array();
            }
            
            $longestAttrNameLen = (strlen($metadataAttrs) > $longestAttrNameLen) ? strlen($metadataAttrs) : $longestAttrNameLen;
            
            $metadataValue = $metadata->getValue();
            if (is_array($metadataValue) && count($metadataValue) > 0) {
                $metadataValue = urldecode(http_build_query($metadataValue, null, ', '));
            } elseif (is_array($metadataValue)) {
                $metadataValue = '(empty array)';
            }
            
            $metadataTree[$metadataType][$metadataName][$metadataAttrs][] = $metadataValue;
        }
        
        foreach ($metadataTree as $metadataType => $metadatasByName) {
            $response->appendContent($metadataType);
            foreach ($metadatasByName as $metadataName => $metadatasByAttributes) {
                $response->appendContent("   " . $metadataName);
                foreach ($metadatasByAttributes as $metadataAttributeName => $metadataValues) {
                    foreach ($metadataValues as $metadataValue) {
                        $response->appendContent(
                            sprintf("      %-{$longestAttrNameLen}.{$longestAttrNameLen}s : ", $metadataAttributeName) 
                            . $metadataValue
                            );
                    }
                }
            }
        }
        
    }
}
