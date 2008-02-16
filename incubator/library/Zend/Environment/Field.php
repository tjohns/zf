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
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_Environment_Exception
 */
require_once 'Zend/Environment/Exception.php';


/**
 * Zend_Environment_Container_Abstract
 */
require_once 'Zend/Environment/Container/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Environment_Field extends Zend_Environment_Container_Abstract
{
    /**
     * Constants to map to version_compare
     */
    const VERSION_MIN = '>=';
    const VERSION_MAX = '<=';
    const VERSION_EXACT = '==';
    
    /**
     * Default properties for a field
     *
     * @var array
     */
    protected $_data = array('name' =>    null,
                             'title' =>   null,
                             'value' =>   null,
                             'version' => null,
                             'info' =>    null);
    
    /**
     * An array of default values can be passed to this component
     *
     * @param  array $params
     * @return void
     */
    public function __construct($params = null)
    {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * Provides base method for checking behaviour of individual fields within
     * a section. Modules such as 'Security' may subclass this to provide more
     * relevant results.
     *
     * @return boolean
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Returns the result of a comparison with a section module and a version
     * number
     *
     * Provides a wrapper of the version_compare php native function, but will
     * allow full-string comparisons against verbose module names. By default
     * will return true if the module version number is equal or greater than
     * the version number provided to this function.
     *
     * If the fail parameter is set to true, an exception will be thrown for
     * failed comparisons
     *
     * A compare constant can be used to provide matching for minimum versions
     * (VERSION_MIN), maximum versions (VERSION_MAX) and identical matches
     * (VERSION_EXACT).
     *
     * @param string $required
     * @param boolean $fail
     * @param string $compare
     * @return boolean|null
     * @throws Zend_Environment_Exception
     */
	public function isVersion($required, $fail = false, $compare = null)
	{
	    if ($this->version === null) {
	        return null;
	    }

        if (is_null($compare)) {
            $compare = self::VERSION_MIN;
        }

        if (!version_compare($this->version, $required, $compare)) {
            if ($fail) {
                throw new Zend_Environment_Exception("Version is not {$compare} {$required}");
            } else {
                return false;
            }
        }

        return true;
	}
}
