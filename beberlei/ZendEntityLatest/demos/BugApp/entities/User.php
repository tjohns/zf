<?php

class User
{
    public $id;
    public $name;
    public $reportedBugs = null;
    public $assignedBugs = null;

    public function __construct()
    {
        $this->reportedBugs = new Zend_Entity_Collection();
        $this->assignedBugs = new Zend_Entity_Collection();
    }

    public function addReportedBug(Bug $bug)
    {
        $this->reportedBugs[] = $bug;
    }

    public function assignedToBug(Bug $bug)
    {
        $this->assignedBugs[] = $bug;
    }

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'reportedBugs' => $this->reportedBugs,
            'assignedBugs' => $this->assignedBugs,
        );
    }

    public function setState(array $state)
    {
        foreach($state AS $k => $v) {
            $this->$k = $v;
        }
    }
}
