<?php

$def = new Zend_Entity_Mapper_Definition_Entity("ZendEntity_Professor");
$def->setTable("university_professors");

$def->addPrimaryKey("id", array(
    'columnName' => 'professor_id',
    'propertyType' => Zend_Entity_Mapper_Definition_Property::TYPE_INT
));

$def->addProperty('name');
$def->addProperty('salary', array(
    'propertyType' => Zend_Entity_Mapper_Definition_Property::TYPE_INT
));

$def->addCollection("teachingCourses", array(
    'relation' => new Zend_Entity_Mapper_Definition_OneToManyRelation("teachingCourses", array(
        'class' => 'ZendEntity_Course',
        "cascade" => "save",
    )),
    'key' => 'teacher_id',
));

return $def;