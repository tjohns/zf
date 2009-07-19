<?php

class Zend_Entity_Definition_Version extends Zend_Entity_Definition_Property
{
    public function isNullable()
    {
        return false;
    }

    public function isUnique()
    {
        return false;
    }

    public function castPropertyToSqlType($propertyValue)
    {
        return (int)$propertyValue;
    }

    public function castColumnToPhpType($columnValue)
    {
        return (int)$columnValue;
    }
}