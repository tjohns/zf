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
 * @package    Db
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
class Zend_Entity_Mapper_MappingInstruction extends Zend_Entity_Definition_VisitorAbstract
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
        $this->class = $entity->getClass();
        $this->table = $entity->getTable();
        
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
     * @param Zend_Entity_Definition_AbstractRelation $relation
     * @param Zend_Entity_MetadataFactory_Interface
     * @return void
     */
    protected function _acceptRelation($relation, $metadataFactory)
    {
        if($relation->getClass() == null) {
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
            if($collection->fetch === null) {
                $collection->fetch = Zend_Entity_Definition_Property::FETCH_LAZY;
            }

            if($collection->element == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No 'element' option was set in collection '".$collection->propertyName."' ".
                    "of entity '".$this->class."'"
                );
            }
            if($collection->table == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "The table field is required in collections ".
                    "definition of entity '".$this->class."'."
                );
            }
            if($collection->mapKey == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No 'mapKey' option was set in collection '".$collection->propertyName."' ".
                    "of entity '".$this->class."'"
                );
            }
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

    /**
     * @param Zend_Entity_Definition_Property_Abstract $property
     * @param Zend_Entity_MetadataFactory_Interface $metadataFactory
     */
    public function acceptProperty(Zend_Entity_Definition_Property_Abstract $property, Zend_Entity_MetadataFactory_Interface $metadataFactory)
    {
        if($property instanceof Zend_Entity_Definition_PrimaryKey) {
            $this->_acceptPrimaryKey($property, $metadataFactory);
        } elseif($property instanceof Zend_Entity_Definition_AbstractRelation) {
            $this->_acceptRelation($property, $metadataFactory);
        } elseif($property instanceof Zend_Entity_Definition_Collection) {
            $this->_acceptCollection($property, $metadataFactory);
        }

        if($property->columnName == null) {
            $property->columnName = $property->propertyName;
        }

        $propertyName = $property->propertyName;
        $this->propertyNames[] = $propertyName;
        if($property instanceof Zend_Entity_Definition_AbstractRelation) {
            if($property->inverse == false) {
                $this->toOneRelations[$propertyName] = $property;
                
                $columnName = $property->columnName;
                $this->columnNameToProperty[$columnName] = $property;
                $this->sqlColumnAliasMap[$columnName] = $property->columnName;
            }
        } elseif($property instanceof Zend_Entity_Definition_Collection) {
            if($property->type == Zend_Entity_Definition_Collection::COLLECTION_RELATION) {
                $this->toManyRelations[$propertyName] = $property;
            } elseif($property->type == Zend_Entity_Definition_Collection::COLLECTION_ELEMENTS) {
                $this->elementCollections[$propertyName] = $property;
            }
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
        $this->stateTransformer->setPropertyNames($this->propertyNames);
    }
}