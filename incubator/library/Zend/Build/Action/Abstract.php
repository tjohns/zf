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
 * @package    Zend_Build_Action
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @see Zend_Build_Resource_Interface
 */
require_once 'Zend/Build/Resource/Interface.php';

/**
 * Include Action files
 */
require_once 'Zend/Build/Action/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Build_Action
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Build_Action_Abstract implements Zend_Build_Action_Interface
{
    protected $_name;
    protected $_short_usage;
    protected $_long_usage;

    /**
     * Project profile to run this command in. 
     * @var string
     */
    protected $_profile;
    
    /**
     * Task options. These appear after the command but before the object on the command line.
     * @var array
     */

    protected $_options;
    /**
     * Array of Zend_Build_Resources to run the command against.
     * @var array
     */
    
    protected $_resources;
    
    /**
     * Return name of the command that appears on the command line.
     * @return string
     */
    public function getName ()
    {
        return $_name;
    }
    
    /**
     * Return short usage string
     * @return string
     */
    public function getShortUsage ()
    {
        return $_short_usage;
    }

    /** 
     * Return long usage string
     */
    public function getLongUsage ()
    {
        return $_long_usage;
    }

    /**
     * Get the profile that would be executed against.
     */
    public function getProfile ()
    {
        return $_profile;
    }

    /**
     * Set the profile to execute against.
     */
    public function setProfile ($profile)
    {
        $_profile = $profile;
    }

    /**
     * Get the options to execute with.
     */
    public function getOptions ()
    {
        return $_options;
    }

    /**
     * Set the options to execute with. Options added later override previous options with the same name.
     */
    public function setOptions (array $options)
    {
        $_options = $options;
    }

    /**
     * Get the resources to execute this command on.
     */
    public function getResources ()
    {
        return $_resouces;
    }

    /**
     * Set the resources to execute this command on.
     */
    public function setResources (array $resources)
    {
        $_resources = $resources;
    }
    
	/**
     * Default implementation of execute(). Should work or offer reuse for many commands.
     */
    public function execute ()
    {
        // Delegate execution to each resource
        $_resources[0]->$this->_name();
    }
    
	/**
     * Default implementation of execute(). Should work or offer reuse for many commands.
     */
    public function validate ()
    {
        if ($_resources == null)
            return false;
        return true;
    }

    /**
     * Return string representation (which will also be a valid CLI command) of this command.
     */
    public function __toString ()
    {
        $str = $this->getName() . ' ';
        foreach ($_options as $option) {
            $str .= $_option . toString() . next($_options) ? ' ' : '';
        }
        foreach ($_resources as $resource) {
            $str .= $_resource;
        }
    }
}