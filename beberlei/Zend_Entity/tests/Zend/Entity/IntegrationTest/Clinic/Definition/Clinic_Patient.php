<?php

$def = new Zend_Entity_Mapper_Definition_Entity("Clinic_Patient", array("table" => "patients"));
$def->addPrimaryKey("id");
$def->addProperty("name");
$def->addProperty("socialSecurityNumber");
$def->addProperty("birthDate");

$def->addCollection("occupancies", array(
    "key" => "occupancy_id",
    "relation" => new Zend_Entity_Mapper_Definition_OneToManyRelation("id", array("class" => "Clinic_Occupancy")),
));

return $def;