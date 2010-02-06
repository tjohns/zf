<?php
class Zend_Rbac_Subject extends Zend_Rbac_Object
{
    const TYPE = 'subject';
    
        
    public function getType() {
        return self::TYPE;
    }
}
