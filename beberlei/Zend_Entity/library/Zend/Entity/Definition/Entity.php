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
    const CHANGEPOLICY_PASSTHROUGH_EXPLICIT = 'passthrough_explicit';
    const CHANGEPOLICY_PASSTHROUGH_IMPLICIT = 'passthrough_implicit';

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $schema = null;

    /**
     * @var Zend_Entity_Definition_PrimaryKey
     */
    public $primaryKey = null;

    /**
     * @var string
     */
    protected $_entityName = null;

    /**
     * @var array
     */
    protected $_properties = array();

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
     * @var Zend_Entity_Defintion_Version
     */
    protected $_version = null;

    /**
     * @var string
     */
    protected $_changePolicy = self::CHANGEPOLICY_PASSTHROUGH_EXPLICIT;

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
        return $this->class;
    }

    /**
     * Set entity classname
     * 
     * @param string $className
     */
    public function setClass($className)
    {
        $this->class = $className;
    }

    /**
     * Get current tablename
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set table
     *
     * @param string $tableName
     */
    public function setTable($tableName)
    {
        $this->table = $tableName;
    }

    /**
     * Get schema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Set schema of this entity.
     * 
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     *
     * @return string
     */
    public function getEntityName()
    {
        if($this->_entityName == null) {
            return $this->class;
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
            $this->primaryKey = $property;
        } else if($property instanceof Zend_Entity_Definition_Version) {
            $this->_version = $property;
        }
        $this->_properties[$propertyName] = $property;
        return $property;
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
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getStateTransformerClass()
    {
        return $this->_stateTransformerClass;
    }

    /**
     *
     * @param string $accessStrategy
     */
    public function setAccess($accessStrategy)
    {
        $this->setStateTransformerClass($accessStrategy);
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->getStateTransformerClass();
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
     * @return string
     */
    public function getChangePolicy()
    {
        return $this->_changePolicy;
    }

    /**
     * @param string $changePolicy
     */
    public function setChangePolicy($changePolicy)
    {
        $this->_changePolicy = $changePolicy;
    }

    /**
     * @param Zend_Entity_Definition_MappingVisitor $visitor
     * @param Zend_Entity_MetadataFactory_Interface $metadataFactory
     */
    public function visit(Zend_Entity_Definition_MappingVisitor $visitor, Zend_Entity_MetadataFactory_Interface $metadataFactory)
    {
        $visitor->acceptEntity($this, $metadataFactory);
        foreach($this->getProperties() AS $property) {
            $visitor->acceptProperty($property, $metadataFactory);
        }
        $visitor->finalize();
    }
}