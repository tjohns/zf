<?php

$def = new Zend_Entity_Definition_Entity("Bug");
$def->setTable("zfbugs");
$def->setAccess("Property");

$def->addPrimaryKey("id", array(
    "columnName" => "bug_id",
    "generator" => new Zend_Entity_Definition_Id_AutoIncrement()
));

$def->addProperty("description", array("columnName" => "bug_description"));
$def->addProperty("created", array(
    "columnName" => "bug_created",
    "propertyType" => Zend_Entity_Definition_Property::TYPE_DATETIME,
));
$def->addProperty("status", array(
    "columnName" => "bug_status",
));

$def->addManyToOneRelation("reporter", array(
    "columnName" => "reported_by",
    "class" => "User",
    "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT,
));

$def->addManyToOneRelation("engineer", array(
    "columnName" => "assigned_to",
    "class" => "User",
    "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT,
));

$def->addCollection("products", array(
    "relation" => new Zend_Entity_Definition_ManyToManyRelation(array(
        "class" => "Product",
        "columnName" => "product_id", // Join Column Product Key
        "inverse" => false, // Mark as owning
    )),
    "table" => "zfbugs_products", // Join Table Name
    "key" => "bug_id", // Join Table Bug Key
    "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT,
));

return $def;