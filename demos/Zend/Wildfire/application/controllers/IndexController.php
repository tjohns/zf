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
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/** Zend_Exception */
require_once 'Zend/Exception.php';

/**
 * Sample index controller.
 *
 * @category   Zend
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        /* Log some messages using Zend_Log_Writer_Firebug */

        $logger = Zend_Registry::get('logger');

        $logger->log('Emergency: system is unusable',            Zend_Log::EMERG);
        $logger->log('Alert: action must be taken immediately',  Zend_Log::ALERT);
        $logger->log('Critical: critical conditions',            Zend_Log::CRIT);
        $logger->log('Error: error conditions',                  Zend_Log::ERR);
        $logger->log('Warning: warning conditions',              Zend_Log::WARN);
        $logger->log('Notice: normal but significant condition', Zend_Log::NOTICE);
        $logger->log('Informational: informational messages',    Zend_Log::INFO);
        $logger->log('Debug: debug messages',                    Zend_Log::DEBUG);
        $logger->log(array('$_SERVER',$_SERVER),                 Zend_Log::DEBUG);
        
        $logger->trace('Trace to here');
        
        $table = array('Summary line for the table',
                       array(
                           array('Column 1', 'Column 2'),
                           array('Row 1 c 1',' Row 1 c 2'),
                           array('Row 2 c 1',' Row 2 c 2')
                       )
                      );
        $logger->table($table);
        
        
        /* Log some messages using Zend_Db_Profiler_Firebug */

        $db = Zend_Registry::get('db');

        $db->getConnection()->exec('CREATE TABLE foo (
                                      id      INTEGNER NOT NULL,
                                      col1    VARCHAR(10) NOT NULL  
                                    )');

        $db->insert('foo', array('id'=>1,'col1'=>'original'));

        $db->fetchAll('SELECT * FROM foo WHERE id = ?', 1);

        $db->update('foo', array('col1'=>'new'), 'id = 1');

        $db->fetchAll('SELECT * FROM foo WHERE id = ?', 1);

        $db->delete('foo', 'id = 1');

        $db->getConnection()->exec('DROP TABLE foo');        

        
        /* Throw an exception to test the default error handler. */
       
        throw new Zend_Exception('Test Exception');
    }
}

