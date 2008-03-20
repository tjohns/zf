<?php

require_once 'Zend/Build/Resource/Abstract.php';

class Zend_Build_Resource_Project extends Zend_Build_Resource_Abstract
{
    
    protected $_projectStructure = null;
    
    /**
     * @var Zend_Build_Manifest
     */
    protected $_buildManifest = null;
    
    public function __construct()
    {
        $this->_buildManifest = Zend_Build_Manifest::getInstance();
    }
    
    public function validate()
    {
        if (isset($this->_parameters['projectProfile'])) {
            
            // check to see if the projectProfile that exists is sane
            
        } else {
            //$this->_projectStructure = new SimpleXMLElement($this->_getDefaultProjectStructure());
        }
        
        if (!isset($this->_parameters['directory'])) {
            $this->_parameters['directory'] = './';
        }
        
        
        if (!realpath($this->_parameters['directory'])) {
            if (mkdir($this->_parameters['directory']) === false) {
                throw new Zend_Tool_Cli_Exception('Could not create directory ' . $this->_parameters['directory']);
            }
        }
        
        $this->_parameters['directory'] = realpath($this->_parameters['directory']);
        
    }
    
    public function create()
    {
        $actionName = 'create';
        $basePath = $this->_parameters['directory'];
        $pathArray = array();
        
        $projectFile = new Zend_Build_Resource_ProjectFile();
        
        $lastDepth = 0;
        $profileIterator = new RecursiveIteratorIterator($projectFile->getProfile(), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($profileIterator as $name => $item) {
            
            $currentDepth = $profileIterator->getDepth();
            
            if ($currentDepth <= $lastDepth) {
                array_pop($pathArray);
            }
            
            if ($currentDepth < $lastDepth) {
                for ($x = 0; $x < ($lastDepth - $currentDepth); $x++) {
                    array_pop($pathArray);
                }
            }
            
            $fullPath = $basePath . '/';
            if ($pathArray) {
                $fullPath .= implode('/', $pathArray);
            }
            
            $resource = $this->_buildManifest->getContext('resource', $name);

            if ($resource === null) {
                throw new Zend_Build_Exception('Context not available.');
            }
            
            $className = $resource->getClassName();
            
            Zend_Loader::loadClass($className);
            $resourceObject = new $className();

            if (!$resourceObject instanceof Zend_Build_Resource_Filesystem) {
                throw new Zend_Build_Exception('Projects can only deal with file and directory resources.');
            }
            
            $resourceObject->setParameter('basePath', $fullPath);
            
            if (count($attrs = $item->attributes()) > 0) {
                
                foreach ($attrs as $attrName => $attrValue) {
                    if ($attrName != 'enabled') {
                        $resourceObject->setParameter($attrName, $attrValue);
                    }
                }
            }
            
            $resourceObject->validate();
            
            if (!isset($item->enabled) || (isset($item->enabled) && ($item->enabled != false))) {
                $resourceObject->execute($actionName);
            }
            
            $dirRemainder = preg_replace('#^' . preg_quote($fullPath, '#') . '#', '', $resourceObject->getDirname());
            
            if ($dirRemainder) {
                array_push($pathArray, trim($dirRemainder, '/'));
            }
            
            $lastDepth = $profileIterator->getDepth();
        }
    }
    
}

