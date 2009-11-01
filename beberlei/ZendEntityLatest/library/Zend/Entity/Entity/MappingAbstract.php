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
 * @subpackage Defnition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract mapping that defines the minimum required datastructure for internal Zend_Entity components
 *
 * @uses       Zend_Entity_Definition_MappingVisitor
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_MappingAbstract implements Zend_Entity_Definition_MappingVisitor
{
    /**
     * @var string
     */
    public $class;

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
     * @var array
     */
    public $newProperties = array();

    /**
     * @var array
     */
    public $classAlias = array();

    /**
     * @var array
     */
    public $cascade = array();

    /**
     * @param Zend_Entity_Definition_Entity $entity
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     * @return void
     */
    public function acceptEntity(Zend_Entity_Definition_Entity $entity, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {
        $this->class = $entity->class;
        if(!is_string($this->class) || strlen($this->class) == 0) {
            throw new Zend_Entity_Exception("Invalid Class name given for an Entity.");
        }

        $this->classAlias[$this->class] = $this->class;
        $this->classAlias[$entity->proxyClass] = $this->class;
        $this->classAlias[$entity->getEntityName()] = $this->class;

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

        $this->_doAcceptEntity($entity);
    }

    /**
     * Accept Entity Definition for Storage Adapter (Mapper) specific configurations.
     * 
     * @param Zend_Entity_Definition_Entity $entity
     */
    abstract protected function _doAcceptEntity($entity);

    /**
     * @param Zend_Entity_Definition_RelationAbstract $relation
     * @param Zend_Entity_MetadataFactory_FactoryAbstract
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

            if($foreignDef->hasProperty($relation->mappedBy) == false) {
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

            if($relation instanceof Zend_Entity_Definition_OneToManyRelation) {
                $mapProperty = $foreignDef->getPropertyByName($relation->mappedBy);
                if($mapProperty instanceof Zend_Entity_Definition_RelationAbstract) {
                    if($mapProperty->inverse === false) {
                        $relation->inverse = true;
                    }
                }
            }
            if($relation instanceof Zend_Entity_Definition_ManyToManyRelation) {
                $mapProperty = $foreignDef->getPropertyByName($relation->mappedBy);
                if($mapProperty instanceof Zend_Entity_Definition_Collection) {
                    if($mapProperty->relation->inverse === false) {
                        throw new Zend_Entity_Exception(
                            "The relations '".$relation->propertyName."' on '".$this->class."' and ".
                            "'".$relation->mappedBy."' on '".$foreignDef->class."' are both marked ".
                            "as owning, which cannot be maintained. One reference has to be owning ".
                            "and the other one inverse."
                        );
                    }
                }
            }
        }

        if($relation instanceof Zend_Entity_Definition_ManyToManyRelation && $relation->columnName == null) {
            $relation->columnName = $foreignDef->getPrimaryKey()->columnName;
        }
    }

    /**
     * @param Zend_Entity_Definition_PrimaryKey $primaryKey
     * @param Zend_Entity_MetadataFactory_FactoryAbstract
     * @return void
     */
    protected function _acceptPrimaryKey($primaryKey, $metadataFactory)
    {
        $this->primaryKey = $primaryKey;
        $this->_doAcceptPrimaryKey($primaryKey, $metadataFactory);
    }

    /**
     * @param Zend_Entity_Definition_PrimaryKey $primaryKey
     * @param Zend_Entity_MetadataFactory_FactoryAbstract
     * @return void
     */
    abstract protected function _doAcceptPrimaryKey($primaryKey, $metadataFactory);

    /**
     * @param Zend_Entity_Definition_Collection $collection
     * @param Zend_Entity_MetadataFactory_FactoryAbstract
     * @return void
     */
    protected function _acceptCollection($collection, $metadataFactory)
    {
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

        $this->_acceptRelation($relation, $metadataFactory);

        $foreignDef = $metadataFactory->getDefinitionByEntityName($relation->class);
        if($collection->table == null) {
            $collection->setTable($foreignDef->getTable());
        }

        if($collection->key == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "The 'key' field is required in collection ".
                "definition of entity '".$this->class."' property ".
                "'".$collection->propertyName."'"
            );
        }

        // Handling of Unidirectional OneToMany relations
        if($collection->relation instanceof Zend_Entity_Definition_OneToManyRelation) {
            $mapProperty = $foreignDef->getPropertyByName($collection->relation->mappedBy);
            if((!($mapProperty instanceof Zend_Entity_Definition_Collection) && !($mapProperty instanceof Zend_Entity_Definition_RelationAbstract))) {
                if($foreignDef->getTable() == $collection->table) {
                    throw new Zend_Entity_Exception(
                        "The unidirectional OneToMany relation '".$collection->propertyName."' on entity ".
                        "'".$this->class."' requires a special join table much as a ManyToMany relation, ".
                        "however the table is set to the foreign entity '".$foreignDef->class."'s table."
                    );
                }
            }
        }
    }

    protected function _acceptArray($array)
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
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function acceptProperty(Zend_Entity_Definition_Property $property, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {
        $propertyName = $property->propertyName;

        if(!is_string($propertyName) || strlen($propertyName) == 0) {
            throw new Zend_Entity_Exception(
                "No name was given to a property in entity definition '".$this->class."'."
            );
        }

        if($property->columnName == null) {
            $property->columnName = $propertyName;
        }

        $this->newProperties[$propertyName] = $property; // @untested

        if($property instanceof Zend_Entity_Definition_PrimaryKey) {
            $this->_acceptPrimaryKey($property, $metadataFactory);
        } elseif($property instanceof Zend_Entity_Definition_RelationAbstract) {
            $this->_acceptRelation($property, $metadataFactory);
        } elseif($property instanceof Zend_Entity_Definition_Collection) {
            $this->_acceptCollection($property, $metadataFactory);
        } else if($property instanceof Zend_Entity_Definition_Array) {
            $this->_acceptArray($property);
        }

        if($property instanceof Zend_Entity_Definition_RelationAbstract) {
            if($property->inverse == false) {
                $this->toOneRelations[$propertyName] = $property;

                $columnName = $property->columnName;
                $this->columnNameToProperty[$columnName] = $propertyName;
                $this->sqlColumnAliasMap[$columnName] = $property->columnName;
            }

            if(count($property->cascade) > 0) {
                $this->cascade[$property->propertyName] = array("relation", $property->cascade);
            }
        } elseif($property instanceof Zend_Entity_Definition_Collection) {
            if(count($property->relation->cascade) > 0) {
                $this->cascade[$property->propertyName] = array("collection", $property->relation->cascade);
            }
            
            $this->toManyRelations[$propertyName] = $property;
        } elseif($property instanceof Zend_Entity_Definition_Array) {
            $this->elementCollections[$propertyName] = $property;
        } else {
            if($property instanceof Zend_Entity_Definition_Version) {
                $this->versionProperty = $property;
            }
            $columnName = $property->columnName;
            $this->columnNameToProperty[$columnName] = $propertyName;
            $this->sqlColumnAliasMap[$columnName] = $columnName;
        }
    }

    public function finalize()
    {
        $this->stateTransformer->setTargetClass($this->class, array_keys($this->newProperties));
    }
}