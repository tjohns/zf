<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

class Zend_Entity_MetadataFactory_Code implements Zend_Entity_MetadataFactory_Interface
{
    const INVALID_ENTITY_NAME_PATTERN = '/([^_a-zA-Z0-9\\\]+)/';

    protected $_path = null;

    protected $_maps = array();

    protected $_entityNames = null;

    public function __construct($path)
    {
        $this->_path = $path;
    }

    /**
     * Retrieve an array of all definitions by name.
     *
     * @return array
     */
    public function getDefinitionEntityNames()
    {
        if($this->_entityNames == null) {
            $this->_entityNames = array();
            foreach(scandir($this->_path) AS $item) {
                if( ($pos = strpos($item, ".php")) !== false) {
                    $this->_entityNames[] = substr($item, 0, $pos);
                }
            }
        }
        return $this->_entityNames;
    }

    /**
     * Return Entity Definition
     * 
     * @param  string $entityName
     * @return Zend_Entity_Mapper_Definition_Entity
     */
    public function getDefinitionByEntityName($entityName)
    {
        if(!isset($this->_maps[$entityName])) {
            $this->assertEntityNameValid($entityName);
            $path = $this->getDefinitionPath($entityName);
            $definition = $this->loadDefinitionFile($path, $entityName);
            $this->_maps[$entityName] = $definition;
            
            $definition->compile($this);
        }
        return $this->_maps[$entityName];
    }

    protected function assertEntityNameValid($entityName)
    {
        if(preg_match(self::INVALID_ENTITY_NAME_PATTERN, $entityName, $matches)) {
            throw new Zend_Entity_Exception("Trying to load invalid entity name '".$entityName."'. Only ".self::INVALID_ENTITY_NAME_PATTERN." are allowed.");
        }
    }

    protected function getDefinitionPath($entityName)
    {
        $path = str_replace(
            DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR, 
            $this->_path . DIRECTORY_SEPARATOR . $entityName . ".php"
        );
        $this->assertPathExists($path, $entityName);
        return $path;
    }

    protected function assertPathExists($path, $entityName)
    {
        if(!file_exists($path)) {
            throw new Zend_Entity_Exception("Definition file '".$path."' for entity '".$entityName."' does not exist!");
        }
    }

    protected function loadDefinitionFile($path, $entityName)
    {
        $definition = require($path);
        if( !($definition instanceof Zend_Entity_Mapper_Definition_Entity) ) {
            throw new Zend_Entity_Exception("Definition file of entity ".$entityName." does not return a entity definition.");
        }
        return $definition;
    }
}