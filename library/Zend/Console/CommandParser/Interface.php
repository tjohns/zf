<?php

interface Zend_Console_CommandParser_Interface
{
    public function setArguments(Array $arguments = array());
    public function getRemainingArguments();
    public function parse();
    public function getResults();
    public function getResult($name);
}
