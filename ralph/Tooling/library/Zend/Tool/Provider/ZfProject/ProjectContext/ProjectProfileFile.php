<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ProjectProfileFile extends Zend_Tool_Provider_ZfProject_ProjectContext_File 
{
    
    protected $_name = '.zfproject.xml';
    
    protected static $_contexts = null;

    public static function fromXml($projectStructureXml, $path = null)
    {
        
        $projStructXmlIterator = new SimpleXMLIterator($projectStructureXml);

        if ($path == null) {
            if (isset($projStructXmlIterator['path'])) {
                $path = $projStructXmlIterator['path'];
            } else {
                throw new Exception('Path information was not supplied for this project.');
            }
        }
        
        if ($projStructXmlIterator->getName() != 'projectDirectory') {
            throw new Exception('Profiles must start with a projectDirectory node');
        }
        
        $contextClass = self::_getContextByName('projectDirectory');
        $projectStructureTree = new $contextClass();
        $projectStructureTree->setBaseDirectoryName($path);
        
        self::_fromXmlRecurse($projectStructureTree, $projStructXmlIterator);
        
        return $projectStructureTree; 
    }
    
    protected static function _fromXmlRecurse(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $parentStructureNode, $projectStructureXmlIterator)
    {
        foreach ($projectStructureXmlIterator as $childNodeName => $childNode) {
            $contextClass = self::_getContextByName($childNodeName);
            $childStructureNode = new $contextClass();
            if ($attributes = $childNode->attributes()) {
                foreach ($attributes as $attrName => $attrValue) {
                    $clnAttrs[$attrName] = (string) $attrValue;
                }
                $childStructureNode->setParameters($clnAttrs);
            }
            $childStructureNode->setBaseDirectoryName($parentStructureNode->getFullPath());
            $parentStructureNode->append($childStructureNode);

            if ($projectStructureXmlIterator->hasChildren()) {
                self::_fromXmlRecurse($childStructureNode, $projectStructureXmlIterator->getChildren());
            }
        }
    }

    protected static function _getContextByName($name)
    {
        if (self::$_contexts === null) {
            self::_loadContexts();
        }
        
        if (isset(self::$_contexts[$name])) {
            return self::$_contexts[$name];
        }
        
        die('couldnt find ' . $name);
        throw new Exception('Context by name ' . $name . ' not found');        
    }
    
    
    protected static function _getContextByClass($className)
    {
        
    }
    
    protected static function _loadContexts()
    {
        $pluginLoader = new Zend_Loader_PluginLoader(array(
            'Zend_Tool_Provider_ZfProject_ProjectContext' => dirname(__FILE__)
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
    
    
    
    
    public function getContextName()
    {
        return 'projectProfileFile';
    }
    
}