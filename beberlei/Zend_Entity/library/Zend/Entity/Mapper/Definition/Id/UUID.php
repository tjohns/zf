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
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

class Zend_Entity_Mapper_Definition_Id_UUID implements Zend_Entity_Mapper_Definition_Id_Interface
{
    /**
     * @var string
     */
    protected $_prefix = '';

    /**
     * @var boolean
     */
    protected $_moreEntropy = false;

    /**
     * @var string
     */
    protected $_lastSequenceId = null;

    /**
     * @param string $prefix
     * @param boolean $moreEntropy
     */
    public function __construct($prefix='', $moreEntropy=false)
    {
        $this->_prefix = $prefix;
        $this->_moreEntropy = $moreEntropy;
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return int|null Sequence Number or null if none generated
     */
    public function nextSequenceId(Zend_Db_Adapter_Abstract $db)
    {
        $this->_lastSequenceId = uniqid($this->_prefix, $this->_moreEntropy);
        return $this->_lastSequenceId;
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return int|null Sequence Number or null if none generated
     */
    public function lastSequenceId(Zend_Db_Adapter_Abstract $db)
    {
        return $this->_lastSequenceId;
    }
}