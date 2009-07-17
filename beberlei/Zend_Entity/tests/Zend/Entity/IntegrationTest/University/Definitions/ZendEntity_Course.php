<?php

$def = new Zend_Entity_Mapper_Definition_Entity("ZendEntity_Course");
$def->setTable("university_courses");

$def->addPrimaryKey("id", array(
    "columnName" => "course_id",
    "propertyType" => Zend_Entity_Mapper_Definition_Property::TYPE_INT,
));

$def->addProperty("name", array(
    'columnName' => 'course_name',
    'propertyType' => Zend_Entity_Mapper_Definition_Property::TYPE_STRING
));

$def->addManyToOneRelation("teacher", array(
    "columnName" => "teacher_id",
    "class" => "ZendEntity_Professor",
    "cascade" => "save",
));

return $def;
