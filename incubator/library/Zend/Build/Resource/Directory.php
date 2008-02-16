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
require_once 'Zend/Build/Resource/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Resource_Directory extends Zend_Build_Resource_Abstract
{
    /**
     * @see Zend_Build_Resource_Interface
     */
    public function exists ()
    {
        $path = $this->getPath();
        return (file_exists($path) && is_dir($path));
    }

    /**
     * Creates this instance of the resource in a project
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function create ()
    {
        return NULL;
    }

    /**
     * Deletes this instance of this resource in a project
     *
     * @throws Zend_Build_Profile_Resource_Exception If authentication cannot be performed
     */
    public function delete ()
    {
        return NULL;
    }

    /**
     * Returns the full path of this filesystem resource relative to the project root
     */
    public function getPath ()
    {
        return join($_parent->path, $this->_name);
    }
}