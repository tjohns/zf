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
 * Abstract Factory which defines a large subset of the handling of metadata definitions
 *
 * @uses       ArrayAccess
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage MetadataFactory
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_MetadataFactory_FactoryAbstract implements ArrayAccess
{
    /**
     * @var string
     */
    private $_defaultAccess = Zend_Entity_Definition_Entity::ACCESS_ARRAY;

    /**
     * @var string
     */
    private $_defaultIdGeneratorClass = null;

    /**
     * @var array
     */
    private $_mappings = array();

    /**
     * @var array
     */
    private $_classAliases = array();

    /**
     * Retrieve an array of all definitions by name.
     *
     * @return array
     */
    abstract public function getDefinitionEntityNames();

    /**
     * Get an Entity Mapper Definition by the name of the Entity
     *
     * @param  string $entityName
     * @throws Zend_Entity_InvalidEntityException
     * @return Zend_Entity_Definition_Entity
     */
    abstract public function getDefinitionByEntityName($entityName);

    /**
     * Hash that allows to check the version of generated classes with the current implementation.
     *
     * @return string
     */
    abstract public function getCurrentVersionHash();

    /**
     * @param Zend_Entity_Definition_MappingVisitor $visitor
     */
    public function visit(Zend_Entity_Definition_MappingVisitor $visitor)
    {
        foreach($this->getDefinitionEntityNames() AS $entityName) {
            $this->getDefinitionByEntityName($entityName)->visit($visitor, $this);
        }
    }

    /**
     * Metadata are used for various reasons and a MappingVisitor implementation transforms them for necessary tasks.
     * 
     * @param  string $visitorClass
     * @param  array $options
     * @return Zend_Entity_Definition_MappingVisitor[]
     */
    public function transform($visitorClass, array $options=array())
    {
        $visitorMap = array();
        foreach($this->getDefinitionEntityNames() AS $entityName) {
            $visitor = new $visitorClass($options);
            $this->getDefinitionByEntityName($entityName)->visit($visitor, $this);
            $visitorMap[$entityName] = $visitor;
        }
        $this->_mappings = $visitorMap;
        foreach($visitorMap AS $mapping) {
            $this->_classAliases = array_merge(
                $this->_classAliases,
                $mapping->classAlias
            );
        }

        return $this;
    }

    /**
     * Set the default access strategy that all defined entities inherit from.
     *
     * @see Zend_Entity_Definition_Entity::ACCESS_ARRAY
     * @see Zend_Entity_Definition_Entity::ACCESS_PROPERTY
     * @see Zend_Entity_Definition_Entity::getAccess()
     * @param string $accessStrategy
     * @return Zend_Entity_MetadataFactory_FactoryAbstract
     */
    public function setDefaultAccess($accessStrategy)
    {
        $this->_defaultAccess = $accessStrategy;
        return $this;
    }

    /**
     * Get the default access strategy that all defined inherit from.
     *
     * @see Zend_Entity_Definition_Entity::ACCESS_ARRAY
     * @see Zend_Entity_Definition_Entity::ACCESS_PROPERTY
     * @see Zend_Entity_Definition_Entity::getAccess()
     * @return string
     */
    public function getDefaultAccess()
    {
        $this->_defaultAccess;
    }

    /**
     * @return string
     */
    public function getDefaultIdGeneratorClass()
    {
        return $this->_defaultIdGeneratorClass;
    }

    /**
     *
     * @param string $defaultIdGeneratorClass
     * @return Zend_Entity_MetadataFactory_FactoryAbstract
     */
    public function setDefaultIdGeneratorClass($defaultIdGeneratorClass)
    {
        if(!class_exists($defaultIdGeneratorClass)) {
            throw new Zend_Entity_Exception("IdGenerator class '".$defaultIdGeneratorClass."' does not exist!");
        }

        $this->_defaultIdGeneratorClass = $defaultIdGeneratorClass;
        return $this;
    }


    /**
     * Get normalized name of the given entity instance.
     *
     * Normalized entity names are required at many different locations to gain ahold of entities, for example in the IdentityMap.
     *
     * @throws Zend_Entity_InvalidEntityException
     * @param  object $entity
     * @return string
     */
    public function getEntityName($entity)
    {
        if(is_object($entity)) {
            $entityName = get_class($entity);
        } else {
            throw new Zend_Entity_InvalidEntityException();
        }

        if(isset($this->_classAliases[$entityName])) {
            $entityName = $this->_classAliases[$entityName];
        } else {
            throw new Zend_Entity_InvalidEntityException();
        }
        return $entityName;
    }

    /**
     * Get the mapping class of a given entity.
     *
     * @param string $entityName
     * @return Zend_Entity_MappingAbstract
     */
    public function offsetGet($entityName)
    {
        if(!isset($this->_classAliases[$entityName])) {
            throw new Zend_Entity_InvalidEntityException($entityName);
        }
        $entityName = $this->_classAliases[$entityName];
        return $this->_mappings[$entityName];
    }

    /**
     * Does a mapping exist for the given entity name?
     *
     * @param string $entityName
     * @return bool
     */
    public function offsetExists($entityName)
    {
        return isset($this->_classAliases[$entityName]);
    }

    /**
     * @param string $entityName
     * @param mixed $value
     */
    public function offsetSet($entityName, $value)
    {
        throw new Zend_Entity_Exception("Setting mappings is not supported in Metadata Factories.");
    }

    /**
     * @param string $entityName
     */
    public function offsetUnset($entityName)
    {
        throw new Zend_Entity_Exception("Unsetting mappings is not supported in Metadata Factories.");
    }
}