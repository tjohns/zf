<?php

interface Zend_Entity_Collection_Interface extends Iterator, Countable, ArrayAccess
{
    public function add($entity);

    public function remove($index);

    public function getRemoved();

    public function getAdded();

    public function wasLoadedFromDatabase();
}