<?php

class Zend_Entity_Fixture_VersionDef extends Zend_Entity_Fixture_SimpleFixtureDefs
{
    static public function createClassADefinition()
    {
        $def = parent::createClassADefinition();
        $def->addVersion("version", array("columnName" => 'a_version'));

        return $def;
    }
}