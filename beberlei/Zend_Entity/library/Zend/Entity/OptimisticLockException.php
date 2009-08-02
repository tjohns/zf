<?php

class Zend_Entity_OptimisticLockException extends Exception
{
    private $_entity = null;

    public function __construct($entity)
    {
        $this->_entity = $entity;
        parent::__construct(
            "The entity to update is already at a newer version ".
            "in the database and cannot be saved in this state."
        );
    }

    public function getEntity()
    {
        return $this->_entity;
    }
}