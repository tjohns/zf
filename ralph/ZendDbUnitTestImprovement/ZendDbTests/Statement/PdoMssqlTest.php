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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Db/Statement/AbstractPdoTestCase.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Statement_PdoMssqlTest extends Zend_Db_Statement_AbstractPdoTestCase
{

    public function testStatementGetColumnMeta()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support meta data.');
    }

    public function testStatementExecuteWithParams()
    {
        $products = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_products');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementExecuteWithParams();
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindParamByPosition()
    {
        $products = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_products');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindParamByPosition();
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindParamByName()
    {
        $products = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_products');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindParamByName();
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindValueByPosition()
    {
        $products = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_products');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindValueByPosition();
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindValueByName()
    {
        $products = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_products');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindValueByName();
        $this->sharedFixture->dbAdapter->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function getDriver()
    {
        return 'Pdo_Mssql';
    }

}
