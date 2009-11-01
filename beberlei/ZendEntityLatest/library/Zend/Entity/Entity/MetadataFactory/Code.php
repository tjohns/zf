<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage MetadataFactory
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * A programmatic metadata factory approach using a file for each entity.
 *
 * @uses       Zend_Entity_MetadataFactory_FactoryAbstract
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage MetadataFactory
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_MetadataFactory_Code extends Zend_Entity_MetadataFactory_FactoryAbstract
{
    const INVALID_ENTITY_NAME_PATTERN = '/([^_a-zA-Z0-9\\\]+)/';

    /**
     * @var string
     */
    protected $_path = null;

    /**
     * @var array
     */
    protected $_maps = null;

    /**
     * @var string
     */
    protected $_entityNames = null;

    /**
     * @var array
     */
    protected $_fileModDates = array();

    /**
     * @param string $path
     */
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
            $this->_entityNames = array();
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

    /**
     * @return string
     */
    public function getCurrentVersionHash()
    {
        ksort($this->_fileModDates);
        $hash = "";
        foreach($this->_fileModDates AS $date) {
            $hash = md5($hash."-".$date);
        }
        return $hash;
    }

    /**
     * @param  string $entityName
     * @return string
     */
    protected function getDefinitionPath($entityName)
    {
        $path = str_replace(
            DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR, 
            $this->_path . DIRECTORY_SEPARATOR . $entityName . ".php"
        );
        return $path;
    }

    /**
     * @throws Zend_Entity_InvalidEntityException
     * @param  string $path
     * @param  string $entityName
     * @return Zend_Entity_Definition_Entity
     */
    protected function loadDefinitionFile($path, $entityName)
    {
        $definition = require($path);
        $this->_fileModDates[$entityName] = filectime($path)."-".filemtime($path);
        if( !($definition instanceof Zend_Entity_Definition_Entity) ) {
            throw new Zend_Entity_InvalidEntityException("Definition file of entity '".$entityName."' does not return a entity definition.");
        }
        return $definition;
    }
}