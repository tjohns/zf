<?php

$def = new Zend_Entity_Definition_Entity("User");
$def->setTable("zfaccounts");
$def->setAccess("Property");

$def->addPrimaryKey("id", array(
    "columnName" => "account_id",
    "generator" => new Zend_Entity_Definition_Id_AutoIncrement(),
));

$def->addProperty("name", array(
    "columnName" => "account_name",
));

$def->addCollection("reportedBugs", array(
    "relation" => new Zend_Entity_Definition_OneToManyRelation(array(
        "class" => "Bug",
        "inverse" => true,
        "mappedBy" => "reporter",
    )),
    "key" => "reported_by",
    "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT,
));

$def->addCollection("assignedBugs", array(
    "relation" => new Zend_Entity_Definition_OneToManyRelation(array(
        "class" => "Bug",
        "inverse" => true,
        "mappedBy" => "engineer",
    )),
    "key" => "assigned_to",
    "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT,
));

return $def;