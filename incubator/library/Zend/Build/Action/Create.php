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
 * @package    Zend_Build_Task
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * Include Resource files
 */
require_once 'Zend/Build/Resource/Interface.php';

/**
 * Include Task files
 */
require_once 'Zend/Build/Action/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Build_Task
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Action_Create extends Zend_Build_Action_Abstract
{   
    public function configure(Zend_Config $config);
    public function validate(Project $projectProfile, array $resources);
    public function execute(Project $projectProfile, array $resources);
}