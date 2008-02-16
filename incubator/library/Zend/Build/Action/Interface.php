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
 * @package    Zend_Build_Task
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @category   Zend
 * @package    Zend_Build_Command
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Build_Task_Interface
{
    public function getName ();

    /**
     * Return short usage string
     */
    public function getShortUsage ();

    /**
     * Return long usage string
     */
    public function getLongUsage ();

    /**
     * Validate all attributes of this command.
     */
    public function validate ();

    /**
     * Execute this command.
     */
    public function execute ();

    /**
     * Set the profile to execute against.
     */
    public function setProfile ($profile);

    /**
     * Get the profile that would be executed against.
     */
    public function getProfile ();

    /**
     * Set the options to execute with. Options added later override previous options with the same name.
     */
    public function setOptions (array $options);

    /**
     * Get the options to execute with.
     */
    public function getOptions ();

    /**
     * Set the resource to execute this command on.
     */
    public function setResources (array $resources);

    /**
     * Get the resource to execute this command on.
     */
    public function getResources ();
}