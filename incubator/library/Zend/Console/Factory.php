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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Build
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Include the console files we need
 */
require_once 'Zend/Console/Exception.php';
require_once 'Zend/Console/ErrorCodes.php';

/**
 * Include the build files we need
 */
require_once 'Zend/Build/Factory.php';

class Zend_Console_Factory
{
    const MF_TASK_SECTION_NAME         = 'tasks';
    const MF_RESOURCE_SECTION_NAME     = 'resources';
    const ZEND_DIR                     = 'Zend';
    const BUILD_DIR                    = 'Build';
    const MANIFEST_FILE_WILDCARD       = '**/zf-manifest.*';
    const ZEND_CONFIG_PACKAGE          = 'Zend_Config_';

    const REGEX_MATCH                  = "ereg('%s', '%s')";
    const EXACT_MATCH                  = "!strcmp('%s', '%s')";

    public static function makeConsole($argv)
    {
        require_once 'Zend/Console.php';
        $zc = new Zend_Console();
        return $zc->init($argv);
    }
    
	/**
	 * Returns the correct Zend_Build_Task to execute given the command line string
	 * 
	 * @param arracy $argv
	 */
	public static function makeConsoleAction($argv)
	{
	    // @todo pass arguments as a config object instead of $argv
        $task = Zend_Build_Factory::makeTask(array_shift($argv));
        $project = Zend_Build_Factory::makeProject();
        return $task->init($argv, $project);
	}
	
	public static function makeConsoleResource($argv)
	{
        // @todo pass arguments as a config object instead of $argv
        $resource = Zend_Build_Factory::makeResource(array_shift($argv));
        $project = Zend_Build_Factory::makeProject();
        return $resource->init($argv, $project);
	}
}
