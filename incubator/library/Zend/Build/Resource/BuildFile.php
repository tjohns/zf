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
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @see Zend_Build_Resource_File
 */
require_once 'Zend/Build/Resource/File.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @category   Zend
 * @package    Zend_Build
 * @subpackage Zend_Build_Resource
 * @uses       Zend_Build_Resource_File
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Resource_BuildFile implements Zend_Build_Resource_File
{
    const BUILD_ENV_CLASS = Zend_Build_Environment;
    const ENV_BUILD_FILE_PLACEHOLDER = '/* INSERT BUILD FILE HERE */';
	
    private $_buildFileName = null;
    private $_envFileName = null;
	 
    public function init($argv)
    {
        // @todo Use Zend_Log for output
        $_buildFileName = _getBuildFileName($argv);
        $_envFileName = Zend_Loader::getFileName(BUILD_ENV_CLASS);
    }
	
    /**
     * Build specified buildfile.
     */
    public function build(string $target, array $args)
    {
        // If there are any errors reading the env or build files, it might as well happen here.
        $buildFileContents = file_read_contents($envFileName);
        $envFileContents = file_read_contents($envFileName);

        // Create the environment for this build
        $processedEnv = $this->preProcessEnvTemplate($buildFileContents, $envFileContents);

		// Load build environment
		/**
         * The processedEnv string has been processed such that eval()
         * should return an accurate line number if an error is found
         * in the build script.
         */
		eval($processedEnv);
		
		// Now execute the build script
		$buildEnv = new BUILD_ENV_CLASS();
		$buildEnv->execute($target);
	}

	protected function _preProcessEnvTemplate(string $buildFileContents, $envFileContents)
	{
		$processed = '';
		
        /*
		 * First replace any newlines with whitespace in the Environment class file contents so that
		 * eval will return line numbers that correspond to the correct lines in the build file
		 * in the event of an error.
		 */
        $processed = str_replace('\r', ' ', str_replace('\n', ' ', $envFileContents));
        
        /*
         * Now insert the contents of the build file and return.
         */
        return str_replace(ENV_BUILD_FILE_PLACEHOLDER, $buildFileContents, $processed);
	}
	
	private function _getBuildFileName($argv)
	{
        // @todo Actually do the right thing here.
        return $argv[1];  
	}
}