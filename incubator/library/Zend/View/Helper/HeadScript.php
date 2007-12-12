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
 * Helper for setting and retrieving title element for HTML head
 *
 * @todo       Add PREPEND support
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_HeadScript extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**#@+
     * Script type contants
     * @const string
     */
    const FILE   = 'FILE';
    const SCRIPT = 'SCRIPT';
    /**#@-*/
    
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Zend_View_Helper_HeadScript';

    /**
     * Capture type and/or attributes (used for hinting during capture)
     * @var string
     */
    protected $_captureTypeOrAttrs = null;
    
    /**
     * Return headScript object
     *
     * @return Zend_View_Helper_HeadScript
     */
    public function headScript()
    {
        return $this;
    }
   
    /**
     * Append script file to object
     * 
     * @param  string $sourceFile 
     * @param  string $type 
     * @return Zend_View_Helper_HeadScript
     */
    public function append($sourceFile, $type = 'text/javascript')
    {
        return $this->_offsetSet($this->nextIndex(), $script, $type, self::FILE);
    }
    
    /**
     * Append script to object
     * 
     * @param  string $script 
     * @param  string $type 
     * @return Zend_View_Helper_HeadScript
     */
    public function appendScript($script, $type = 'text/javascript')
    {
        $this->_offsetSet($this->nextIndex(), $script, $type, self::SCRIPT);
        return $this;
    }
    
    /**
     * Set script item in object
     * 
     * @param  string $index 
     * @param  string $sourceFile 
     * @param  string $typeOrAttrs 
     * @return Zend_View_Helper_HeadScript
     */
    public function offsetSet($index, $sourceFile, $typeOrAttrs = 'text/javascript')
    {
        return $this->_offsetSet($index, $script, $typeOrAttrs, self::FILE);
    }
    
    /**
     * Set script item in object
     * 
     * @param  string $index 
     * @param  string $script 
     * @param  string $typeOrAttrs 
     * @return Zend_View_Helper_HeadScript
     */
    public function offsetSetScript($index, $script, $typeOrAttrs = 'text/javascript')
    {
        return $this->_offsetSet($index, $sourceFile, $typeOrAttrs, self::SCRIPT);
    }
    
    /**
     * Set new script item in object
     * 
     * @todo   Support PREPEND
     * @param  int|string $index 
     * @param  string $scriptOrSourceFile 
     * @param  string $typeOrAttrs 
     * @param  string $mode 
     * @return Zend_View_Helper_HeadScript
     */
    protected function _offsetSet($index, $scriptOrSourceFile, $typeOrAttrs = 'text/javascript', $mode = Zend_View_Helper_HeadScript::FILE)
    {
        $valueArray = array(
            'content' => $scriptOrSourceFile,
            'mode'    => $mode
            );
        
        if (is_array($type)) {
            $valueArray = array_merge($typeOrAttrs, $valueArray);
        } else {
            $valueArray['type'] = (string) $typeOrAttrs;
        }
            
        parent::offsetSet($index, $valueArray);
        return $this;
    }
    
    /**
     * Start capture action
     * 
     * @param  mixed $captureType 
     * @param  string $typeOrAttrs 
     * @return void
     */
    public function captureStart($captureType = Zend_View_Helper_Placeholder_Container_Abstract::APPEND, $typeOrAttrs = 'text/javascript')
    {
        $this->_captureTypeOrAttrs = $typeOrAttrs;
        return parent::captureStart($captureType);
    }
    
    /**
     * End capture action and store
     * 
     * @todo   Support PREPEND
     * @return void
     */
    public function captureEnd()
    {
        $content = ob_get_clean();
        $typeOrAttrs = $this->_captureTypeOrAttrs;
        $this->_captureTypeOrAttrs = null;
        
        $this->_captureLock = false;
        switch ($this->_captureType) {
            case self::SET:
                $this->exchangeArray(array($valueArray));
                break;
            case self::APPEND:
            default:
                $this->_offsetSet($this->nextIndex(), $content, $this->_captureTypeOrAttrs, self::SCRIPT);
                break;
        }
    }
    
    /**
     * Render as string
     * 
     * @return string
     */
    public function toString($indent = null)
    {
        $indent   = ($indent != null) ? $indent : $this->_indent;
        $indent   = (is_int($indent)) ? str_repeat(' ', $indent) : $indent;
        $useCdata = $this->view->doctype()->isXhtml() ? true : false;
        $output   = '';
        
        foreach ($this as $script) {
            $output .= '<script ';
            
            $content = null;
            if (isset($script['content'])) {
                $content = $script['content'];
            }
            unset($script['content']);
            
            $mode = $script['mode'];
            unset($script['mode']);
            
            foreach ($script as $name => $attr) {
                $output .= $name . '="' . $attr . '" ';
            }
            
            $output = rtrim($output) . '>';
            
            if ($content) {
                if ($useCdata) {
                    $output .= PHP_EOL . $indent . '//<![CDATA[' . PHP_EOL . $indent . $content . PHP_EOL . $indent . '//]]>' . PHP_EOL . $indent;
                } else {
                    $output .= $content;
                }
            }
            
            $output .= '</script>' . PHP_EOL . $indent;
        }
        
        return trim($output);
    }
}
