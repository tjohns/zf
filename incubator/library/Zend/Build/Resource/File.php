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
 * @package    Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @see Zend_Build_Resource_AbstractFilesystemResource
 */
require_once 'Zend/Build/Resource/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Resource_File implements Zend_Build_Resource_Interface
{
	const DEFAULT_BUILD_FILE_NAME = 'build.zf';
	private $_contents = null;
	
	public function __construct (string $name, Zend_Build_Resource $parent)
    {
        $_name = $name;
        $_parent = $parent;
    }

    /**
     * @see Zend_Build_Resource_Interface
     */
    public function exists ()
    {
        return (file_exists($this->getPath()));
    }

    /**
     * Creates this instance of the resource in a project
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function create (string $content = '')
    {
        if ($this->exists()) {
            throw new Zend_Build_Exception("File already exists: $this->getPath()");
        }
        if (! $handle = fopen($this->getPath(), 'w')) {
            throw new Zend_Build_Exception("Cannot create file: $this->getPath()");
        }
        // Write $somecontent to our opened file.        if (! fwrite($handle, $content)) {
            throw new Zend_Build_Exception("Cannot write to file: $this->getPath()");
        }
        fclose($handle);
    }

    /**
     * Deletes this instance of this resource in a project
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function delete ();
    
    /**
     * Reads this file into a string and returns it.
     */
    protected function read()
    {
    	$fileName = getPath();
        if (!is_set($fileName) || $$fileName == '') {
        	$name = DEFAULT_BUILD_FILE_NAME;
        } else {
        	trim($fileName);
        }
        
        // Read contents and store them
        if(!($contents = file_read_contents($fileName))) {
        	throw new Zend_Build_Exception("File '$fileName' could not be read.");
        }
    }

    /**
     * Returns the full path of this filesystem resource relative to the project root
     */
    public function getPath ()
    {
        if ($_parent != null) {
            return join($_parent->path, $this->_name, '');
        }
        return $this->_name;
    }
}