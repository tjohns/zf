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

    protected $_maps = null;

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
        $this->loadDefinitions();
        
        return $this->_entityNames;
    }

    protected function loadDefinitions()
    {
        if($this->_maps == null) {
            if(!is_dir($this->_path)) {
                throw new Zend_Entity_InvalidEntityException(
                    "Could not load Definitions because '".$this->_path."' is not a directory."
                );
            }

            $this->_maps = array();
            foreach(scandir($this->_path) AS $item) {
                if( ($pos = strpos($item, ".php")) !== false) {
                    $entityName = substr($item, 0, $pos);
                    $path = $this->getDefinitionPath($entityName);
                    $definition = $this->loadDefinitionFile($path, $entityName);

                    $entityName = $definition->getEntityName();
                    $this->_maps[$entityName] = $definition;
                    $this->_entityNames[] = $entityName;
                }
            }

            foreach($this->_maps AS $definition) {
                $definition->compile($this);
            }
        }
    }

    /**
     * Return Entity Definition
     *
     * @throws Zend_Entity_InvalidEntityException
     * @param  string $entityName
     * @return Zend_Entity_Definition_Entity
     */
    public function getDefinitionByEntityName($entityName)
    {
        $this->loadDefinitions();
        if(!isset($this->_maps[$entityName])) {
            throw new Zend_Entity_InvalidEntityException("The entity '".$entityName."' is unknown.");
        }
        return $this->_maps[$entityName];
    }

    protected function getDefinitionPath($entityName)
    {
        $path = str_replace(
            DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR, 
            $this->_path . DIRECTORY_SEPARATOR . $entityName . ".php"
        );
        return $path;
    }

    protected function loadDefinitionFile($path, $entityName)
    {
        $definition = require($path);
        if( !($definition instanceof Zend_Entity_Definition_Entity) ) {
            throw new Zend_Entity_InvalidEntityException("Definition file of entity '".$entityName."' does not return a entity definition.");
        }
        return $definition;
    }
}