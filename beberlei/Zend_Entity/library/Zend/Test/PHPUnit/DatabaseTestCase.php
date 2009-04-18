<?php


abstract class Zend_Test_PHPUnit_DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    /**
     * Creates a new Zend Database Connection using the given Adapter and database schema name.
     *
     * @param  Zend_Db_Adapter_Abstract $connection
     * @param  string $schema
     * @return Zend_Test_PHPUnit_Database_Connection
     */
    protected function createZendDbConnection(Zend_Db_Adapter_Abstract $connection, $schema)
    {
        return new Zend_Test_PHPUnit_Database_Connection($connection, $schema);
    }

    /**
     * Convenience function to get access to the database connection.
     * 
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getAdapter()
    {
        return $this->getConnection()->getConnection();
    }

    /**
     * Returns the database operation executed in test setup.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getSetUpOperation()
    {
        return new PHPUnit_Extensions_Database_Operation_Composite(array(
            new Zend_Test_PHPUnit_Database_Operation_Truncate(),
            new Zend_Test_PHPUnit_Database_Operation_Insert(),
        ));
    }

    /**
     * Returns the database operation executed in test cleanup.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getTearDownOperation()
    {
        return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * Create a dataset based on multiple Zend_Db_Table instances
     *
     * @param  array $tables
     * @return Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet
     */
    protected function createDbTableDataSet(array $tables=array())
    {
        return new Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet();
    }

    /**
     * Create a table based on one Zend_Db_Table instance
     *
     * @param  Zend_Db_Table_Abstract $table
     * @return Zend_Test_PHPUnit_Database_DataSet_DbTable
     */
    protected function createDbTable(Zend_Db_Table_Abstract $table)
    {
        return new Zend_Test_PHPUnit_Database_DataSet_DbTable($table);
    }

    /**
     * Create a data table based on a Zend_Db_Table_Rowset instance
     *
     * @param  Zend_Db_Table_Rowset_Abstract $rowset
     * @return Zend_Test_PHPUnit_Database_DataSet_DbRowset
     */
    protected function createDbRowset(Zend_Db_Table_Rowset_Abstract $rowset)
    {
        return new Zend_Test_PHPUnit_Database_DataSet_DbRowset($rowset);
    }
}