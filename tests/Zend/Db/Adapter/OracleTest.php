<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Common class is DB independant
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Pdo' . DIRECTORY_SEPARATOR . 'OciTest.php';


/**
 * @package    Zend_Db_Adapter_Pdo_MysqlTest
 * @subpackage UnitTests
 */
class Zend_Db_Adapter_OracleTest extends Zend_Db_Adapter_Pdo_OciTest
{

    public function getDriver()
    {
        return 'Oracle';
    }

    protected function tearDownMetadata()
    {
        $tableList = $this->_db->fetchCol('SELECT TABLE_NAME FROM ALL_TABLES');
        if (in_array(self::TABLE_NAME, $tableList['TABLE_NAME'])) {
            $this->_db->query($this->getDropTableSQL());
        }
        $seqList = $this->_db->fetchCol('SELECT SEQUENCE_NAME FROM ALL_SEQUENCES');
        if (in_array(self::SEQUENCE_NAME, $seqList['SEQUENCE_NAME'])) {
            $this->_db->query($this->getDropSequenceSQL());
        }
    }

    public function testFetchAll()
    {
        $result = $this->_db->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE date_created > :created_date',
            array('created_date' => '2006-01-01')
        );

        $rows = $result->fetchAll();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('1', $rows[0]['ID']);
    }

    public function testSelect()
    {
        $select = $this->_db->select();
        $this->assertThat($select, $this->isInstanceOf('Zend_Db_Select'));

        $select->from(self::TABLE_NAME);
        $result = $this->_db->query($select);
        $row = $result->fetch();
        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row['ID']); // correct data
    }

    public function testExceptionInvalidLoginCredentials()
    {
        $params = $this->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Oracle($params);
            $this->fail('Expected to catch Zend_Db_Adapter_Oracle_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Oracle_Exception'), 'Expected to catch Zend_Db_Adapter_Oracle_Exception, got '.get_class($e));
        }
    }

}
