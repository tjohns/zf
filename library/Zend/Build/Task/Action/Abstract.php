<?php

abstract class Zend_Build_Task_Action_Abstract
{
    abstract public function getName();
    
    public function satisfyDependencies()
    {}
    
    public function setup()
    {}
    
    public function rollback()
    {}
    
    public function cleanup()
    {}
    
}