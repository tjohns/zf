<?php

interface Zend_Entity_Collection_Interface extends Iterator, Countable, ArrayAccess
{
    public function __ze_getRemoved();

    public function __ze_getAdded();

    public function __ze_wasLoadedFromDatabase();
}