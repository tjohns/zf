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
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_View_Abstract
 */
require_once('Zend/Environment/Module/Abstract.php');


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Environment_Module_Os extends Zend_Environment_Module_Abstract
{
    protected $_type = 'os';
    
    protected function _init()
    {
        $this->os = new Zend_Environment_Field(array(
            'title' => 'OS',
            'info' => 'Host operating system',
            'value' => PHP_OS));

        $this->uid = new Zend_Environment_Field(array(
            'title' => 'Script uid',
            'info' => 'script user id',
            'value' => getmyuid()));

        $this->gid = new Zend_Environment_Field(array(
            'title' => 'Script gid',
            'info' => 'script group id',
            'value' => getmygid()));

        $this->script_username = new Zend_Environment_Field(array(
            'title' => 'Script username',
            'info' => 'username obtained via HTTP authentication',
            'value' => get_current_user()));

        $this->memory = new Zend_Environment_Field(array(
            'title' => 'Memory',
            'info' => 'Memory used by this script on host'));
        if (function_exists('memory_get_usage')) {
            $this->memory->value = memory_get_usage();
        } else {
            $this->memory->notice = 'memory_get_usage() not enabled';
        }
    }
}
