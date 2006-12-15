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
 * Zend_Acl
 */
require_once 'Zend/Acl.php';


/**
 * Zend_Acl_Aro_Interface
 */
require_once 'Zend/Acl/Aro/Interface.php';


/**
 * Zend_Acl_Aco_Interface
 */
require_once 'Zend/Acl/Aco/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Acl_Assert_Interface
{
    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, ARO, ACO, and privilege to which the authorization query applies. If the
     * $aro, $aco, or $privilege parameters are null, it means that the query applies to all AROs, ACOs, or
     * privileges, respectively.
     *
     * @param  Zend_Acl               $acl
     * @param  Zend_Acl_Aro_Interface $aro
     * @param  Zend_Acl_Aco_Interface $aco
     * @param  string                 $privilege
     * @return boolean
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Aro_Interface $aro = null, Zend_Acl_Aco_Interface $aco = null,
                           $privilege = null);
}
