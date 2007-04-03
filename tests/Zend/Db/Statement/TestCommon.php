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

require_once 'Zend/Db/TestSetup.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class Zend_Db_Statement_TestCommon extends Zend_Db_TestSetup
{

    /**
     * Test the Statement's fetchAll() method.
     */
    public function testStatementFetchAll()
    {
        $stmt = $this->_db->query('SELECT * FROM products WHERE product_id > 1 ORDER BY product_id ASC');
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test the Statement's fetchColumn() method.
     */
    public function testStatementFetchColumn()
    {
        $stmt = $this->_db->query('SELECT * FROM products WHERE product_id > 1 ORDER BY product_id ASC');
        $result = $stmt->fetchColumn();
        $this->assertEquals(2, $result);
        $result = $stmt->fetchColumn();
        $this->assertEquals(3, $result);
    }

    /**
     * Test the Statement's fetchObject() method.
     */
    public function testStatementFetchObject()
    {
        $stmt = $this->_db->query('SELECT * FROM products WHERE product_id > 1 ORDER BY product_id ASC');
        $result = $stmt->fetchObject();
        $this->assertThat($result, $this->isInstanceOf('stdClass'), 'Expecting object of type stdClass, got '.get_class($result));
        $this->assertEquals('Linux', $result->product_name);
    }

}
