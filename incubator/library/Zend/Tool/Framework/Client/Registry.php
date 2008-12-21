<?php

class Zend_Tool_Framework_Client_Registry extends ArrayObject 
{

    /**
     * Registry object provides storage for shared objects.
     * @var Zend_Registry
     */
    protected static $_instance = null;

    /**
     * Retrieves the default registry instance.
     *
     * @return Zend_Registry
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
