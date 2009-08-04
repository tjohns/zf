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

class Zend_Entity_Definition_Entity
{
    /**
     * @var string
     */
    protected $_className;

    /**
     * @var string
     */
    protected $_entityName = null;

    /**
     * @var string
     */
    protected $_tableName;

    /**
     * @var array
     */
    protected $_properties = array();

    /**
     * @var Zend_Entity_Definition_PrimaryKey
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
    protected $_stateTransformerClass = "Zend_Entity_StateTransformer_Array";

    /**
     * @var Zend_Entity_StateTransformer_Abstract
     */
    protected $_stateTransformer = null;

    /**
     * @var boolean
     */
    private $_isCompiled = false;

    /**
     * @var Zend_Entity_Defintion_Version
     */
    private $_version = null;

    /**
     * Construct entity
     * 
     * @param string $className
     * @param array $options
     */
    public function __construct($className=null, $options=array())
    {
        $this->setClass($className);
        $this->setTable($className);

        foreach($options AS $k => $v) {
            $method = "set".ucfirst($k);
            if(method_exists($this, $method)) {
                call_user_func_array(array($this, $method), array($v));
            }
        }
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
     * Get current tablename
     *
     * @return string
     */
    public function getTable()
    {
        if($this->_tableName == null) {
            throw new Exception("No table has been set for the definition.");
        }
        return $this->_tableName;
    }

    /**
     * Set table
     *
     * @param string $tableName
     */
    public function setTable($tableName)
    {
        $this->_tableName = $tableName;
    }

    /**
     *
     * @return string
     */
    public function getEntityName()
    {
        if($this->_entityName == null) {
            return $this->_className;
        } else {
            return $this->_entityName;
        }
    }

    /**
     * @param string $entityName
     */
    public function setEntityName($entityName)
    {
        $this->_entityName = $entityName;
    }

    /**
     *
     * @param  string $propertyName
     * @return boolean
     */
    public function hasProperty($propertyName)
    {
        return isset($this->_properties[$propertyName]);
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
        if($this->hasProperty($propertyName)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("Property ".$propertyName." already exists! Cannot have the same property twice.");
        }
        $property = Zend_Entity_Definition_Utility::loadDefinition($propertyType, $propertyName, $options);

        if($property instanceof Zend_Entity_Definition_PrimaryKey) {
            $this->_id = $property;
        } else if($property instanceof Zend_Entity_Definition_Version) {
            $this->_version = $property;
        }
        $this->_properties[$propertyName] = $property;
        return $property;
    }

    public function addInstance($property)
    {
        $propertyName = $property->getPropertyName();
        $this->_properties[$propertyName] = $property;
        return $this;
    }

    /**
     * Add new property via magic __call()
     *
     * @param  string $method
     * @param  array $args
     * @return object
     */
    public function __call($method, $args)
    {
        if(substr($method, 0, 3) == "add") {
            $propertyType = substr($method, 3);

            if(!isset($args[0]) || !is_string($args[0])) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "First argument of '".$propertyType."' has to be a Property Name of type string."
                );
            } else {
                $propertyName = $args[0];
            }
            if(!isset($args[1]) || !is_array($args[1])) {
                $options = array();
            } else {
                $options = $args[1];
            }
            return $this->add($propertyType, $propertyName, $options);
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("Unknown method '".$method."' called.");
        }
    }

    /**
     * Get all current properties of table
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
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
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("No Property found!");
        }
    }

    /**
     * Return Primary Key Definition Property of this EntityClass Definition
     *
     * @return Zend_Entity_Definition_PrimaryKey
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
                $stateTransformerClass = "Zend_Entity_StateTransformer_Array";
                break;
            case 'property':
                $stateTransformerClass = "Zend_Entity_StateTransformer_Property";
                break;
            case 'reflection':
                $stateTransformerClass = "Zend_Entity_StateTransformer_Reflection";
                break;
        }
        $this->_stateTransformerClass = $stateTransformerClass;
    }

    /**
     * @return Zend_Entity_StateTransformer_Abstract
     */
    public function getStateTransformer()
    {
        return $this->_stateTransformer;
    }

    /**
     * @param Zend_Entity_StateTransformer_Abstract $stateTransformer 
     */
    public function setStateTransformer(Zend_Entity_StateTransformer_Abstract $stateTransformer)
    {
        $this->_stateTransformer = $stateTransformer;
    }

    /**
     * @return Zend_Entity_Definition_Version
     */
    public function getVersionProperty()
    {
        return $this->_version;
    }

    /**
     * Compile Entity Definition
     * 
     * @param Zend_Entity_MetadataFactory_Interface $map
     * @return void
     */
    final public function compile(Zend_Entity_MetadataFactory_Interface $map)
    {
        if($this->_isCompiled == false) {
            $this->_isCompiled = true;
            $this->_compile($map);
        }
    }

    /**
     * @param Zend_Entity_MetadataFactory_Interface $map
     */
    protected function _compile(Zend_Entity_MetadataFactory_Interface $map)
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

        if(class_exists($this->_stateTransformerClass)) {
            $this->_stateTransformer = new $this->_stateTransformerClass();
            $this->_stateTransformer->setPropertyNames($propertyNames);
        } else {
            throw new Zend_Entity_Exception(
                "Invalid State Transformer Class '".$this->_stateTransformerClass."' ".
                "name given in '".$this->getClass()."' entity definition."
            );
        }

        $this->setPersisterClass("Zend_Entity_Mapper_Persister_Simple");
    }
}