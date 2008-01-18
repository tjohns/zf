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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Build_Factory
{
    const MF_ACTION_SECTION_NAME       = 'actions';
    const MF_RESOURCE_SECTION_NAME     = 'resources';
    const ZEND_DIR                     = 'Zend';
    const BUILD_DIR                    = 'Build';
    const MANIFEST_FILE_WILDCARD       = '**/zf-manifest.*';
    const ZEND_CONFIG_PACKAGE          = 'Zend_Config_';

    const REGEX_MATCH                  = "ereg('%s', '%s')";
    const EXACT_MATCH                  = "!strcmp('%s', '%s')";
    
    const DEFAULT_PROFILE_PATH         = './';
    const DEFAULT_PROFILE_NAME         = 'zf-project.xml';
    
    /**
     * Returns the correct project object for the specified project profile
     * 
     * @param string $profileFilePath
     * @param string $profileFileName
     */
    public static function makeProject($profileFilePath = self::DEFAULT_PROFILE_PATH, $profileFileName = self::DEFAULT_PROFILE_NAME)
    {
        $xml = file_get_contents($profileFilePath . $profileFileName);
        return Zend_Build_XmlConvertor::xmlToResource($xml); 
    }

    /**
     * Returns the correct action as specified by the manifest files
     * 
     * @param string $name
     */
    public static function makeAction($name)
    {	
        $actionClasses = self::loadMFClasses(self::MF_ACTION_SECTION_NAME, $name);
        if (empty($actionClasses)) {
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception(
                "Action '$actionStr' not found."
            );
        }
        
        $actionClass = $actionClasses[0];
        if (!($action = self::_instantiateClass($actionClass)) || !($action instanceof Zend_Build_action_Interface)) {
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception(
                "Invalid action class: '$actionClass'."
            );
        }

        return $action;
    }

    /**
     * Returns the correct resource as specified by the manifest files
     * 
     * @param string $name
     */
    public static function makeResource($name)
    {
        $resourceClasses = self::loadMFClasses(self::MF_RESOURCE_SECTION_NAME, $name);
        if (empty($resourceClasses)) {
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception(
                "Resource '$resourceStr' not found."
            );
        }
        
        $resourceClass = $resourceClasses[0];
        if (!($resource = _instantiateClass($resourceClass)) || !($resource instanceof Zend_Build_Resource_Interface)) {
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception(
                "Invalid resource class: '$resourceClass'."
            );
        }

        return $resource;
    }
	
    private static function _instantiateClass($className)
    {
        try
        {
            $object = new $className();
        } catch (Exception $e) {
            return false;
        }
	   
        return $object;
    }

    private static function loadAllMFClasses($sectionName)
    {
        return getClassNames($sectionName, '.*', self::REGEX_MATCH);
    }
    
    /**
     * Loads all classes referred to in the section specified by $sectionName and returns
     * the class names of all loaded classes.
     * 
     * @return array Class names of the loaded classes. Empty array if no classes loaded.
     */
	public static function loadMFClasses($sectionName, $testStr, $testType = self::EXACT_MATCH)
	{
        $loadedClasses = array();
        
		// First get the include path
		$includePathArray = explode(PATH_SEPARATOR, get_include_path());
		foreach ($includePathArray as $path) {
			// This might be the library directory
			$libDir = $path;
			$zendDir = $libDir . DIRECTORY_SEPARATOR . self::ZEND_DIR;
			$zendBuildDir = $zendDir . DIRECTORY_SEPARATOR . self::BUILD_DIR;
			
			$manifestFiles = array();
			
			// Now add the manifest files in Zend_Build
            $manifestFiles += self::_findManifestFiles($zendBuildDir);
			
			// Now add the manifest files in Zend
			$manifestFiles += self::_findManifestFiles($zendDir);
			
			// Finally add any other manifest files in this directory
			$manifestFiles += self::_findManifestFiles($libDir);
			
			foreach($manifestFiles as $manifest)
			{
				// Figure out which config class to use and load it
				$extension = substr(strrchr($manifest, "."), 1);
				$configClass = self::ZEND_CONFIG_PACKAGE . ucfirst(strtolower($extension));
				try
				{
				    require_once 'Zend/Loader.php';
					Zend_Loader::loadClass($configClass, explode(PATH_SEPARATOR, get_include_path()));
				}
				catch(Zend_Exception $e)
				{
				    // Problem with loading the class, continue
					continue;
				}
				
				$config = new $configClass($manifest, $sectionName);
				
				foreach($config as $key => $value) {
				    if(self::_testStr($testType, $testStr, $key) && !isset($classes[$key])) {
				        try
                        {
                            Zend_Loader::loadClass($value);
                            $loadedClasses[$key] = $value;
                        }
                        catch(Zend_Exception $e)
                        {
                            print("problem\n");
                            // Problem with loading the class, continue
                            continue;
                        }
				    }
				}
			}
		}
		return $loadedClasses;
	}
	
	private static function _findManifestFiles($searchDir)
	{
		if(!is_dir($searchDir))
		{
		   return array();
		}
		
		$foundFiles = glob($searchDir . DIRECTORY_SEPARATOR . self::MANIFEST_FILE_WILDCARD);
		
		// Key by the file name so that array merging will work
		if(!empty($foundFiles)) {
            $foundFiles = array_combine(array_values($foundFiles), array_values($foundFiles));
		}

		return (isset($foundFiles)) ? $foundFiles : array();
	}
	
	private static function _testStr($testType, $testStr, $str)
	{
	   $test = sprintf('return ' . $testType, $testStr, $str) . ';';
	   return eval($test);
	}
}
