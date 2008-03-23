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
 * @package    Zend_Console
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * Include the console files we need
 *
 * @see Zend_Console_Exception
 */
require_once 'Zend/Console/Exception.php';

/**
 * Include the console files we need
 *
 * @see Zend_Console_ErrorCodes
 */
require_once 'Zend/Console/ErrorCodes.php';

/**
 * Include the build files we need
 *
 * @see Zend_Build_Factory
 */
require_once 'Zend/Build/Factory.php';

/**
 * @category   Zend
 * @package    Zend_Console
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Console_Factory
{
    /**
     * @constant string
     */
    const MF_TASK_SECTION_NAME         = 'tasks';

    /**
     * @constant string
     */
    const MF_RESOURCE_SECTION_NAME     = 'resources';

    /**
     * @constant string
     */
    const ZEND_DIR                     = 'Zend';

    /**
     * @constant string
     */
    const BUILD_DIR                    = 'Build';

    /**
     * @constant string
     */
    const MANIFEST_FILE_WILDCARD       = '**/zf-manifest.*';

    /**
     * @constant string
     */
    const ZEND_CONFIG_PACKAGE          = 'Zend_Config_';

    /**
     * @constant string
     */
    const REGEX_MATCH                  = "ereg('%s', '%s')";

    /**
     * @constant string
     */
    const EXACT_MATCH                  = "!strcmp('%s', '%s')";

    /**
     * makeConsole
     *
     * @param  array $argv
     * @return mixed
     */
    public static function makeConsole($argv)
    {
        /**
         * @see Zend_Console
         */
        require_once 'Zend/Console.php';
        $zc = new Zend_Console();
        return $zc->init($argv);
    }
    
    /**
     * Returns the correct Zend_Build_Task to execute given the command line string
     * 
     * @param  array $argv
     * @return mixed
     */
    public static function makeConsoleAction($argv)
    {
        // @todo pass arguments as a config object instead of $argv
        $task = Zend_Build_Factory::makeTask(array_shift($argv));
        $project = Zend_Build_Factory::makeProject();
        return $task->init($argv, $project);
    }

    /**
     * makeConsoleResource
     *
     * @param  array $argv
     * @return mixed
     */
    public static function makeConsoleResource($argv)
    {
        // @todo pass arguments as a config object instead of $argv
        $resource = Zend_Build_Factory::makeResource(array_shift($argv));
        $project = Zend_Build_Factory::makeProject();
        return $resource->init($argv, $project);
    }
}