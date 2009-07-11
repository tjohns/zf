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
    protected $_resourceMap = null;

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    /**
     * @param Zend_Entity_MetadataFactory_Interface $resourceMap
     * @param Zend_Cache_Core $cache
     */
    public function __construct(Zend_Entity_MetadataFactory_Interface $resourceMap, Zend_Cache_Core $cache)
    {
        $this->_resourceMap = $resourceMap;
        $this->_cache = $cache;
    }

    /**
     * @param  string $entityName
     * @return Zend_Entity_Mapper_Definition_Entity
     */
    public function getDefinitionByEntityName($entityName)
    {
        if($this->_cache->test($entityName)) {
            $entityDef = $this->_cache->load($entityName);
        } else {
            $entityDef = $this->_resourceMap->getDefinitionByEntityName($entityName);
        }
        return $entityDef;
    }
}