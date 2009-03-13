<?php

require_once 'Zend/Tool/Project/Profile/FileParser/Interface.php';
require_once 'Zend/Tool/Project/Context/Repository.php';
require_once 'Zend/Tool/Project/Profile.php';
require_once 'Zend/Tool/Project/Profile/Resource.php';

class Zend_Tool_Project_Profile_FileParser_Xml implements Zend_Tool_Project_Profile_FileParser_Interface
{
    
    /**
     * @var unknown_type
     */
    protected $_systemResourceReferences = array();
    
    /**
     * @var Zend_Tool_Project_Profile
     */
    protected $_profile = null;
    
    /**
     * @var Zend_Tool_Project_Context_Repository
     */
    protected $_contextRepository = null;

    
    public function __construct()
    {
        $this->_contextRepository = Zend_Tool_Project_Context_Repository::getInstance();
    }
    
    public function serialize(Zend_Tool_Project_Profile $profile)
    {

        $profile = clone $profile;
        
        $this->_profile = $profile;
        $xmlElement = new SimpleXMLElement('<projectProfile />');

        self::_serializeRecurser($profile, $xmlElement);            
        
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
    public function unserialize($data, Zend_Tool_Project_Profile $profile)
    {
        if ($data == null) {
            throw new Exception('contents not available to unserialize.');
        }

        $this->_profile = $profile;
        
        $xmlDataIterator = new SimpleXMLIterator($data);

        if ($xmlDataIterator->getName() != 'projectProfile') {
            throw new Exception('Profiles must start with a projectProfile node');
        }

        
        $this->_unserializeRecurser($xmlDataIterator);
        
        $this->_lazyLoadContexts();
        
        return $this->_profile;
        
    }
        
    protected function _serializeRecurser($resources, SimpleXmlElement $xmlNode)
    {
        // @todo find a better way to handle concurrency.. if no clone, _position in node gets messed up
        //if ($resources instanceof Zend_Tool_Project_Profile_Resource) {
        //    $resources = clone $resources;
        //}
        
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
            
            foreach ($resource->getPersistentAttributes() as $paramName => $paramValue) {
                $newNode->addAttribute($paramName, $paramValue);
            }

            if ($resource->hasChildren()) {
                self::_serializeRecurser($resource, $newNode);
            }
            
        }

    }
    
    
    
    protected function _unserializeRecurser(SimpleXMLIterator $xmlIterator, Zend_Tool_Project_Profile_Resource $resource = null)
    {
        
        foreach ($xmlIterator as $resourceName => $resourceData) {
            
            $contextName = $resourceName;
            $subResource = new Zend_Tool_Project_Profile_Resource($contextName);
            $subResource->setProfile($this->_profile);

            if ($resourceAttributes = $resourceData->attributes()) {
                $attributes = array();
                foreach ($resourceAttributes as $attrName => $attrValue) {
                    $attributes[$attrName] = (string) $attrValue;
                }
                $subResource->setAttributes($attributes);
            }
            
            if ($resource) {
                $resource->append($subResource, false);
            } else {
                $this->_profile->append($subResource);
            }

            if ($this->_contextRepository->isOverwritableContext($contextName) == false) {
                $subResource->initializeContext();
            }
            
            if ($xmlIterator->hasChildren()) {
                self::_unserializeRecurser($xmlIterator->getChildren(), $subResource);
            }
        }
    }
    
    protected function _lazyLoadContexts()
    {
        
        foreach ($this->_profile as $topResource) {
            $rii = new RecursiveIteratorIterator($topResource, RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $resource) {
                $resource->initializeContext();
            }
                
        }
        
    }
    
}