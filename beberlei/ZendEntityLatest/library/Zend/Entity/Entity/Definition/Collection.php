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

class Zend_Entity_Definition_Collection extends Zend_Entity_Definition_ArrayAbstract
{
    const COLLECTION_RELATION = 'relation';
    const COLLECTION_ELEMENTS = 'elements';

    /**
     * @var string
     */
    public $type = self::COLLECTION_RELATION;

    /**
     * @var Zend_Entity_Definition_RelationAbstract
     */
    public $relation = null;

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
     * @return Zend_Entity_Definition_RelationAbstract
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Set type of relation of this collection.
     *
     * @param Zend_Entity_Definition_RelationAbstract $relation
     */
    public function setRelation(Zend_Entity_Definition_RelationAbstract $relation)
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
}