<?php

abstract class Zend_Entity_Event_EventAbstract
{
    const PRE_INSERT   = 'preInsert';
    const POST_INSERT  = 'postInsert';
    const PRE_UPDATE   = 'preUpdate';
    const POST_UPDATE  = 'postUpdate';
    const PRE_DELETE   = 'preDelete';
    const POST_DELETE  = 'postDelete';
    const POST_LOAD    = 'postLoad';

    public function preInsert($entity)
    {
        
    }

    public function postInsert($entity)
    {

    }

    public function preUpdate($entity)
    {
        
    }

    public function postUpdate($entity)
    {
        
    }

    public function preDelete($entity)
    {
        
    }

    public function postDelete($entity)
    {
        
    }

    public function postLoad($entity)
    {
        
    }
}