<?php

class Product
{
    public $id;
    public $name;

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
        );
    }

    public function setState(array $state)
    {
        foreach($state AS $k => $v) {
            $this->$k = $v;
        }
    }
}