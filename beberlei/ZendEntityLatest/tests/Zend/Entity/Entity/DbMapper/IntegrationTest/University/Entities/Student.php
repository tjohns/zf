<?php

class ZendEntity_Student implements Zend_Entity_Interface
{
    protected $id;

    protected $name;

    protected $studentId;

    protected $currentCourses;

    public function __construct()
    {
        $this->currentCourses = new Zend_Entity_Collection();
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
            'studentId' => $this->studentId,
            'currentCourses' => $this->currentCourses,
        );
    }

    public function setState(array $state)
    {
        foreach($state AS $k => $v) {
            $this->$k = $v;
        }
    }
}
