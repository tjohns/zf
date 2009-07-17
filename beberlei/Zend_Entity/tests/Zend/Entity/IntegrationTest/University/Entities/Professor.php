<?php

class ZendEntity_Professor implements Zend_Entity_Interface
{
    public $id;

    public $name;

    public $teachingCourses;

    public $salary;

    public function __construct()
    {
        $this->teachingCourses = new Zend_Entity_Collection();
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