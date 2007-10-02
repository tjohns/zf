<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class representing container for placeholder values
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_View_Helper_Placeholder_Container_Abstract extends ArrayObject
{
    /**
     * Whether or not to override all contents of placeholder
     * @const string
     */
    const SET    = 'set';

    /**
     * Whether or not to append contents to placeholder
     * @const string
     */
    const APPEND = 'append';

    /**
     * What text to prefix the placeholder with when rendering
     * @var string
     */
    protected $_prefix    = '';

    /**
     * What text to append the placeholder with when rendering
     * @var string
     */
    protected $_postfix   = '';

    /**
     * What string to use between individual items in the placeholder when rendering
     * @var string
     */
    protected $_separator = '';

    /**
     * Whether or not we're already capturing for this given container
     * @var bool
     */
    protected $_captureLock = false;

    /**
     * What type of capture (overwrite (set), append) to use
     * @var string
     */
    protected $_captureType;

    /**
     * Set a single value
     *
     * @param  mixed $value
     * @return void
     */
    public function set($value)
    {
        $this->exchangeArray(array($value));
    }

    /**
     * Retrieve container value
     *
     * If single element registered, returns that element; otherwise,
     * serializes to array.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (1 == count($this)) {
            return $this[0];
        }

        return $this->getArrayCopy();
    }

    /**
     * Set prefix for __toString() serialization
     *
     * @param  string $prefix
     * @return Zend_View_Helper_Placeholder_Container
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = (string) $prefix;
        return $this;
    }

    /**
     * Retrieve prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Set postfix for __toString() serialization
     *
     * @param  string $postfix
     * @return Zend_View_Helper_Placeholder_Container
     */
    public function setPostfix($postfix)
    {
        $this->_postfix = (string) $postfix;
        return $this;
    }

    /**
     * Retrieve postfix
     *
     * @return string
     */
    public function getPostfix()
    {
        return $this->_postfix;
    }

    /**
     * Set separator for __toString() serialization
     *
     * Used to implode elements in container
     *
     * @param  string $separator
     * @return Zend_View_Helper_Placeholder_Container
     */
    public function setSeparator($separator)
    {
        $this->_separator = (string) $separator;
        return $this;
    }

    /**
     * Retrieve separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Start capturing content to push into placeholder
     *
     * @param  int $type How to capture content into placeholder; append or set
     * @return void
     * @throws Zend_View_Helper_Placeholder_Exception if nested captures detected
     */
    public function captureStart($type = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        if ($this->_captureLock) {
            throw new Zend_View_Helper_Placeholder_Exception('Cannot nest placeholder captures for the same placeholder');
        }

        $this->_captureLock = true;
        $this->_captureType = $type;
        ob_start();
    }

    /**
     * End content capture
     *
     * @return void
     */
    public function captureEnd()
    {
        $data = ob_get_clean();
        $this->_captureLock = false;
        switch ($this->_captureType) {
            case self::SET:
                $this->exchangeArray(array($data));
                break;
            case self::APPEND:
            default:
                $this[] = $data;
                break;
        }
    }

    /**
     * Render the placeholder
     *
     * @return string
     */
    public function toString()
    {
        $items  = $this->getArrayCopy();
        $return = $this->getPrefix()
                . implode($this->getSeparator(), $items)
                . $this->getPostfix();
        return $return;
    }

    /**
     * Serialize object to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
