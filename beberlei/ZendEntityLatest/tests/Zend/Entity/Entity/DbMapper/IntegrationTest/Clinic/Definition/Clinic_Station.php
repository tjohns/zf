<?php

$def = new Zend_Entity_Definition_Entity("Clinic_Station", array("table" => "zfclinic_stations"));

$def->addPrimaryKey("id");
$def->addProperty("name");
$def->addCollection("beds", array(
    "key" => "station_id",
    "relation"   => new Zend_Entity_Definition_OneToManyRelation(
        "beds", array(
            "class" => "Clinic_Bed",
            "cascade" => "persist",
            "mappedBy" => "station",
        )
    ),
));

$def->addCollection("currentOccupancies", array(
    "key" => "station_id",
    "relation" => new Zend_Entity_Definition_OneToManyRelation(
        "id", array(
            "class" => "Clinic_Occupancy",
            "mappedBy" => "station",
        )
    ),
    "where" => "(zfclinic_occupancies.occupied_from >= NOW() AND zfclinic_occupancies.occupied_to <= NOW())"
));

return $def;