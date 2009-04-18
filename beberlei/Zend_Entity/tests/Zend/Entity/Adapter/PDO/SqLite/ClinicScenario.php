<?php

class Zend_Entity_Adapter_PDO_SqLite_ClinicScenario extends Zend_Entity_ScenarioData_Clinic_ScenarioTest
{
    protected function getConnection()
    {
        $dbname = "clinic_scenario";
        $db = Zend_Db::factory("pdo_sqlite", array("dbname" => ZEND_ENTITY_CLINIC_SQLITE_DATA));
        return $this->createZendDbConnection($db, $dbname);
    }

    public function testNoCurrentOccupanciesInStation()
    {
        $this->markTestIncomplete('Problem with NOW() in SqLite. How to implement generic Sql Expressions?');
    }

    public function testAddEmergencyOcuupancyToStation()
    {
        $this->markTestIncomplete('Problem with NOW() in SqLite. How to implement generic Sql Expressions?');
    }
}