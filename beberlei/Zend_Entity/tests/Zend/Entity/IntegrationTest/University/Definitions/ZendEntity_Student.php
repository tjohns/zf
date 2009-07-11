<?php

$def = new Zend_Entity_Mapper_Definition_Entity("ZendEntity_Student");
$def->setTable("university_students");

$def->addPrimaryKey("id", array(
    "columnName" => "student_id",
    "propertyType" => Zend_Entity_Mapper_Definition_Property::TYPE_INT,
));

$def->addProperty("name", array(
    'columnName' => 'student_name',
    'propertyType' => Zend_Entity_Mapper_Definition_Property::TYPE_STRING
));

$def->addProperty("studentId", array(
    'columnName' => 'student_campus_id',
    'propertyType' => Zend_Entity_Mapper_Definition_Property::TYPE_INT
));

$def->addCollection("currentCourses", array(
    'relation' => new Zend_Entity_Mapper_Definition_ManyToManyRelation(
        "currentCourses", array(
            'class' => 'ZendEntity_Course',
        )
    ),
    'key' => 'student_id',
    'table' => 'university_students_semester_courses',
));

return $def;
