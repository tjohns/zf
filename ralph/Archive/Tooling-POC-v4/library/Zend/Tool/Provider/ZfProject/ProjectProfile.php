<?php

class Zend_Tool_Provider_ZfProject_ProjectProfile implements IteratorAggregate  
{

    /**
     * @var Zend_Tool_Provider_ZfProject_ProfileSet_ProfileSetAbstract
     */
    protected static $_profileSet = null;

    /**
     * @var Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract[]
     */
    protected static $_contexts = null;
    
    /**
     * @var Zend_Tool_Provider_ZfProject_ProjectContext_ProjectProfileFile
     */
    protected $_projectProfileFile = null;
    
    protected $_projectContexts = array();

    
    public static function getProfileSet()
    {
        if (self::$_profileSet == null) {
            self::$_profileSet = new Zend_Tool_Provider_ZfProject_ProfileSet_Default(); 
        }
        
        return self::$_profileSet;
    }
    
    public static function setProfileSet(Zend_Tool_Provider_ZfProject_ProfileSet_ProfileSetAbstract $profileSet)
    {
        self::$_profileSet = $profileSet;
    }
    
    
    public static function getContextByName($name)
    {
        if (self::$_contexts === null) {
            self::_loadContexts();
        }
        
        if (isset(self::$_contexts[$name])) {
            return clone self::$_contexts[$name];
        }
        
        //Zend_Debug::dump(self::$_contexts);
        
        die('couldnt find ' . $name);
    }
    
    
    /**
     * @todo public static function getContextByClass($className)
     */

    /**
     * _loadContexts() - statically find and load the context files
     *
     */
    protected static function _loadContexts()
    {
        $pluginLoader = new Zend_Loader_PluginLoader(array(
            'Zend_Tool_Provider_ZfProject_ProjectContext_' => dirname(__FILE__) . '/ProjectContext/'
            ));
        
        $classes = $pluginLoader->loadAll();
        
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->isInstantiable() && $reflectionClass->isSubclassOf('Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract')) {
                $context = $reflectionClass->newInstance();
                self::$_contexts[$context->getContextName()] = $context;
            }
        }
        
    }
    
    public function __construct($projectProfileData = null)
    {
        if ($projectProfileData == null) {
            $profileSet = self::getProfileSet();
            $projectProfileData = $profileSet->projectProfile();
        }
        
        $this->_unserializeXml($projectProfileData);
    }
    
    public function getProjectContexts()
    {
        return $this->_projectContexts;
    }
    
    public function findContext($searchContexts)
    {
        if (is_string($searchContexts)) {
            $searchContexts = array($searchContexts);
        }
        
        $orderedSearchContexts = array();
        $orderedSearchContextIndex = 0;
        
        foreach ($searchContexts as $searchContextName => $searchContextValue) {
            
            
            if (is_int($searchContextName)) {
                $orderedSearchContexts[$orderedSearchContextIndex]['contextName'] = $searchContextValue;
                $orderedSearchContexts[$orderedSearchContextIndex]['contextParams'] = array();
            } elseif (is_string($searchContextName) && is_array($searchContextValue)) {
                $orderedSearchContexts[$orderedSearchContextIndex]['contextName'] = $searchContextName;
                $orderedSearchContexts[$orderedSearchContextIndex]['contextParams'] = $searchContextValue;
            } else {
                throw new Exception('your search criteria doesnt make sense.');
            }
            
            $orderedSearchContextIndex++;
        }
        
        
        $foundContext = null;
        
        while ($currentSearchContext = array_shift($orderedSearchContexts)) {
            
            if (!$foundContext) {
                
                foreach ($this->_projectContexts as $projectContext) {
                    $foundContext = $this->_recursiveFindContext($projectContext, $currentSearchContext['contextName'], $currentSearchContext['contextParams']);
                    if ($foundContext) {
                        break;
                    }
                }
                
            } else {
                $foundContext = $this->_recursiveFindContext($foundContext, $currentSearchContext['contextName'], $currentSearchContext['contextParams']);
            }
            
        }
        
        return $foundContext;
    }
    
    protected function _recursiveFindContext(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $haystackProjectContexts, $searchContextName, $searchContextParams)
    {

        $subProjectContextIterator = new RecursiveIteratorIterator($haystackProjectContexts, RecursiveIteratorIterator::SELF_FIRST);
        $haystackProjectContexts->getContextName();
        foreach ($subProjectContextIterator as $subProjectContext) {
            if (strtolower($subProjectContext->getContextName()) == strtolower($searchContextName)) {
                
                if ($searchContextParams) {
                    $contextParams = $subProjectContext->getParameters();
                    
                    $foundKeysSearch = array_intersect_key($searchContextParams, $contextParams);
                    $foundKeysOrig   = array_intersect_key($contextParams, $searchContextParams);
                    
                    // a search key was missing in the contextParams
                    if ($foundKeysSearch !== $searchContextParams) {
                        continue;
                    }
                    
                    foreach ($searchContextParams as $searchContextParamName => $searchContextParamValue) {
                        if ($contextParams[$searchContextParamName] !== $searchContextParamValue) {
                            continue 2;
                        }
                    }
                    
                    return $subProjectContext;
                } else {
                    return $subProjectContext;                    
                }

            }
        }
        
        return false;
    }
    
    public function appendProjectContext(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $projectContext)
    {
        $this->_projectContexts[$projectContext->getContextName()] = $projectContext;
    }

    public function getIterator()
    {
        return $this->_projectContexts;
    }
    
    public function __isset($name)
    {
        return (array_key_exists($name, $this->_projectContexts));
    }
    
    public function __get($name)
    {
        return (isset($this->_projectContexts[$name]) ? $this->_projectContexts[$name] : null);
    }

    public function toString()
    {
        return $this->_serialize();
    }
    
    public function __toString()
    {
        return $this->toString();
    }
    
    public function create()
    {
        foreach ($this->_projectContexts as $projectContext) {
            $projectContext->create();
        }
    }

    protected function _unserializeXml($xmlProjectProfile)
    {
        if ($xmlProjectProfile == null) {
            throw new Exception('contents not available to unserialize.');
        }
        
        $projectProfileIterator = new SimpleXMLIterator($xmlProjectProfile);

        if ($projectProfileIterator->getName() != 'projectProfile') {
            throw new Exception('Profiles must start with a projectProfile node');
        }

        $this->_unserializeRecurser($projectProfileIterator);

    }
    
    protected function _unserializeRecurser(SimpleXMLIterator $projectProfileIterator, Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $context = null)
    {
        
        foreach ($projectProfileIterator as $projectProfileNodeName => $projectProfileNode) {
            $subContextClass = self::getContextByName($projectProfileNodeName);
            $subContext = new $subContextClass();
            
            if ($subContext->getContextName() === 'projectProfileFile') {
                $subContext->setProjectProfile($this);
            }
            
            if ($attributes = $projectProfileNode->attributes()) {
                foreach ($attributes as $attrName => $attrValue) {
                    $clnAttrs[$attrName] = (string) $attrValue;
                }
                $subContext->setParameters($clnAttrs);
            }
            
            if ($context) {
                $context->append($subContext);
            } else {
                $this->appendProjectContext($subContext);
            }

            if ($projectProfileIterator->hasChildren()) {
                self::_unserializeRecurser($projectProfileIterator->getChildren(), $subContext);
            }
        }
    }

    protected function _serialize()
    {
        
        $projectStructureXmlNode = new SimpleXMLElement('<projectProfile />');
        $projectStructureXmlNode->addAttribute('profileSet', get_class(self::getProfileSet()));

        self::_serializeRecurser($this->_projectContexts, $projectStructureXmlNode);            
        
        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;
        $domnode = dom_import_simplexml($projectStructureXmlNode);
        $domnode = $doc->importNode($domnode, true);
        $domnode = $doc->appendChild($domnode);

        return $doc->saveXML();
    }
    
    protected function _serializeRecurser($context, SimpleXmlElement $xmlNode)
    {
        foreach ($context as $subContext) {
            
            if ($subContext->isDeleted()) {
                continue;
            }
            
            $newNode = $xmlNode->addChild($subContext->getContextName());
            foreach ($subContext->getPersistentParameters() as $paramName => $paramValue) {
                $newNode->addAttribute($paramName, $paramValue);
            }
            
            if ($subContext->hasChildren()) {
                self::_serializeRecurser($subContext, $newNode);
            }
            
        }
    }

    
}