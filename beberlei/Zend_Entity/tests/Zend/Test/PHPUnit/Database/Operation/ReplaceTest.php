<?php

require_once dirname(__FILE__)."/../../../../TestHelper.php";
require_once "PHPUnit/Extensions/Database/Operation/Replace.php";

class Zend_Test_PHPUnit_Database_Operation_ReplaceTest extends PHPUnit_Framework_TestCase
{
    public function testReplace()
    {
        $dbname = "clinic_scenario";
        $adapterMock = Zend_Db::factory("pdo_mysql", array("username" => "clinicscenario", "password" => "", "dbname" => $dbname));
        $connectionMock = new Zend_Test_PHPUnit_Database_Connection($adapterMock, $dbname);

        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/../_files/testdata.xml");

        $replaceOperation = new PHPUnit_Extensions_Database_Operation_Replace();
        $replaceOperation->execute($connectionMock, $dataSet);
    }
}