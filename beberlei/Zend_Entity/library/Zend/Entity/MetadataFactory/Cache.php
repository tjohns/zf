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

class Zend_Entity_MetadataFactory_Cache implements Zend_Entity_MetadataFactory_Interface
{
    /**
     * @var Zend_Entity_MetadataFactory_Interface
     */
    protected $_metadataFactory = null;

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    /**
     * @var string
     */
    protected $_cachePrefix = '';

    /**
     * @param Zend_Entity_MetadataFactory_Interface $metadataFactory
     * @param Zend_Cache_Core $cache
     */
    public function __construct(Zend_Entity_MetadataFactory_Interface $metadataFactory, Zend_Cache_Core $cache, $cachePrefix='')
    {
        $this->_metadataFactory = $metadataFactory;
        $this->_cache = $cache;
        $this->_cachePrefix = $cachePrefix;
    }

    /**
     * Retrieve an array of all definitions by name.
     *
     * @return array
     */
    public function getDefinitionEntityNames()
    {
        $cacheName = $this->_cachePrefix."ze__entityDefinitionNames";

        $entityNames = $this->_cache->load($cacheName);
        if($entityNames === false) {
            $entityNames = $this->_metadataFactory->getDefinitionEntityNames();
            $this->_cache->save($entityNames, $cacheName);
        }

        return $entityNames;
    }

    /**
     * @param  string $entityName
     * @return Zend_Entity_Definition_Entity
     */
    public function getDefinitionByEntityName($entityName)
    {
        $cacheName = $this->_cachePrefix.$entityName;

        $entityDef = $this->_cache->load($cacheName);
        if($entityDef === false) {
            $entityDef = $this->_metadataFactory->getDefinitionByEntityName($entityName);
            $this->_cache->save($entityDef, $cacheName);
        }
        
        return $entityDef;
    }

    /**
     *
     * @param  string $visitorClass
     * @return Zend_Entity_Definition_MappingVisitor[]
     */
    public function transform($visitorClass)
    {
        $visitorMap = array();
        foreach($this->getDefinitionEntityNames() AS $entityName) {
            $visitor = new $visitorClass;
            $this->getDefinitionByEntityName($entityName)->visit($visitor, $this);
            $visitorMap[$entityName] = $visitor;
        }
        return $visitorMap;
    }
}