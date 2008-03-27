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
 * @subpackage Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * This is a very special class; it can be instantiated directly but is intended to be used as a template
 * for build scripts.
 * 
 * @category   Zend
 * @package    Zend_Build
 * @subpackage Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Build_Environment
{
    /**
     * @constant string
     */
    const CURR_DIR = './';

    /**
     * @constant string
     */
    const DEFAULT_BUILD_FILE_NAME = './build.zf';

    /**
     * @constant string
     */
    const PRE_EXECUTE_TASK  = 'pre';

    /**
     * @constant string
     */
    const POST_EXECUTE_TASK = 'post';

    /**
     * @var Zend_Build_Profile
     */
    private $_profile = null;

    /**
     * @var array
     */
    private $_executedTasks = array();

    /**
     * Constructor
     *
     * @param  Zend_Build_Profile $profile
     * @return void
     */
    public function __construct (Zend_Build_Profile $profile = null)
    {
        $this->_profile = $profile;
    }

    /**
     * zfBuildCall
     *
     * @param  string $target
     * @param  array  $args
     * @return void
     */
    public function zfBuildCall($target, array $args)
    {
        $this->zfBuildCall(PRE_EXECUTE_TASK);
        $this->_zfBuildCall($task, $args);
        $this->zfBuildCall(POST_EXECUTE_TASK);
    }

    /**
     * _zfBuildCall
     *
     * @param  string $task
     * @param  array  $args
     * @return void
     */
    private function _zfBuildCall($task, array $args)
    {
        if(_zfBuildCalled($task, $args)) return;
        _zfBuildPreCall($task, $args);
        $this->$task($args);
        _zfBuildPostCall($task, $args);
    }

    /**
     * _zfBuildPreCall
     *
     * @return void
     */
    protected function _zfBuildPreCall()
    {
        $dependencies = _zfBuildGetDependencies($task);
        foreach($dependencies as $dependency) {
            _zfBuildCall(_zfBuildParseNameFromCS($dependency), _zfBuildParseArgsFromCS($dependency));
        }
    }

    /**
     * _zfBuildPostCall
     *
     * @return void
     */
    protected function _zfBuildPostCall()
    {
    }

    /**
     * _zfBuildCalled
     *
     * @param  string $taskName
     * @param  array  $args
     * @return boolean
     */
    private function _zfBuildCalled($taskName, array $args)
    {
        $callStr = _zfBuildGetCallString($taskName, $args);

        // For debugging.
        if(in_array($callStr)) {
            print($callStr . " has already been called.\n");
        } else {
            print($callStr . " has not already been called.\n");
        }
        return in_array($callStr);
    }

    /**
     * _zfBuildGetDependencies
     *
     * @param  string $taskName
     * @param  array  $args
     * @return string
     */
    private function _zfBuildGetDependencies($taskName, array $args)
    {
        // For now we won't support arguments
    }

    /**
     * _zfBuildGetCallString
     *
     * @param  string $taskName
     * @param  array  $args
     * @return string
     */
    private function _zfBuildGetCallString($taskName, array $args)
    {
        return "$taskName(implode(',', array_map('trim', $args)))";
    }

    /* INSERT BUILD FILE HERE */
}