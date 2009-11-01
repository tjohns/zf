<?php

class ZendEntity_Course implements Zend_Entity_Interface
{
    protected $id;

    protected $name;

    protected $teacher;

    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    public function __set($name, $value)
    {
        if(property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'teacher' => $this->teacher,
        );
    }

    public function setState(array $state)
    {
        foreach($state AS $k => $v) {
            $this->$k = $v;
        }
    }
}