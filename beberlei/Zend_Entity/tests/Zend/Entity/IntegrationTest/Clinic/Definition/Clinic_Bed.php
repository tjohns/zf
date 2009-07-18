<?php

$def = new Zend_Entity_Definition_Entity("Clinic_Bed", array("table" => "beds"));

$def->addPrimaryKey("id");
$def->addManyToOneRelation("station", array(
    "class"       => "Clinic_Station",
    "columnName"  => "station_id",
    "propertyRef" => "id",
    "load"        => "directly",
));
$def->addCollection("occupancyPlan", array(
    "key" => "bed_id",
    "relation" => new Zend_Entity_Definition_OneToManyRelation(
        "occupancyPlan", array(
            "class" => "Clinic_Occupancy",
            "mappedBy" => "bed",
        )
    ),
));

return $def;