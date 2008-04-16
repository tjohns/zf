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
 * @subpackage Zend_Build_Action
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @category   Zend
 * @package    Zend_Build
 * @subpackage Zend_Build_Action
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Build_Task_Interface
{
    /**
     * getName
     *
     * @return void
     */
    public function getName ();

    /**
     * Return short usage string
     *
     * @return void
     */
    public function getShortUsage ();

    /**
     * Return long usage string
     *
     * @return void
     */
    public function getLongUsage ();

    /**
     * Validate all attributes of this command.
     *
     * @return void
     */
    public function validate ();

    /**
     * Execute this command.
     *
     * @return void
     */
    public function execute ();

    /**
     * Set the profile to execute against.
     *
     * @param  mixed $profile
     * @return void
     */
    public function setProfile ($profile);

    /**
     * Get the profile that would be executed against.
     *
     * @return void
     */
    public function getProfile ();

    /**
     * Set the options to execute with. Options added later override previous options with the same name.
     *
     * @param  array $options
     * @return void
     */
    public function setOptions (array $options);

    /**
     * Get the options to execute with.
     *
     * @return void
     */
    public function getOptions ();

    /**
     * Set the resource to execute this command on.
     *
     * @param  array $resources
     * @return void
     */
    public function setResources (array $resources);

    /**
     * Get the resource to execute this command on.
     *
     * @return void
     */
    public function getResources ();
}