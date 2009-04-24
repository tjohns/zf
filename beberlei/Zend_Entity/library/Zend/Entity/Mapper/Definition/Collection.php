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
     * @var string
     */
    protected $load = Zend_Entity_Mapper_Definition_Property::LOAD_LAZY;

    /**
     * @var Zend_Entity_Mapper_Definition_Relation_Interface
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
    protected $fetch = Zend_Entity_Mapper_Definition_Property::FETCH_LAZY;

    /**
     * @var string
     */
    protected $cascade = Zend_Entity_Mapper_Definition_Property::CASCADE_NONE;

    /**
     * @var string
     */
    protected $_additionalWhereCondition = null;

    /**
     * @var array
     */
    static protected $_allowedFetchValues = array(
        Zend_Entity_Mapper_Definition_Property::FETCH_JOIN,
        Zend_Entity_Mapper_Definition_Property::FETCH_LAZY,
        Zend_Entity_Mapper_Definition_Property::FETCH_SELECT,
    );

    /**
     * @var array
     */
    static protected $_allowedCascadeValues = array(
        Zend_Entity_Mapper_Definition_Property::CASCADE_ALL,
        Zend_Entity_Mapper_Definition_Property::CASCADE_NONE,
        Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE,
        Zend_Entity_Mapper_Definition_Property::CASCADE_DELETE,
    );

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
     * @return string
     */
    public function getFetch()
    {
        return $this->fetch;
    }

    /**
     * @param string $fetch
     */
    public function setFetch($fetch)
    {
        if(in_array($fetch, self::$_allowedFetchValues)) {
            $this->fetch = $fetch;
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Cannot set fetching-strategy of collection ".
                "'".$this->getPropertyName()."' to unknown value '".$fetch."'"
            );
        }
    }

    /**
     * @return string
     */
    public function getColumnName()
    {
        return null;
    }

    /**
     * Get Cascading type of Collection
     *
     * @return string
     */
    public function getCascade()
    {
        return $this->cascade;
    }

    /**
     * Set Cascading type of collection
     *
     * @param string $cascade
     * @throws Zend_Entity_Exception
     */
    public function setCascade($cascade)
    {
        if(!in_array($cascade, self::$_allowedCascadeValues)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "An invalid cascade value '".$cascade."' is set in collection ".
                "definition '".$this->getPropertyName()."'."
            );
        }
        $this->cascade = $cascade;
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
     * @return Zend_Entity_Mapper_Definition_Relation_Interface
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Set type of relation of this collection.
     *
     * @param Zend_Entity_Mapper_Definition_Relation_Interface $relation
     */
    public function setRelation(Zend_Entity_Mapper_Definition_Relation_Interface $relation)
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