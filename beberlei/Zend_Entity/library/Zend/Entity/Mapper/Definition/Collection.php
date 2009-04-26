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

class Zend_Entity_Mapper_Definition_Collection extends Zend_Entity_Mapper_Definition_Table
    implements Zend_Entity_Mapper_Definition_Property_Interface
{
    const COLLECTION_RELATION = 'relation';
    const COLLECTION_ELEMENTS = 'elements';

    /**
     * @var string
     */
    protected $_collectionType = self::COLLECTION_RELATION;

    /**
     * @var Zend_Entity_Mapper_Definition_Relation
     */
    protected $relation;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $_additionalWhereCondition = null;

    /**
     * @var string
     */
    protected $_orderByCondition = null;

    /**
     * @var Zend_Entity_Mapper_Definition_Property
     */
    protected $_mapKey = null;

    /**
     * @var Zend_Entity_Mapper_Definition_Property
     */
    protected $_element = null;

    /**
     * Construct Collection Definition
     *
     * @param string $propertyName
     * @param array $options
     */
    public function __construct($propertyName, array $options=array())
    {
        $this->setPropertyName($propertyName);
        parent::__construct(null, $options);
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @param string $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
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
        $this->_additionalWhereCondition = $additionalWhereCondition;
    }

    /**
     * Get additional where condition
     * 
     * @return string
     */
    public function getWhere()
    {
        return $this->_additionalWhereCondition;
    }

    /**
     * What type of relation is this collection?
     *
     * @return Zend_Entity_Mapper_Definition_Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Set type of relation of this collection.
     *
     * @param Zend_Entity_Mapper_Definition_Relation $relation
     */
    public function setRelation(Zend_Entity_Mapper_Definition_Relation $relation)
    {
        $this->_collectionType = self::COLLECTION_RELATION;
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
        $this->_orderByCondition = $orderByCondition;
    }

    /**
     * Get Order By Clause
     * 
     * @return string
     */
    public function getOrderBy()
    {
        return $this->_orderByCondition;
    }

    /**
     * Set the property Name of the Map Key
     *
     * This option is only relevant for collection maps, not for lists. It defaults to the primary key.
     *
     * @param Zend_Entity_Mapper_Definition_Property $mapKey
     */
    public function setMapKey(Zend_Entity_Mapper_Definition_Property $mapKey)
    {
        $this->_collectionType = self::COLLECTION_ELEMENTS;
        $this->_mapKey = $mapKey;
    }

    /**
     * Get the property name of the map key.
     * 
     * @return Zend_Entity_Mapper_Definition_Property
     */
    public function getMapKey()
    {
        return $this->_mapKey;
    }

    /**
     * Set the element property for a map-element collection.
     *
     * @param Zend_Entity_Mapper_Definition_Property $element
     * @return void
     */
    public function setElement(Zend_Entity_Mapper_Definition_Property $element)
    {
        $this->_collectionType = self::COLLECTION_ELEMENTS;
        $this->_element = $element;
    }

    /**
     * Get element property
     *
     * @return Zend_Entity_Mapper_Definition_Property
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Get the type of the collection.
     * 
     * @return string
     */
    public function getCollectionType()
    {
        return $this->_collectionType;
    }

    /**
     * Compile Definition of Collection.
     *
     * @throws Zend_Entity_Exception
     * @param Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param Zend_Entity_Resource_Interface $map
     */
    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
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

            if($this->getTable() == null) {
                $foreignDef = $map->getDefinitionByEntityName($relation->getClass());
                $this->setTable($foreignDef->getTable());
            }
            
            $relation->compile($entityDef, $map);
        } else if($this->getCollectionType() == self::COLLECTION_ELEMENTS) {
            if($this->getElement() == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "No element option was set in collection '".$this->getPropertyName()."' ".
                    "of entity '".$entityDef->getClass()."'"
                );
            } elseif($this->getTable() == null) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "The table field is required in collections ".
                    "definition of entity '".$entityDef->getClass()."'."
                );
            }

            $mapKey = $this->getMapKey();
            if($mapKey !== null) {
                $mapKey->compile($entityDef, $map);
            }
            $this->getElement()->compile($entityDef, $map);
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