<?php

$def = new Zend_Entity_Definition_Entity("Zend_Entity_Category");
$def->setTable("ze_category");
$def->addPrimaryKey("id", array("columnName" => "category_id"));
$def->addProperty("name");
$def->setStateTransformerClass("Property");

$def->addManyToOneRelation("parent", array(
    "class" => "Zend_Entity_Category",
    "columnName" => "parent",
    "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT, // ncessary only because Zend_Entity_Category allows no lazy load.
    "nullable" => true, // necessary for root node being null
));

$def->addCollection("children", array(
    "relation" => new Zend_Entity_Definition_OneToManyRelation(array(
        "class" => "Zend_Entity_Category",
        "mappedBy" => "parent",
        "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT, // ncessary only because Zend_Entity_Category allows no lazy load.
        "inverse" => true,
    )),
    "key" => "parent",
));

return $def;
