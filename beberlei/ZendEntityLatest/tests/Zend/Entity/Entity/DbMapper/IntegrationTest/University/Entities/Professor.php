<?php

class ZendEntity_Professor implements Zend_Entity_Interface
{
    protected $id;

    protected $name;

    protected $teachingCourses;

    protected $salary;

    public function __construct()
    {
        $this->teachingCourses = new Zend_Entity_Collection();
    }

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
            'teachingCourses' => $this->teachingCourses,
            'salary' => $this->salary,
        );
    }

    public function setState(array $state)
    {
        foreach($state AS $k => $v) {
            $this->$k = $v;
        }
    }
}