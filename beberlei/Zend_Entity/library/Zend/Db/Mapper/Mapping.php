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
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Contains Mapping Instructions for Database Storage Loader and Persister
 *
 * @uses       uses
 * @category   Zend
 * @package    package
 * @subpackage subpackage
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Mapping implements Zend_Entity_Definition_MappingVisitor
{
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
    public $schema;

    /**
     * @var Zend_Entity_Definition_PrimaryKey
     */
    public $primaryKey;

    /**
     * @var Zend_Entity_StateTransformer_Abstract
     */
    public $stateTransformer;

    /**
     * @var Zend_Entity_Definition_Version
     */
    public $versionProperty;

    /**
     * @var array
     */
    public $tableColumns = array();

    /**
     * @var array
     */
    public $propertyNames = array();

    /**
     * @var array
     */
    public $properties = array();

    /**
     * @var array
     */
    public $toOneRelations = array();

    /**
     * @var array
     */
    public $toManyRelations = array();

    /**
     * @var array
     */
    public $elementCollections = array();

    /**
     * @var array
     */
    public $sqlColumnAliasMap = array();

    /**
     * @var array
     */
    public $columnNameToProperty = array();

    /**
     * @param Zend_Entity_Definition_Entity $entity
     */
    public function acceptEntity(Zend_Entity_Definition_Entity $entity, Zend_Entity_MetadataFactory_Interface $metadataFactory)
    {
        $this->class = $entity->class;
        if(!is_string($this->class) || strlen($this->class) == 0) {
            throw new Zend_Entity_Exception("Invalid Class name given for an Entity.");
        }

        $this->table = $entity->table;
        if(!is_string($this->table) || strlen($this->table) == 0) {
            throw new Zend_Entity_Exception("Invalid table name given for entity '".$this->class."'.");
        }
        $this->schema = $entity->schema;
        
        $this->primaryKey = $entity->primaryKey;
        if($this->primaryKey == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "No primary key was set for entity '".$this->class."' but is a required attribute."
            );
        }

        $stateTransformerClass = $entity->getStateTransformerClass();
        if(class_exists($stateTransformerClass)) {
            $this->stateTransformer = new $stateTransformerClass;
            if(!($this->stateTransformer instanceof Zend_Entity_StateTransformer_Abstract)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No valid State Transformer given, '".$stateTransformerClass."' has to be ".
                    "of type 'Zend_Entity_StateTransformer_Abstract'."
                );
            }
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Invalid State Transformer Class '".$stateTransformerClass."' ".
                "name given in '".$this->class."' entity definition."
            );
        }
    }

    /**
     * @param Zend_Entity_Definition_RelationAbstract $relation
     * @param Zend_Entity_MetadataFactory_Interface
     * @return void
     */
    protected function _acceptRelation($relation, $metadataFactory)
    {
        if($relation->class == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Cannot compile relation due to missing class reference for ".
                "property '".$relation->propertyName."' in entity '".$this->class."'"
            );
        }

        $foreignDef = $metadataFactory->getDefinitionByEntityName($relation->class);
        if($relation->inverse == true) {
            if($relation->mappedBy == null) {
                throw new Zend_Entity_Exception(
                    "The inverse relation '".$relation->propertyName."' in ".
                    "'".$this->class."' has to specify a 'mappedBy' element."
                );
            }

            if($foreignDef->hasProperty( $relation->mappedBy ) == false) {
                throw new Zend_Entity_Exception(
                    "The mappedBy property '".$relation->mappedBy."' of the releation ".
                    "'".$relation->propertyName."' on entity '".$this->class."' ".
                    "does not exist on foreign entity '".$foreignDef->getClass()."'."
                );
            }
        } else {
            if($relation->mappedBy == null) {
                $relation->mappedBy = $foreignDef->getPrimaryKey()->propertyName;
            } elseif(!$foreignDef->hasProperty($relation->mappedBy)) {
                throw new Zend_Entity_Exception(
                    "The owing relation '".$relation->propertyName."' in ".
                    "'".$this->class."' has to specify a valid 'mappedBy' element, ".
                    "but '".$relation->mappedBy."' is unknown."
                );
            }
        }

        if($relation instanceof Zend_Entity_Definition_ManyToManyRelation && $relation->columnName == null) {
            $relation->columnName = $foreignDef->getPrimaryKey()->columnName;
        }
    }

    /**
     * @param Zend_Entity_Definition_PrimaryKey $primaryKey
     * @param Zend_Entity_MetadataFactory_Interface
     * @return void
     */
    protected function _acceptPrimaryKey($primaryKey, $metadataFactory)
    {
        $this->primaryKey = $primaryKey;
        if($primaryKey->getGenerator() === null) {
            $primaryKey->setGenerator(new Zend_Entity_Definition_Id_AutoIncrement());
        }
    }

    /**
     * @param Zend_Entity_Definition_Collection $collection
     * @param Zend_Entity_MetadataFactory_Interface
     * @return void
     */
    protected function _acceptCollection($collection, $metadataFactory)
    {
        if($collection->getCollectionType() == Zend_Entity_Definition_Collection::COLLECTION_RELATION) {
            $relation = $collection->relation;
            if($collection->relation == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No relation option was set in collection '".$collection->propertyName."'."
                );
            }

            if($collection->fetch === null) {
                $collection->fetch = $relation->fetch;
            }

            if($collection->table == null) {
                $foreignDef = $metadataFactory->getDefinitionByEntityName($relation->class);
                $collection->setTable($foreignDef->getTable());
            }

            $this->_acceptRelation($relation, $metadataFactory);
        } else if($collection->getCollectionType() == Zend_Entity_Definition_Collection::COLLECTION_ELEMENTS) {

        }

        if($collection->key == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "The 'key' field is required in collection ".
                "definition of entity '".$this->class."' property ".
                "'".$collection->propertyName."'"
            );
        }
    }

    public function _acceptArray($array)
    {
        /* @var $array Zend_Entity_Definition_Array */
        if($array->fetch === null) {
            $array->fetch = Zend_Entity_Definition_Property::FETCH_LAZY;
        }

        if($array->element == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "No 'element' option was set in collection '".$array->propertyName."' ".
                "of entity '".$this->class."'"
            );
        }
        if($array->table == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "The table field is required in collections ".
                "definition of entity '".$this->class."'."
            );
        }
        if($array->mapKey == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "No 'mapKey' option was set in collection '".$array->propertyName."' ".
                "of entity '".$this->class."'"
            );
        }

        if($array->key == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "The 'key' field is required in collection ".
                "definition of entity '".$this->class."' property ".
                "'".$array->propertyName."'"
            );
        }
    }

    /**
     * @param Zend_Entity_Definition_Property $property
     * @param Zend_Entity_MetadataFactory_Interface $metadataFactory
     */
    public function acceptProperty(Zend_Entity_Definition_Property $property, Zend_Entity_MetadataFactory_Interface $metadataFactory)
    {
        $propertyName = $property->propertyName;

        if(!is_string($propertyName) || strlen($propertyName) == 0) {
            throw new Zend_Entity_Exception(
                "No name was given to a property in entity definition '".$this->class."'."
            );
        }

        if($property instanceof Zend_Entity_Definition_PrimaryKey) {
            $this->_acceptPrimaryKey($property, $metadataFactory);
        } elseif($property instanceof Zend_Entity_Definition_RelationAbstract) {
            $this->_acceptRelation($property, $metadataFactory);
        } elseif($property instanceof Zend_Entity_Definition_Collection) {
            $this->_acceptCollection($property, $metadataFactory);
        } else if($property instanceof Zend_Entity_Definition_Array) {
            $this->_acceptArray($property);
        }

        if($property->columnName == null) {
            $property->columnName = $propertyName;
        }

        $this->propertyNames[] = $propertyName;
        if($property instanceof Zend_Entity_Definition_RelationAbstract) {
            if($property->inverse == false) {
                $this->toOneRelations[$propertyName] = $property;
                
                $columnName = $property->columnName;
                $this->columnNameToProperty[$columnName] = $property;
                $this->sqlColumnAliasMap[$columnName] = $property->columnName;
            }
        } elseif($property instanceof Zend_Entity_Definition_Collection) {
            $this->toManyRelations[$propertyName] = $property;
        } elseif($property instanceof Zend_Entity_Definition_Array) {
            $this->elementCollections[$propertyName] = $property;
        } else {
            if($property instanceof Zend_Entity_Definition_Version) {
                $this->versionProperty = $property;
            }
            if(!($property instanceof Zend_Entity_Definition_Formula)) {
                $this->properties[] = $property;
                $this->tableColumns[$propertyName] = $property;
            }
            $columnName = $property->columnName;
            $this->columnNameToProperty[$columnName] = $property;
            $this->sqlColumnAliasMap[$columnName] = $columnName;
        }
    }

    public function finalize()
    {
        $this->stateTransformer->setTargetClass($this->class, $this->propertyNames);
    }
}