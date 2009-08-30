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
}
