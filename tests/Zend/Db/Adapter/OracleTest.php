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

    public function testFetchAll()
    {
        $result = $this->_db->query(
            'SELECT * FROM ' . self::TableName . ' WHERE date_created > :created_date',
            array('created_date' => '2006-01-01')
        );

        $rows = $result->fetchAll();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('1', $rows[0]['id']);
    }

}
