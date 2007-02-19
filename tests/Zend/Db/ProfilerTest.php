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
 * Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Db
 * @subpackage UnitTests
 */
class Zend_Db_ProfilerTest extends PHPUnit_Framework_TestCase
{

    function testProfilerFactory()
    {
        $db = Zend_Db::factory('pdo_sqlite',
            array(
                'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE,
                'profiler' => false
            )
        );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'), 'Expected object of type Zend_Db_Adapter_Abstract');

        $prof = $db->getProfiler();
        $this->assertThat($prof, $this->isInstanceOf('Zend_Db_Profiler'), 'Expected object of type Zend_Db_Profiler');
        $this->assertFalse($prof->getEnabled());

        $db = Zend_Db::factory('pdo_sqlite',
            array(
                'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE,
                'profiler' => true
            )
        );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'), 'Expected object of type Zend_Db_Adapter_Abstract');

        $prof = $db->getProfiler();
        $this->assertThat($prof, $this->isInstanceOf('Zend_Db_Profiler'), 'Expected object of type Zend_Db_Profiler');
        $this->assertTrue($prof->getEnabled());
    }

    function testProfilerSetEnabled()
    {
        $db = Zend_Db::factory('pdo_sqlite',
            array(
                'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE,
                'profiler' => false
            )
        );
        $prof = $db->getProfiler();
        $this->assertFalse($prof->getEnabled());
        $prof->setEnabled(true);
        $this->assertTrue($prof->getEnabled());
    }

}
