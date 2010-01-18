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
 * @package    Zend_Doctrine2
 * @subpackage Log
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Doctrine 2 and Firebug Profiler
 *
 * @uses       Zend_Db_Profiler_Firebug
 * @uses       \Doctrine\DBAL\Logging\SqlLogger
 * @category   Zend
 * @package    Zend_Doctrine2
 * @subpackage Log
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Doctrine2_FirebugProfiler extends Zend_Db_Profiler_Firebug
    implements \Doctrine\DBAL\Logging\SqlLogger
{
    /**
     * @param string $sql
     * @param array $params
     */
    public function logSql($sql, array $params = null)
    {
        $queryId = $this->queryStart($sql);
        $this->getQueryProfile($queryId)->bindParams($params);
        $this->queryEnd($queryId);
    }
}