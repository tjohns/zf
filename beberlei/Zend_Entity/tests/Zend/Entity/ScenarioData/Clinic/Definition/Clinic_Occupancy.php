<?php

$def = new Zend_Entity_Mapper_Definition_Entity("Clinic_Occupancy", array("table" => "occupancies"));

$def->addPrimaryKey("id");
$def->addManyToOne("patient", array("columnName" => "patient_id", "propertyRef" => "patient", "class" => "Clinic_Patient", "cascade" => "save"));
$def->addManyToOne("bed",     array("columnName" => "bed_id", "class" => "Clinic_Bed"));
$def->addManyToOne("station", array("columnName" => "station_id", "class" => "Clinic_Station"));
$def->addProperty("occupiedFrom");
$def->addProperty("occupiedTo");

return $def;