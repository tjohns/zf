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
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Pdo' . DIRECTORY_SEPARATOR . 'MysqlTest.php';

/**
 * @package    Zend_Db_Adapter_Pdo_MysqlTest
 * @subpackage UnitTests
 */
class Zend_Db_Adapter_MysqliTest extends Zend_Db_Adapter_Pdo_MysqlTest
{

    public function getDriver()
    {
        // @todo: when the Mysqli adapter moves out of the incubator,
        // the following trick to allow it to be loaded should be removed.
        $incubator = dirname(dirname(dirname(dirname(dirname(__FILE__)))))
            . DIRECTORY_SEPARATOR . 'incubator' . DIRECTORY_SEPARATOR . 'library';
        Zend::loadClass('Zend_Db_Adapter_Mysqli', $incubator);
        // @todo: also load any auxiliary classes if necessary, e.g.:
        // Zend_Db_Adapter_Mysqli_Exception
        // Zend_Db_Statement_Mysqli
        // Zend_Db_Statement_Mysqli_Exception

        return 'Mysqli';
    }

    public function testExceptionInvalidLoginCredentials()
    {
        $params = $this->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Mysqli($params);
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Mysqli_Exception'));
            echo $e->getMessage();
        }
    }

}
