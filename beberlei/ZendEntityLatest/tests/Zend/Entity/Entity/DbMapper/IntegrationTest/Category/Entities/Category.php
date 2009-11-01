<?php

class Zend_Entity_Category
{
    public $id;
    public $parent;
    public $name;
    public $children = array();
}