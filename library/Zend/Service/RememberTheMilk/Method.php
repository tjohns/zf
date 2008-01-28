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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk_Method
{
    /**
     * Associative array for referring equivalents of requiredperms values
     *
     * @todo Confirm the values of
     *       Zend_Service_RememberTheMilk_Method::$_permsRef are correct
     * @var array
     */
    protected static $_permsRef = array(
        '1' => Zend_Service_RememberTheMilk::PERMS_READ,
        '2' => Zend_Service_RememberTheMilk::PERMS_WRITE,
        '3' => Zend_Service_RememberTheMilk::PERMS_DELETE
    );

    /**
     * Name of the method
     *
     * @var string
     */
    protected $_name;

    /**
     * Whether or not the method requires authentication
     *
     * @var bool
     */
    protected $_needsLogin;

    /**
     * Whether or not calls to the method must be signed
     *
     * @var bool
     */
    protected $_needsSigning;

    /**
     * Description of the method
     *
     * @var string
     */
    protected $_description;

    /**
     * Example response for the method
     *
     * @var string
     */
    protected $_response;

    /**
     * Required permission level to execute the method
     *
     * @var string
     */
    protected $_requiredPerms;

    /**
     * List of arguments taken by the method
     *
     * @var Zend_Service_RememberTheMilk_ArgumentList
     */
    protected $_arguments;

    /**
     * List of errors that can be returned by calls to the method
     *
     * @var Zend_Service_RememberTheMilk_ErrorList
     */
    protected $_errors;

    /**
     * Constructor to initialize the object with data
     *
     * @todo Implement Method::__construct()
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $data = $data->method;
        $this->_name = $data->name;
        $this->_needsLogin = ($data->needslogin == '1') ? true : false;
        $this->_needsSigning = ($data->needssigning == '1') ? true : false;
        $this->_requiredPerms = self::$_permsRef[$data->requiredperms];
        $this->_description = $data->description;
        $this->_response = $data->response;
        $this->_arguments = new Zend_Service_RememberTheMilk_ArgumentList($data);
        $this->_errors = new Zend_Service_RememberTheMilk_ErrorList($data);
    }

    /**
     * Returns the name of the method.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns whether or not the method requires authentication.
     *
     * @return bool TRUE if the method requires authentication, FALSE
     *              otherwise
     */
    public function needsLogin()
    {
        return $this->_needsLogin;
    }

    /**
     * Returns whether or not calls to the method must be signed.
     *
     * @return bool TRUE if method calls must be signed, FALSE otherwise
     */
    public function needsSigning()
    {
        return $this->_needsSigning;
    }

    /**
     * Returns the permission level required to make calls to the method.
     *
     * @see Zend_Service_RememberTheMilk::PERMS_READ
     * @see Zend_Service_RememberTheMilk::PERMS_WRITE
     * @see Zend_Service_RememberTheMilk::PERMS_DELETE
     * @return string
     */
    public function requiredPerms()
    {
        return $this->_requiredPerms;
    }

    /**
     * Returns a description of the method.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Returns an example response for the method.
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Returns a list of arguments taken by the method.
     *
     * @return Zend_Service_RememberTheMilk_ArgumentList
     */
    public function getArguments()
    {
        return $this->_arguments;
    }

    /**
     * Returns a list of errors that can be returned by calls to the method.
     *
     * @return Zend_Service_RememberTheMilk_ErrorList
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
