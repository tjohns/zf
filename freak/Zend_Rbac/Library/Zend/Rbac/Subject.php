<?php
class Zend_Rbac_Subject extends Zend_Rbac_Object
{
    const TYPE = 'Subject';
    
        
    public function getType() {
        return self::TYPE;
    }
}
