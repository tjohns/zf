<?php

class Zend_Entity_Mapper_Query extends Zend_Entity_Query_QueryAbstract
{
    public function select($properties)
    {
        return $this;
    }

    public function where($property, $operatorOrValue, $valueOrConditionName = null, $conditionName = null)
    {
        return $this;
    }

    public function combine(array $conditionNames, $operation, $combinedConditionName = null)
    {
        return $this;
    }

    public function with($entityName)
    {
        return $this;
    }

    public function join($entityName)
    {
        return $this;
    }

    public function joinInner($entityName)
    {
        return $this;
    }

    public function joinLeft($entityName)
    {
        return $this;
    }

    public function joinRight($entityName)
    {
        return $this;
    }

    public function order($property, $sortBy="ASC")
    {
        return $this;
    }

    public function distinct()
    {
        return $this;
    }

    public function group($spec)
    {
        return $this;
    }

    public function having($condition, $operatorOrValue, $valueOrConditionName=null, $conditionName=null)
    {
        return $this;
    }

    public function combineHaving(array $conditionNames, $operation, $combinedConditionName = null)
    {
        return $this;
    }
    public function __toString() {
    }
    public function setFirstResult($offset) {
    }
    public function setMaxResults($itemCountPerPage) {
    }
    public function getResultList() {
    }
    public function count() {
        
    }
}