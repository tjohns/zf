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
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk_Argument
{
    /**
     * Name of the argument
     *
     * @var string
     */
    protected $_name;

    /**
     * Whether or not the argument is optional
     *
     * @var bool
     */
    protected $_optional;

    /**
     * Description of the argument
     *
     * @var string
     */
    protected $_description;

    /**
     * Constructor to initialize the object with data
     *
     * @todo Check parsing for description in Argument::__construct()
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_name = $data->name;
        $this->_option = ($data->optional == '1') ? true : false;
        $data = (array) $data;
        $this->_description = $data['$t'];
    }

    /**
     * Returns the name of the argument.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns whether or not the argument is optional.
     *
     * @return bool TRUE if the argument is optional, FALSE otherwise
     */
    public function isOptional()
    {
        return $this->_optional;
    }

    /**
     * Returns the description of the argument.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
}
