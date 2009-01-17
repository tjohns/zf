<?php

class Zend_Tool_Project_Profile_Iterator_EnabledResource extends RecursiveFilterIterator
{
    public function accept()
    {
        return $this->current()->isEnabled();
    }
}