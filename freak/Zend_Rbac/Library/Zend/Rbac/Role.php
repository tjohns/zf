<?php
class Zend_Rbac_Role extends Zend_Rbac_Object
{
	const TYPE = 'role';
	    
    public function getType() {
        return self::TYPE;
    }
	
}