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

/*
 * This is a very special class; it can be instantiated directly but is intended to be used as a template
 * for build scripts.
 */
abstract class Zend_Build_Environment
{
	const CURR_DIR = './'
	const DEFAULT_BUILD_FILE_NAME = CURR_DIR . 'build.zf';
	const PRE_EXECUTE_TASK = 'pre';
	const POST_EXECUTE_TASK = 'post';
	
	private $_profile = null;
	private $_executedTasks = array();
	
    public function __construct (Zend_Build_Profile $profile = null)
    {
        $_profile = $profile;
    }
    
    public function zfBuildCall($target, array $args)
    {
        $this->zfBuildCall(PRE_EXECUTE_TASK);
        $this->_zfBuildCall($task, $args);
        $this->zfBuildCall(POST_EXECUTE_TASK)
    }
    
    private function _zfBuildcall($task, array $args)
    {
        if(_zfBuildCalled($task, $args)) return;
        _zfBuildPreCall($task, $args);
        $this->$task($args);
        _zfBuildPostCall($task, $args);
    }
    
    protected function _zfBuildPreCall()
    {
        $dependencies = _zfBuildGetDependencies($task);
        foreach($dependencies as $dependency) {
            _zfBuildCall(_zfBuildParseNameFromCS($dependency), _zfBuildParseArgsFromCS($dependency));
        }
    }
    
    protected function _zfBuildPostCall()
    {
        
    }
    
    private function _zfBuildCalled(string $taskName, array $args)
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
    
    private function _zfBuildGetDependencies(string $taskName, array $args)
    {
    	// For now we won't support arguments
    }
    
    private function _zfBuildGetCallString(string $taskName, array $args)
    {
        return "$taskName(implode(',', array_map('trim', $args)))";
    }

    /* INSERT BUILD FILE HERE */
    
}