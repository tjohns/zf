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
    protected $load = Zend_Entity_Mapper_Definition_Property::LOAD_LAZY;

    protected $relation;

    protected $table;

    protected $key;

    protected $fetch = Zend_Entity_Mapper_Definition_Property::FETCH_SELECT;

    protected $cascade = Zend_Entity_Mapper_Definition_Property::CASCADE_NONE;

    protected $_additionalWhereCondition = null;

    public function __construct($propertyName, $options)
    {
        $this->setPropertyName($propertyName);
        parent::__construct(null, $options);
    }

    public function getPropertyName()
    {
        return $this->propertyName;
    }

    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    public function getLoad()
    {
        return $this->load;
    }

    public function setLoad($load)
    {
        $this->load = $load;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setTable($collectionTable)
    {
        $this->table = $collectionTable;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getFetch()
    {
        return $this->fetch;
    }

    public function setFetch($fetch)
    {
        $this->fetch = $fetch;
    }

    public function getColumnName()
    {
        return null;
    }

    public function getCascade()
    {
        return $this->cascade;
    }

    public function setCascade($cascade)
    {
        $this->cascade = $cascade;
    }

    public function setMapKey($mapKey)
    {
        $this->mapKey = $mapKey;
    }

    public function getMapKey()
    {
        return $this->mapKey;
    }

    public function setWhere($additionalWhereCondition)
    {
        $this->_additionalWhereCondition = $additionalWhereCondition;
    }

    public function getWhere()
    {
        return $this->_additionalWhereCondition;
    }

    /**
     *
     * @return Zend_Entity_Mapper_Definition_Relation_Interface
     */
    public function getRelation()
    {
        return $this->relation;
    }

    public function setRelation(Zend_Entity_Mapper_Definition_Relation_Interface $relation)
    {
        $this->relation = $relation;
    }

    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        $relation = $this->getRelation();
        if($relation == null) {
            throw new Exception("No relation option was set in collection '".$this->getPropertyName()."' of entity '".$entityDef->getClass()."'");
        }

        $foreignDef = $map->getDefinitionByEntityName($relation->getClass());
        if($this->getTable() == null) {
            $this->setTable($foreignDef->getTable());
        }

        if($this->getKey() == null) {
            throw new Exception("The foreign-key field is a required column in collections definition of entity '".$entityDef->getClass()."'.");
        }

        $relation->compile($entityDef, $map);
    }
}