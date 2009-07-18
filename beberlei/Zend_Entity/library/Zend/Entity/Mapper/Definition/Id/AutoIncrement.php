<?php

class Zend_Entity_Mapper_Definition_Id_AutoIncrement extends Zend_Entity_Definition_Id_AutoIncrement
{
    public function __construct()
    {
        trigger_error(
            "Use of 'Zend_Entity_Mapper_Definition_Id_AutoIncrement' is deprecated, ".
            "use 'Zend_Entity_Definition_Id_AutoIncrement' instead.", E_USER_NOTICE
        );
    }
}