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

class Zend_Entity_Definition_Collection extends Zend_Entity_Definition_Property_Abstract
{
    const COLLECTION_RELATION = 'relation';
    const COLLECTION_ELEMENTS = 'elements';

    /**
     * @var string
     */
    public $type = self::COLLECTION_RELATION;

    /**
     * @var Zend_Entity_Definition_AbstractRelation
     */
    public $relation = null;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $whereRestriction = null;

    /**
     * @var string
     */
    public $orderByRestriction = null;

    /**
     * @var Zend_Entity_Definition_Property
     */
    public $mapKey = null;

    /**
     * @var Zend_Entity_Definition_Property
     */
    public $element = null;

    /**
     * @var boolean
     */
    public $inverse = false;

    /**
     * @var string
     */
    public $fetch = null;

    /**
     * Construct Collection Definition
     *
     * @param string $propertyName
     * @param array $options
     */
    public function __construct($propertyName, array $options=array())
    {
        $this->setPropertyName($propertyName);

        foreach($options AS $k => $v) {
            $method = "set".ucfirst($k);
            if(method_exists($this, $method)) {
                call_user_func_array(array($this, $method), array($v));
            }
        }
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $collectionTable
     */
    public function setTable($collectionTable)
    {
        $this->table = $collectionTable;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Set Where for Additional Conditional Clause on the Collection.
     *
     * @param string $additionalWhereCondition
     */
    public function setWhere($additionalWhereCondition)
    {
        $this->whereRestriction = $additionalWhereCondition;
    }

    /**
     * Get additional where condition
     * 
     * @return string
     */
    public function getWhere()
    {
        return $this->whereRestriction;
    }

    /**
     * What type of relation is this collection?
     *
     * @return Zend_Entity_Definition_AbstractRelation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Set type of relation of this collection.
     *
     * @param Zend_Entity_Definition_AbstractRelation $relation
     */
    public function setRelation(Zend_Entity_Definition_AbstractRelation $relation)
    {
        $this->type = self::COLLECTION_RELATION;
        $this->relation = $relation;
    }

    /**
     * Set Order By Clause options for selecting an order of the collection items.
     *
     * @param string $orderByCondition
     * @return void
     */
    public function setOrderBy($orderByCondition)
    {
        $this->orderByRestriction = $orderByCondition;
    }

    /**
     * Get Order By Clause
     * 
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderByRestriction;
    }

    /**
     * Set the property Name of the Map Key
     *
     * This option is only relevant for collection maps, not for lists. It defaults to the primary key.
     *
     * @param string $mapKey
     */
    public function setMapKey($mapKey)
    {
        if(!is_string($mapKey)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Map-Key option is required to be a string."
            );
        }

        $this->type = self::COLLECTION_ELEMENTS;
        $this->mapKey = $mapKey;
    }

    /**
     * Get the property name of the map key.
     * 
     * @return Zend_Entity_Definition_Property
     */
    public function getMapKey()
    {
        return $this->mapKey;
    }

    /**
     * Set the element property for a map-element collection.
     *
     * @param string $element
     * @return void
     */
    public function setElement($element)
    {
        if(!is_string($element)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Element option is required to be a string."
            );
        }

        $this->type = self::COLLECTION_ELEMENTS;
        $this->element = $element;
    }

    /**
     * Get element property
     *
     * @return Zend_Entity_Definition_Property
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Get the type of the collection.
     * 
     * @return string
     */
    public function getCollectionType()
    {
        return $this->type;
    }

    /**
     * Is this collection the inverse of the relation?
     *
     * @return boolean
     */
    public function getInverse()
    {
        return $this->inverse;
    }

    /**
     * Set Inverse configuration
     *
     * @param boolean $inverse
     * @return void
     */
    public function setInverse($inverse)
    {
        $this->inverse = $inverse;
        return $this;
    }

    /**
     * @return string
     */
    public function getFetch()
    {
        return $this->fetch;
    }

    /**
     *
     * @param string $fetch
     */
    public function setFetch($fetch)
    {
        $this->fetch = $fetch;
        return $this;
    }

    /**
     * Compile Definition of Collection.
     *
     * @throws Zend_Entity_Exception
     * @param Zend_Entity_Definition_Entity $entityDef
     * @param Zend_Entity_MetadataFactory_Interface $map
     */
    public function compile(Zend_Entity_Definition_Entity $entityDef, Zend_Entity_MetadataFactory_Interface $map)
    {
        if($this->getCollectionType() == self::COLLECTION_RELATION) {
            $relation = $this->getRelation();
            if($relation == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No relation option was set in collection '".$this->getPropertyName()."' ".
                    "of entity '".$entityDef->getClass()."'"
                );
            }

            if($this->fetch === null) {
                $this->fetch = $relation->getFetch();
            }

            if($this->getTable() == null) {
                $foreignDef = $map->getDefinitionByEntityName($relation->getClass());
                $this->setTable($foreignDef->getTable());
            }
            
            $relation->compile($entityDef, $map);
        } else if($this->getCollectionType() == self::COLLECTION_ELEMENTS) {
            if($this->fetch === null) {
                $this->fetch = Zend_Entity_Definition_Property::FETCH_LAZY;
            }

            if($this->getElement() == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No 'element' option was set in collection '".$this->getPropertyName()."' ".
                    "of entity '".$entityDef->getClass()."'"
                );
            }
            if($this->getTable() == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "The table field is required in collections ".
                    "definition of entity '".$entityDef->getClass()."'."
                );
            }
            if($this->getMapKey() == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No 'mapKey' option was set in collection '".$this->getPropertyName()."' ".
                    "of entity '".$entityDef->getClass()."'"
                );
            }
        }

        if($this->getKey() == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "The foreign-key field is required in collections ".
                "definition of entity '".$entityDef->getClass()."'."
            );
        }
    }
}