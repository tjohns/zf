<?php

class ZendEntity_Student implements Zend_Entity_Interface
{
    public $id;

    public $name;

    public $studentId;

    public $currentCourses;

    public function __construct()
    {
        $this->currentCourses = new Zend_Entity_Collection();
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
