<?php

class Zend_Entity_Mapper_Definition_Entity extends Zend_Entity_Definition_Entity
{
    public function __construct($entityName, array $options=array())
    {
        trigger_error(
            "Use of 'Zend_Entity_Mapper_Definition_Entity' is deprecated, ".
            "use 'Zend_Entity_Definition_Entity' instead.", E_USER_NOTICE
        );
        parent::__construct($entityName, $options);
    }
}