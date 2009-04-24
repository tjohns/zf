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
        $this->relation = $relation;
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
        $relation = $this->getRelation();
        if($relation == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "No relation option was set in collection '".$this->getPropertyName()."' ".
                "of entity '".$entityDef->getClass()."'"
            );
        }

        $foreignDef = $map->getDefinitionByEntityName($relation->getClass());
        if($this->getTable() == null) {
            $this->setTable($foreignDef->getTable());
        }

        if($this->getKey() == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "The foreign-key field is a required column in collections ".
                "definition of entity '".$entityDef->getClass()."'."
            );
        }

        $relation->compile($entityDef, $map);
    }
}