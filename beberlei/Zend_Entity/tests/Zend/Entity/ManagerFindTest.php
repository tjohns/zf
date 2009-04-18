<?php

require_once dirname(__FILE__)."/../../TestHelper.php";
require_once "TestCase.php";

class Zend_Entity_ManagerFindTest extends Zend_Entity_TestCase
{
    public function testGetSelectFromUnknownEntityMapper()
    {
        $manager = $this->createEntityManager();
        $manager->select("UnknownEntity");
    }
}