<?php

$def = new Zend_Entity_Definition_Entity("EmptyEntityDefinition", array("table" => "beds"));
$def->addPrimaryKey("id");

return $def;