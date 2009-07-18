<?php

$def = new Zend_Entity_Mapper_Definition_Entity("Clinic_Station", array("table" => "stations"));

$def->addPrimaryKey("id");
$def->addProperty("name");
$def->addCollection("beds", array(
    "key" => "station_id",
    "relation"   => new Zend_Entity_Mapper_Definition_OneToManyRelation(
        "beds", array(
            "class" => "Clinic_Bed",
            "cascade" => "save",
            "mappedBy" => "station",
        )
    ),
));

$def->addCollection("currentOccupancies", array(
    "key" => "station_id",
    "relation" => new Zend_Entity_Mapper_Definition_OneToManyRelation(
        "id", array(
            "class" => "Clinic_Occupancy",
            "mappedBy" => "station",
        )
    ),
    "where" => "(occupancies.occupiedFrom >= NOW() AND occupancies.occupiedTo <= NOW())"
));

return $def;