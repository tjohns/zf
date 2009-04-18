<?php

class Zend_Entity_Debug
{
    /**
     * @param Zend_Entity_Interface|Zend_Entity_Collection_Interface $object
     */
    static public function dump($object)
    {
        if($object instanceof Zend_Entity_Interface) {
            $object = $object->getState();
            foreach($object AS $k => $v) {
                if($v instanceof Zend_Entity_Mapper_LazyLoad_Collection) {
                    $v = "*LAZYLOADCOLLECTION*";
                } else if($v instanceof Zend_Entity_Mapper_LazyLoad_Entity) {
                    $v = "*LAZYLOADOBJECT*";
                }
                $object[$k] = $v;
            }
            var_dump($object);
        } else if($object instanceof Zend_Entity_Collection_Interface) {
            foreach($object AS $k => $v) {
                Zend_Entity_Debug::dump($v);
            }
        }
    }
}