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
            $state = array();
            foreach($object AS $k => $v) {
                if($v instanceof Zend_Entity_Collection_Interface && $v->__ze_wasLoadedFromDatabase() == false) {
                    $v = "*LAZYLOADCOLLECTION*";
                } else if($v instanceof Zend_Entity_LazyLoad_Entity) {
                    $v = "*LAZYLOADENTITY*";
                }
                $state[$k] = $v;
            }
            var_dump($state);
        } else if($object instanceof Zend_Entity_Collection_Interface) {
            if($object->__ze_wasLoadedFromDatabase() == false) {
                var_dump("*LAZYLOADCOLLECTION*");
            } else {
                foreach($object AS $k => $v) {
                    Zend_Entity_Debug::dump($v);
                }
            }
        }
    }
}