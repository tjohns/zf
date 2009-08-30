<?php
$def = new Zend_Entity_Definition_Entity("Product");
$def->setTable("zfproducts");
$def->setAccess("Property");

$def->addPrimaryKey("id", array(
    "columnName" => "product_id",
    "generator" => new Zend_Entity_Definition_Id_AutoIncrement(),
));

$def->addProperty("name", array(
    "columnName" => "product_name",
));

return $def;