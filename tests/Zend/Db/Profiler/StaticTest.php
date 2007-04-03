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

class Zend_Db_Profiler_StaticTest extends Zend_Db_TestSetup
{

    function testProfilerFactory()
    {
        $db = Zend_Db::factory('Static',
            array(
                'dbname'   => 'dummy',
                'profiler' => false
            )
        );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'),
            'Expected object of type Zend_Db_Adapter_Abstract, got '.get_class($db));

        $prof = $db->getProfiler();
        $this->assertThat($prof, $this->isInstanceOf('Zend_Db_Profiler'),
            'Expected object of type Zend_Db_Profiler, got '.get_class($prof));
        $this->assertFalse($prof->getEnabled());

        $db = Zend_Db::factory('Static',
            array(
                'dbname' => 'dummy',
                'profiler' => true
            )
        );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'),
            'Expected object of type Zend_Db_Adapter_Abstract, got '.get_class($db));

        $prof = $db->getProfiler();
        $this->assertThat($prof, $this->isInstanceOf('Zend_Db_Profiler'),
            'Expected object of type Zend_Db_Profiler, got '.get_class($prof));
        $this->assertTrue($prof->getEnabled());
    }

    function testProfilerSetEnabled()
    {
        $db = Zend_Db::factory('Static',
            array(
                'dbname' => 'dummy',
                'profiler' => false
            )
        );
        $prof = $db->getProfiler();
        $this->assertFalse($prof->getEnabled());
        $prof->setEnabled(true);
        $this->assertTrue($prof->getEnabled());
    }

    public function getDriver()
    {
        return 'Static';
    }

}
