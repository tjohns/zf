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

class Zend_Entity_Mapper_Definition_Entity extends Zend_Entity_Mapper_Definition_Table
{
    protected $_className;

    protected $_extensions = array();

    protected $_relations = array();

    protected $_id;

    protected $_loaderClass = null;

    protected $_persisterClass = null;

    public function __construct($className=null, $options=array())
    {
        $this->setClass($className);
        parent::__construct($className, $options);
    }

    public function getClass()
    {
        if($this->_className == null) {
            throw new Exception("Class of the Entity is not set in the definition.");
        }
        return $this->_className;
    }

    public function setClass($className)
    {
        $this->_className = $className;
    }

    /**
     * Add a Composite Key
     *
     * Ugly, but it works!
     *
     * @param array $key
     * @param array $options
     */
    public function addCompositeKey($key, array $options=array())
    {
        if(!is_array($key)) {
            throw new Exception("CAnnot add invalid composite key, has to have array as first value.");
        }
        $this->_id = Zend_Entity_Mapper_Definition_Utility::loadDefinition('CompositeKey', $key, $options);
        foreach($key as $field) {
            $this->addProperty($field);
            $this->_properties[$field] = $this->_id;
        }
        return $this->_id;
    }

    protected function _add($propertyType, $propertyName, $options)
    {
        if(isset($this->_properties[$propertyName]) || isset($this->_relations[$propertyName])) {
            throw new Exception("Property ".$propertyName." already exists! Cannot have the same property twice.");
        }
        $property = Zend_Entity_Mapper_Definition_Utility::loadDefinition($propertyType, $propertyName, $options);

        if( $property instanceof Zend_Entity_Mapper_Definition_Table ) {
            $this->_extensions[$propertyName] = $property;
        } elseif($property instanceof Zend_Entity_Mapper_Definition_Relation_Interface) {
            $this->_relations[$propertyName] = $property;
        } else {
            if($property instanceof Zend_Entity_Mapper_Definition_PrimaryKey) {
                $this->_id = $property;
            }
            $this->_properties[$propertyName] = $property;
        }
        return $property;
    }

    public function getPropertyByName($propertyName)
    {
        if(isset($this->_properties[$propertyName])) {
            return $this->_properties[$propertyName];
        } else if(isset($this->_relations[$propertyName])) {
            return $this->_relations[$propertyName];
        } else if(isset($this->_extensions[$propertyName])) {
            return $this->_extensions[$propertyName];
        } else {
            throw new Exception("No Property found!");
        }
    }

    /**
     * Return all relations
     *
     * @return Zend_Entity_Mapper_Definition_Relation_Interface[]
     */
    public function getRelations()
    {
        return $this->_relations;
    }

    /**
     * Extensions are fields that are not derived from a column but extend the state.
     *
     * Falling under this category are collections and subjoin elements.
     *
     * @return Zend_Entity_Mapper_Definition_Table[]
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }

    /**
     * Return Primary Key Definition Property of this EntityClass Definition
     *
     * @return Zend_Entity_Mapper_Definition_PrimaryKey
     */
    public function getPrimaryKey()
    {
        return $this->_id;
    }

    public function getLoaderClass()
    {
        return $this->_loaderClass;
    }

    public function setLoaderClass($loaderClass)
    {
        $this->_loaderClass = $loaderClass;
    }

    public function getPersisterClass()
    {
        return $this->_persisterClass;
    }

    public function setPersisterClass($persisterClass)
    {
        $this->_persisterClass = $persisterClass;
    }

    public function compile(Zend_Entity_Resource_Interface $map)
    {
        foreach($this->getProperties() AS $property) {
            $property->compile($this, $map);
        }
        foreach($this->getRelations() AS $relation) {
            $relation->compile($this, $map);
        }
        foreach($this->getExtensions() AS $extension) {
            $extension->compile($this, $map);
        }

        if(count($this->getExtensions()) > 0) {
            $this->setPersisterClass("Zend_Entity_Mapper_Persister_CollectionCascade");
        } else if(count($this->getRelations()) > 0) {
            $this->setPersisterClass("Zend_Entity_Mapper_Persister_EntityCascade");
        } else {
            $this->setPersisterClass("Zend_Entity_Mapper_Persister_Simple");
        }
    }
}