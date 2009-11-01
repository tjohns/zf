<?php

$def = new Zend_Entity_Definition_Entity("Clinic_Patient", array("table" => "zfclinic_patients"));
$def->addPrimaryKey("id");
$def->addProperty("name");
$def->addProperty("social_security_number");
$def->addProperty("birth_date");

$def->addCollection("occupancies", array(
    "key" => "occupancy_id",
    "relation" => new Zend_Entity_Definition_OneToManyRelation(
        "occupancies", array(
            "class" => "Clinic_Occupancy",
            "mappedBy" => "patient",
        )
    ),
));

return $def;