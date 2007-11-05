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
    const SET    = 'SET';

    /**
     * Whether or not to append contents to placeholder
     * @const string
     */
    const APPEND = 'APPEND';

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
     * What string to use as the indentation of output, this will typically be spaces. Eg: '    '
     * @var string
     */
    protected $_indent = '';
    
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
     * The protected member of container items, available to all subclasses
     * @var array
     */
    protected $_items = array();
    
    /**
     * Constructor - This is needed so that we can attach a class member as the ArrayObject container
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct($this->_items, parent::ARRAY_AS_PROPS);
    }
    
    /**
     * Set a single value
     *
     * @param  mixed $value
     * @return void
     */
    public function set($value)
    {
        $this->_items = array($value);
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
     * Set the indentation string for __toString() serialization,
     * optionally, if a number is passed, it will be the number of spaces
     *
     * @param string|int $indent
     * @return Zend_View_Helper_Placeholder_Container
     */
    public function setIndent($indent)
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }
        
        $this->_indent = (string) $indent;
        return $this;
    }

    /**
     * Retrieve indentation
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->_indent;
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
            require_once 'Zend/View/Helper/Placeholder/Container/Exception.php';
            throw new Zend_View_Helper_Placeholder_Container_Exception('Cannot nest placeholder captures for the same placeholder');
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
                $this->_items = array($data);
                break;
            case self::APPEND:
            default:
                $this->_items[] = $data;
                break;
        }
    }

    /**
     * Next Index
     *
     * as defined by the PHP manual
     * @return int
     */
    public function nextIndex()
    {
        return $nextIndex = max(array_keys($this->_items)) + 1;
    }
    
    /**
     * Render the placeholder
     *
     * @return string
     */
    public function toString($indent = null)
    {
        $indent = ($indent != null) ? $indent : $this->_indent;
        $indent = (is_int($indent)) ? str_repeat(' ', $indent) : $indent;
        
        $items  = $this->getArrayCopy();
        $return = $indent 
                . $this->getPrefix()
                . implode($this->getSeparator(), $items)
                . $this->getPostfix();
        $return = str_replace("\n", "\n{$indent}", $return);
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
