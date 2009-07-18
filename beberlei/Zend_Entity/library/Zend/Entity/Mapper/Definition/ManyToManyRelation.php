<?php

class Zend_Entity_Mapper_Definition_ManyToManyRelation extends Zend_Entity_Definition_OneToManyRelation
{
    public function __construct($propertyName, array $options=array())
    {
        trigger_error(
            "Use of 'Zend_Entity_Mapper_Definition_ManyToManyRelation' is deprecated, ".
            "use 'Zend_Entity_Definition_ManyToManyRelation' instead.", E_USER_NOTICE
        );
        parent::__construct($entityName, $options);
    }
}
