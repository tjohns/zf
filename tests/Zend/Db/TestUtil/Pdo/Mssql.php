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
 * @version    $Id$
 */


/**
 * @see Zend_Db_TestUtil_Pdo_Common
 */
require_once 'Zend/Db/TestUtil/Pdo/Common.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_TestUtil_Pdo_Mssql extends Zend_Db_TestUtil_Pdo_Common
{

    public function getParams(array $constants = array())
    {
        $constants = array (
            'host'     => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_HOSTNAME',
            'username' => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_USERNAME',
            'password' => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PASSWORD',
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_DATABASE',
            'port'     => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PORT'
        );

        return parent::getParams($constants);
    }

}
