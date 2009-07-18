<?php

class Zend_Entity_Mapper_Definition_OneToManyRelation extends Zend_Entity_Definition_OneToManyRelation
{
    public function __construct($propertyName, array $options=array())
    {
        trigger_error(
            "Use of 'Zend_Entity_Mapper_Definition_OneToManyRelation' is deprecated, ".
            "use 'Zend_Entity_Definition_OneToManyRelation' instead.", E_USER_NOTICE
        );
        parent::__construct($entityName, $options);
    }
}
