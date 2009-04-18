<?php

class Zend_Entity_Adapter_PDO_MySQL_ClinicScenario extends Zend_Entity_ScenarioData_Clinic_ScenarioTest
{
    protected function getConnection()
    {
        $dbname = "clinic_scenario";
        $db = Zend_Db::factory("pdo_mysql", array("username" => "clinicscenario", "password" => "", "dbname" => $dbname));
        return $this->createZendDbConnection($db, $dbname);
    }
}