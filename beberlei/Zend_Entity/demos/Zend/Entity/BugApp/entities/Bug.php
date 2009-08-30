<?php

class Bug
{
    public $id;
    public $description;
    public $created;
    public $status;
    public $engineer;
    public $reporter;
    public $products = null; // Make protected for encapsulation

    public function __construct()
    {
        $this->products = new Zend_Entity_Collection();
    }

    public function setEngineer(User $engineer)
    {
        $engineer->assignedToBug($this);
        $this->engineer = $engineer;
    }

    public function setReporter(User $reporter)
    {
        $reporter->addReportedBug($this);
        $this->reporter = $reporter;
    }

    public function assignToProduct($product)
    {
        $this->products[] = $product;
    }
}