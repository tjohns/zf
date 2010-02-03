<?php
class Zend_Rbac_Resource extends Zend_Rbac_Object
{
    const TYPE = 'resource';
	
    public function getType() {
        return self::TYPE;
    }
	
}