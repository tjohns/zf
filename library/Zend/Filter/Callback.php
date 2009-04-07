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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Callback implements Zend_Filter_Interface
{
    /**
     * Callback in a call_user_func format
     *
     * @var string|array
     */
    protected $_callback = null;

    /**
     * Constructor
     *
     * @param string|array $callback Callback in a call_user_func format
     */
    public function __construct($callback)
    {
        $this->_callback = $callback;
    }

    /**
     * (non-PHPdoc)
     * @see library/Zend/Filter/Zend_Filter_Interface#filter()
     */
    public function filter($value)
    {
        return call_user_func($this->_callback, $value);
    }
}