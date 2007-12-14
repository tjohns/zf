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
 * @version    $Id: Placeholder.php 7078 2007-12-11 14:29:33Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Helper for setting and retrieving stylesheets
 *
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_HeadStyle extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Zend_View_Helper_HeadStyle';

    /**
     * Allowed optional attributes
     * @var array
     */
    protected $_optionalAttributes = array('lang', 'title', 'media', 'dir');

    /**
     * Allowed media types
     * @var array
     */
    protected $_mediaTypes = array(
        'all', 'aural', 'braille', 'handheld', 'print', 
        'projection', 'screen', 'tty', 'tv'
    );

    /**
     * Capture type and/or attributes (used for hinting during capture)
     * @var string
     */
    protected $_captureAttrs = null;

    /**
     * Whether or not to format scripts using CDATA; used only if doctype 
     * helper is not accessible
     * @var bool
     */
    public $useCdata = false;

    /**
     * Constructor
     *
     * Set separator to PHP_EOL.
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSeparator(PHP_EOL);
    }
    
    /**
     * Return headStyle object
     *
     * Returns headStyle helper object; optionally, allows specifying 
     *
     * @param  string $content Stylesheet contents
     * @param  string $placement Append, prepend, or set
     * @param  string|array $attributes Optional attributes to utilize
     * @return Zend_View_Helper_HeadStyle
     */
    public function headStyle($content = null, $placement = 'APPEND', $attributes = array())
    {
        if ((null !== $content) && is_string($content)) {
            switch (strtoupper($placement)) {
                case 'SET':
                    $action = 'setStyle';
                    break;
                case 'PREPEND':
                    $action = 'prependStyle';
                    break;
                case 'APPEND':
                default:
                    $action = 'appendStyle';
                    break;
            }
            $this->$action($content, $attributes);
        }

        return $this;
    }

    /**
     * Overload method calls
     *
     * Allows the following method calls:
     * - appendStyle($content, $attributes = array(), $indent = null)
     * - prependStyle($content, $attributes = array(), $indent = null)
     * - setStyle($content, $attributes = array(), $indent = null)
     * 
     * @param  string $method 
     * @param  array $args 
     * @return void
     * @throws Zend_View_Exception When no $content provided or invalid method
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>(set|(ap|pre)pend))(Style)$/', $method, $matches)) {
            if (1 > count($args)) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(sprintf('Method "%s" requires minimally content for the stylesheet', $method));
            }
            $action  = $matches['action'];
            $content = $args[0];
            $attrs   = array();
            $indent  = null;
            if (isset($args[1])) {
                $attrs = (array) $args[1];
            }
            if (isset($args[2])) {
                $indent = $args[2];
            }
            return $this->$action($this->_styleToString($content, $attrs, $indent));
        }

        require_once 'Zend/View/Exception.php';
        throw new Zend_View_Exception(sprintf('Invalid method %s::%s()', __CLASS__, $method));
    }

    /**
     * Determine if a value is a valid style tag
     * 
     * @param  mixed $value 
     * @param  string $method 
     * @return true
     * @throws Zend_View_Exception if invalid
     */
    protected function _isValid($value, $method)
    {
        if (!is_string($value)
            || ('<style ' !== substr($value, 0, 7))
            || ('</style>' != substr($value, -8)))
        {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(sprintf('Invalid style tag provided to %s', $method));
        }

        return true;
    }

    /**
     * Override append to enforce style creation
     * 
     * @param  mixed $value 
     * @return void
     */
    public function append($value)
    {
        $this->_isValid($value, __METHOD__);
        return parent::append($value);
    }
   
    /**
     * Override prepend to enforce style creation
     * 
     * @param  mixed $value 
     * @return void
     */
    public function prepend($value)
    {
        $this->_isValid($value, __METHOD__);
        return parent::prepend($value);
    }

    /**
     * Override set to enforce style creation
     * 
     * @param  mixed $value 
     * @return void
     */
    public function set($value)
    {
        $this->_isValid($value, __METHOD__);
        return parent::set($value);
    }

    /**
     * Start capture action
     * 
     * @param  mixed $captureType 
     * @param  string $typeOrAttrs 
     * @return void
     */
    public function captureStart($type = Zend_View_Helper_Placeholder_Container_Abstract::APPEND, $attrs = null)
    {
        $this->_captureAttrs = $attrs;
        return parent::captureStart($type);
    }
    
    /**
     * End capture action and store
     * 
     * @return void
     */
    public function captureEnd()
    {
        $content             = ob_get_clean();
        $attrs               = $this->_captureTypeOrAttrs;
        $this->_captureAttrs = null;
        $this->_captureLock  = false;

        switch ($this->_captureType) {
            case self::SET:
                $this->setStyle($content, $attrs);
                break;
            case self::PREPEND:
                $this->prependStyle($content, $attrs);
                break;
            case self::APPEND:
            default:
                $this->appendStyle($content, $attrs);
                break;
        }
    }

    /**
     * Convert content and attributes into valid style tag
     * 
     * @param  string $content 
     * @param  array $attributes 
     * @return string
     */
    protected function _styleToString($content, array $attributes, $indent)
    {
        if (null !== $indent) {
            if (!is_int($indent) && !is_string($indent)) {
                $indent = $this->_indent;
            }
        } else {
            $indent = $this->_indent;
        }

        if ($this->view) {
            $useCdata = $this->view->doctype()->isXhtml() ? true : false;
        } else {
            $useCdata = $this->useCdata ? true : false;
        }
        $escapeStart = ($useCdata) ? '<![CDATA[' : '<!--';
        $escapeEnd   = ($useCdata) ? ']]>'       : '-->';

        $attrString = '';
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                if (!in_array($key, $this->_optionalAttributes)) {
                    continue;
                }
                if ('media' == $key) {
                    if (!in_array($value, $this->_mediaTypes)) {
                        continue;
                    }
                }
                $attrString .= sprintf(' %s="%s"', $key, htmlspecialchars($value));
            }
        }

        $html = '<style type="text/css"' . $attrString . '>' . PHP_EOL
              . $indent . $escapeStart . PHP_EOL . $indent . $content . PHP_EOL . $indent . $escapeEnd . PHP_EOL
              . '</style>';

        return $html;
    }
}
