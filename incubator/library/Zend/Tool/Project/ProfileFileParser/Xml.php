<?php

require_once 'Zend/Tool/Project/ProfileFileParser/Interface.php';
require_once 'Zend/Tool/Project/Profile.php';
require_once 'Zend/Tool/Project/Resource.php';

class Zend_Tool_Project_ProfileFileParser_Xml implements Zend_Tool_Project_ProfileFileParser_Interface
{
    
    protected $_profile = null;
    
    public function serialize(Zend_Tool_Project_Profile $profile)
    {

        $profile = clone $profile;
        
        $this->_profile = $profile;
        $xmlElement = new SimpleXMLElement('<projectProfile />');

        self::_serializeRecurser($profile->getTopResources(), $xmlElement);            
        
        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;
        $domnode = dom_import_simplexml($xmlElement);
        $domnode = $doc->importNode($domnode, true);
        $domnode = $doc->appendChild($domnode);
        
        return $doc->saveXML();
    }
    
    /**
     * 
     * @return Zend_Tool_Project_Profile
     */
    public function unserialize($data)
    {
        if ($data == null) {
            throw new Exception('contents not available to unserialize.');
        }
        
        $xmlDataIterator = new SimpleXMLIterator($data);

        if ($xmlDataIterator->getName() != 'projectProfile') {
            throw new Exception('Profiles must start with a projectProfile node');
        }

        $this->_profile = new Zend_Tool_Project_Profile();
        
        $this->_unserializeRecurser($xmlDataIterator);
        
        return $this->_profile;
        
    }
        
    protected function _serializeRecurser($resources, SimpleXmlElement $xmlNode)
    {
        // @todo find a better way to handle concurrency.. if no clone, _position in node gets messed up
        if ($resources instanceof Zend_Tool_Project_Resource) {
            $resources = clone $resources;
        }
        
        foreach ($resources as $resource) {
            
            if ($resource->isDeleted()) {
                continue;
            }
            
            $resourceName = $resource->getContext()->getName();
            $resourceName[0] = strtolower($resourceName[0]);
            
            $newNode = $xmlNode->addChild($resourceName);

            $reflectionClass = new ReflectionClass($resource->getContext());

            if ($resource->isEnabled() == false) {
                $newNode->addAttribute('enabled', 'false');
            }
            
            foreach ($resource->getPersistentParameters() as $paramName => $paramValue) {
                $newNode->addAttribute($paramName, $paramValue);
            }

            if ($resource->hasChildren()) {
                self::_serializeRecurser($resource, $newNode);
            }
            
        }

    }
    
    
    
    protected function _unserializeRecurser(SimpleXMLIterator $xmlIterator, Zend_Tool_Project_Resource $resource = null)
    {
        
        foreach ($xmlIterator as $resourceName => $resourceData) {
            
            //$context = Zend_Tool_Project_Context_Registry::getInstance()->getContext($resourceName);
            $contextName = $resourceName;
            $subResource = new Zend_Tool_Project_Resource($contextName);

            /*
            if ($attributes = $resourceData->attributes()) {
                foreach ($attributes as $attrName => $attrValue) {
                    $method = 'set' . $attrName;
                    if (method_exists($subResource, $method)) {
                        $subResource->{$method}((string) $attrValue);
                    } elseif (method_exists($context, $method)) {
                        $context->{$method}((string) $attrValue);
                    }
                }
            }
            

            if (method_exists($context, 'setProfile')) {
                $context->setProfile($this->_profile);
            }
            */
            
            if ($resource) {
                $resource->append($subResource);
            } else {
                $this->_profile->append($subResource);
            }

            if ($xmlIterator->hasChildren()) {
                self::_unserializeRecurser($xmlIterator->getChildren(), $subResource);
            }
        }
    }
    
}