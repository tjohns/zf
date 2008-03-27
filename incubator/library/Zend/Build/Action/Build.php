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
 * @package    Zend_Build
 * @subpackage Zend_Build_Action
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * Include Resource files
 */

/**
 * @see Zend_Build_Resource_Interface
 */
require_once 'Zend/Build/Resource/Interface.php';

/**
 * @see Zend_Build_Resource_Abstract
 */
require_once 'Zend/Build/Resource/Abstract.php';

/**
 * Include the Console files
 * @see Zend_Console_Context_Interface
 */
require_once 'Zend_Console_Context_Interface';

/**
 * Include Task files
 * @see Zend_Build_Task_Abstract
 */
require_once 'Zend/Build/Task/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Build
 * @subpackage Zend_Build_Action
 * @uses       Zend_Build_Task_Abstract
 * @uses       Zend_Console_Context_Interface
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Command_Create extends Zend_Build_Task_Abstract implements Zend_Console_Context_Interface
{
    /**
     * configure
     *
     * @param  Zend_Config $config
     * @return void
     */
    public function configure(Zend_Config $config);

    /**
     * validate
     *
     * @param  Project $projectProfile
     * @param  array   $resources
     * @return void
     */
    public function validate(Project $projectProfile, array $resources);

    /**
     * execute
     *
     * @param  Project $projectProfile
     * @param  array   $resources
     * @return void
     */
    public function execute(Project $projectProfile, array $resources);
}