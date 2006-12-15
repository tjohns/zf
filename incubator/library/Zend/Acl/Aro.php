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
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Acl_Aro_Interface
 */
require_once 'Zend/Acl/Aro/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Acl_Aro implements Zend_Acl_Aro_Interface
{
    /**
     * Unique id of ARO
     *
     * @var string
     */
    protected $_aroId;

    /**
     * Sets the ARO identifier
     *
     * @param  string $id
     * @return void
     */
    public function __construct($aroId)
    {
        $this->_aroId = (string) $aroId;
    }

    /**
     * Defined by Zend_Acl_Aro_Interface; returns the ARO identifier
     *
     * @return string
     */
    public function getAroId()
    {
        return $this->_aroId;
    }

}
