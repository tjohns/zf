<?php

require_once dirname(__FILE__)."/../../../../TestHelper.php";

class Zend_Entity_Adapter_PDO_Mysql_InvoiceScenarioTest extends Zend_Entity_ScenarioData_Invoice_ScenarioTest
{
    protected function getConnection()
    {
        $dbname = "invoice_scenario";
        $db = Zend_Db::factory("pdo_mysql", array("username" => "clinicscenario", "password" => "", "dbname" => $dbname));
        return $this->createZendDbConnection($db, $dbname);
    }
}