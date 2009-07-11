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
    /**
     * @var string
     */
    protected $_className;

    /**
     * @var array
     */
    protected $_extensions = array();

    /**
     * @var array
     */
    protected $_relations = array();

    /**
     * @var Zend_Entity_Mapper_Definition_PrimaryKey
     */
    protected $_id = null;

    /**
     * @var string
     */
    protected $_loaderClass = null;

    /**
     * @var string
     */
    protected $_persisterClass = null;

    /**
     * @var string
     */
    protected $_stateTransformerClass = "Zend_Entity_Mapper_StateTransformer_Array";

    /**
     * @var Zend_Entity_Mapper_StateTransformer_Abstract
     */
    protected $_stateTransformer = null;

    /**
     * Construct entity
     * 
     * @param string $className
     * @param array $options
     */
    public function __construct($className=null, $options=array())
    {
        $this->setClass($className);
        parent::__construct($className, $options);
    }

    /**
     * Get entity classname
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_className;
    }

    /**
     * Set entity classname
     * 
     * @param string $className
     */
    public function setClass($className)
    {
        $this->_className = $className;
    }

    /**
     * Implementation of Abstract Property Add method.
     * 
     * @param  string $propertyType
     * @param  string $propertyName
     * @param  array $options
     * @return object
     */
    public function add($propertyType, $propertyName, $options)
    {
        if(isset($this->_properties[$propertyName]) || isset($this->_relations[$propertyName])) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("Property ".$propertyName." already exists! Cannot have the same property twice.");
        }
        $property = Zend_Entity_Mapper_Definition_Utility::loadDefinition($propertyType, $propertyName, $options);

        if($property instanceof Zend_Entity_Mapper_Definition_Table) {
            $this->_extensions[$propertyName] = $property;
        } elseif($property instanceof Zend_Entity_Mapper_Definition_AbstractRelation) {
            $this->_relations[$propertyName] = $property;
        } elseif($property instanceof Zend_Entity_Mapper_Definition_Property_Abstract) {
            if($property instanceof Zend_Entity_Mapper_Definition_PrimaryKey) {
                $this->_id = $property;
            }
            $this->_properties[$propertyName] = $property;
        }
        return $property;
    }

    /**
     * Get property object by name
     *
     * @param  string $propertyName
     * @return object
     */
    public function getPropertyByName($propertyName)
    {
        if(isset($this->_properties[$propertyName])) {
            return $this->_properties[$propertyName];
        } else if(isset($this->_relations[$propertyName])) {
            return $this->_relations[$propertyName];
        } else if(isset($this->_extensions[$propertyName])) {
            return $this->_extensions[$propertyName];
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("No Property found!");
        }
    }

    /**
     * Return all relations
     *
     * @return Zend_Entity_Mapper_Definition_AbstractRelation[]
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

    /**
     * Get the loader class name
     * 
     * @return string
     */
    public function getLoaderClass()
    {
        return $this->_loaderClass;
    }

    /**
     * Set the loader class name
     * 
     * @param string $loaderClass
     * @return void
     */
    public function setLoaderClass($loaderClass)
    {
        $this->_loaderClass = $loaderClass;
    }

    /**
     * Get the persister class name
     *
     * @return string
     */
    public function getPersisterClass()
    {
        return $this->_persisterClass;
    }

    /**
     * Set persister class name
     *
     * @param string $persisterClass
     * @return void
     */
    public function setPersisterClass($persisterClass)
    {
        $this->_persisterClass = $persisterClass;
    }

    /**
     * @return string
     */
    public function getStateTransformerClass()
    {
        return $this->_stateTransformerClass;
    }

    /**
     * @param string $stateTransformerClass
     */
    public function setStateTransformerClass($stateTransformerClass)
    {
        switch(strtolower($stateTransformerClass)) {
            case 'array':
                $stateTransformerClass = "Zend_Entity_Mapper_StateTransformer_Array";
                break;
            case 'property':
                $stateTransformerClass = "Zend_Entity_Mapper_StateTransformer_Property";
                break;
            case 'reflection':
                $stateTransformerClass = "Zend_Entity_Mapper_StateTransformer_Reflection";
                break;
        }
        $this->_stateTransformerClass = $stateTransformerClass;
    }

    /**
     * @return Zend_Entity_Mapper_StateTransformer_Abstract
     */
    public function getStateTransformer()
    {
        return $this->_stateTransformer;
    }

    /**
     * Compile Entity Definition
     * 
     * @param Zend_Entity_MetadataFactory_Interface $map
     * @return void
     */
    public function compile(Zend_Entity_MetadataFactory_Interface $map)
    {
        if($this->_id === null) {
            throw new Zend_Entity_Exception(
                "No primary key was set for entity '".$this->getClass()."' but is a required attribute."
            );
        }

        $propertyNames = array();
        foreach($this->_properties AS $property) {
            $property->compile($this, $map);
            $propertyNames[] = $property->getPropertyName();
        }
        foreach($this->_relations AS $relation) {
            $relation->compile($this, $map);
            $propertyNames[] = $relation->getPropertyName();
        }
        foreach($this->_extensions AS $extension) {
            $extension->compile($this, $map);
            $propertyNames[] = $extension->getPropertyName();
        }

        if(class_exists($this->_stateTransformerClass)) {
            $this->_stateTransformer = new $this->_stateTransformerClass();
            $this->_stateTransformer->setPropertyNames($propertyNames);
        } else {
            throw new Zend_Entity_Exception(
                "Invalid State Transformer Class name given in '".$this->getClass()."' entity definition."
            );
        }

        $this->setPersisterClass("Zend_Entity_Mapper_Persister_Simple");
    }
}