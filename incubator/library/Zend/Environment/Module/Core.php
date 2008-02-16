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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_View_Abstract
 */
require_once('Zend/Environment/Module/Abstract.php');


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Environment_Module_Core extends Zend_Environment_Module_Abstract
{
    protected $_type = 'core';
    
    protected function _init()
    {
        $this->os = new Zend_Environment_Field(array(
            'title' => 'PHP',
            'info' => 'PHP Version',
            'value' => PHP_VERSION));

        $this->zf = new Zend_Environment_Field(array(
            'title' => 'Zend Framework',
            'info' => 'versions follow PHP version conventions (see http://www.php.net/version_compare)',
            'value' => Zend_Version::VERSION));
    }
}
