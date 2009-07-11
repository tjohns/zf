<?php

$def = new Zend_Entity_Mapper_Definition_Entity("Clinic_Bed", array("table" => "beds"));
$def->addPrimaryKey("id");

return $def;