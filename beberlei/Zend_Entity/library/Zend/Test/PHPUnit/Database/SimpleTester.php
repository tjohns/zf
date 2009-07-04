<?php

class Zend_Test_PHPUnit_Database_SimpleTester extends PHPUnit_Extensions_Database_DefaultTester
{
    /**
     * Creates a new default database tester using the given connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     */
    public function __construct(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        if(!($connection instanceof Zend_Test_PHPUnit_Database_Connection)) {
            require_once "Zend/Test/PHPUnit/Database/Exception.php";
            throw new Zend_Test_PHPUnit_Database_Exception("Not a valid Zend_Test_PHPUnit_Database_Connection instance, ".get_class($connection)." given!");
        }

        $this->connection = $connection;
        $this->setUpOperation = new PHPUnit_Extensions_Database_Operation_Composite(array(
            new Zend_Test_PHPUnit_Database_Operation_Truncate(),
            new Zend_Test_PHPUnit_Database_Operation_Insert(),
        ));
        $this->tearDownOperation = PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function setUpDatabase(PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        $this->setDataSet($dataSet);
        $this->onSetUp();
    }
}