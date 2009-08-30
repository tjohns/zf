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
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Sequence Id Generator
 *
 * @uses       Zend_Entity_Definition_Interface
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Definition_Id_Sequence implements Zend_Entity_Definition_Id_Interface
{
    protected $_sequenceName = null;

    /**
     * @param string $sequenceName
     */
    public function __construct($sequenceName)
    {
        $this->_sequenceName = $sequenceName;
    }

    /**
     * @return bool
     */
    public function isPrePersistGenerator()
    {
        return true;
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return int|null Sequence Number or null if none generated
     */
    public function nextSequenceId(Zend_Db_Adapter_Abstract $db)
    {
        return $db->nextSequenceId($this->_sequenceName);
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return int|null Sequence Number or null if none generated
     */
    public function lastSequenceId(Zend_Db_Adapter_Abstract $db)
    {
        return null;
    }
}