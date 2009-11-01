<?php

class Zend_Entity_Definition_Id_AutoIncrement extends Zend_Db_Mapper_Id_AutoIncrement
{
    /**
     * @param string $tableName
     * @param string $primaryKey
     */
    public function __construct($tableName=null, $primaryKey=null)
    {
        trigger_error("Zend_Entity_Definition_Id_AutoIncrement namespace is deprecated, use Zend_Db_Mapper_Id_AutoIncrement instead.", E_USER_NOTICE);
        parent::__construct($tableName, $primaryKey);
    }
}