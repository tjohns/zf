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

/** Zend_View_Exception */
require_once 'Zend/View/Exception.php';

/**
 * Zend_Layout_View_Helper_HeadLink
 *
 * @see        http://www.w3.org/TR/xhtml1/dtds.html
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_HeadLink extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**
     * $_validAttributes
     *
     * @var array
     */
    protected $_itemKeys = array('charset', 'href', 'hreflang', 'media', 'rel', 'rev', 'type', 'title');

    /**
     * @var string registry key
     */
    protected $_regKey = 'Zend_View_Helper_HeadLink';

    /**
     * Constructor
     *
     * Use PHP_EOL as separator
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSeparator(PHP_EOL);
    }
    
    /**
     * headLink() - View Helper Method
     *
     * Returns current object instance. Optionally, allows passing array of 
     * values to build link.
     *
     * @return Zend_View_Helper_HeadLink
     */
    public function headLink(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        if (null !== $attributes) {
            switch ($placement) {
                case self::SET:
                    $this->set($attributes);
                    break;
                case self::PREPEND:
                    $this->prepend($attributes);
                    break;
                case self::APPEND:
                default:
                    $this->append($attributes);
                    break;
            }
        }
        return $this;
    }
    
    /**
     * prepend()
     *
     * @param  array $attributes
     * @return Zend_Layout_ViewHelper_HeadLink
     */
    public function prepend($attributes)
    {
        if (!is_array($attributes)) {
            throw new Zend_View_Exception('Zend_View_Helper_HeadLink::prepend() expects an array of attributes');
        }

        if ($link = $this->_buildLinkFromAttributesArray($attributes)) {
            return parent::prepend($link);
        }

        throw new Zend_View_Exception('Invalid attributes passed to Zend_View_Helper_HeadLink::prepend(); unable to create link');
    }

    /**
     * append()
     *
     * @param  array $attributes
     * @return Zend_Layout_ViewHelper_HeadLink
     */
    public function append($attributes)
    {
        if (!is_array($attributes)) {
            throw new Zend_View_Exception('Zend_View_Helper_HeadLink::append() expects an array of attributes; received: ' . var_export($attributes, 1));
        }

        if ($link = $this->_buildLinkFromAttributesArray($attributes)) {
            return parent::append($link);
        }

        throw new Zend_View_Exception('Invalid attributes passed to Zend_View_Helper_HeadLink::append(); unable to create link');
    }
    
    /**
     * offsetSet()
     * 
     * @param  string|int $index 
     * @param  array $attributes 
     * @return void
     */
    public function offsetSet($index, $attributes) 
    {
        if (is_string($attributes) && ('<link ' != substr($attributes, 0, 6))) {
            throw new Zend_View_Exception('Zend_View_Helper_HeadLink::offsetSet() expects fully qualified link html');
        } elseif (is_string($attributes) && ('<link ' == substr($attributes, 0, 6))) {
            return parent::offsetSet($index, $attributes);
        } elseif (!is_array($attributes)) {
            throw new Zend_View_Exception('Zend_View_Helper_HeadLink::offsetSet() expects an array of attributes');
        }

        if ($link = $this->_buildLinkFromAtrributesArray($attributes)) {
            return parent::offsetSet($index, $link);
        }
        
        throw new Zend_View_Exception('No valid attributes where provided to Zend_View_Helper_HeadLink::offsetSet() to build a link with.');
    }

    /**
     * Create a stylesheet link element
     * 
     * @param  string $href 
     * @param  string $media 
     * @param  bool $conditionalStylesheet 
     * @return string
     */
    protected function _createStylesheetLink($href, $media, $conditionalStylesheet)
    {
        $attributes = array(
            'rel'   => 'stylesheet',
            'type'  => 'text/css',
            'href'  => $href,
            'media' => $media
            );

        $link = $this->_buildLinkFromAttributesArray($attributes);

        if ($conditionalStylesheet) {
            $link = '<!--[if ' . $conditionalStylesheet . ']> ' . $link . '<![endif]-->';
        }

        return $link;
    }
    
    /**
     * prependStylesheet() - 
     *
     * @param string $href
     * @param string $media
     * @param string $conditionalStylesheet
     * @return Xend_Layout_ViewHelper_HeadLink
     */
    public function prependStylesheet($href, $media = 'screen', $conditionalStylesheet = null)
    {
        parent::prepend($this->_createStylesheetLink($href, $media, $conditionalStylesheet));
        return $this;
    }

    /**
     * appendStylesheet() - 
     *
     * @param string $href
     * @param string $media
     * @param string $conditionalStylesheet
     * @return Xend_Layout_ViewHelper_HeadLink
     */
    public function appendStylesheet($href, $media = 'screen', $conditionalStylesheet = null)
    {
        $this->offsetSetStylesheet($this->nextIndex(), $href, $media, $conditionalStylesheet);
        return $this;
    }
    
    /**
     * Insert stylesheet link by index
     * 
     * @param  mixed $index 
     * @param  mixed $href 
     * @param  string $media 
     * @param  mixed $conditionalStylesheet 
     * @return void
     */
    public function offsetSetStylesheet($index, $href, $media = 'screen', $conditionalStylesheet = null)
    {
        $link = $this->_createStylesheetLink($href, $media, $conditionalStylesheet);
        return parent::offsetSet($index, $link);
    }

    /**
     * Create alternate link
     * 
     * @param  string $href 
     * @param  string $type 
     * @param  string $title 
     * @return string
     */
    protected function _createAlternateLinkData($href, $type, $title)
    {
        $attrs = array(
            'rel'   => 'alternate',
            'href'  => $href,
            'type'  => $type,
            'title' => $title
        );
        return $attrs;
    }

    /**
     * prependAlternate()
     * 
     * @param  string $href 
     * @param  string $type 
     * @param  string $title 
     * @return Zend_View_Helper_HeadLink
     */
    public function prependAlternate($href, $type, $title)
    {
        return $this->prepend($this->_createAlternateLinkData($href, $type, $title));
    }

    /**
     * appendAlternate()
     *
     * @param  string $href
     * @param  string $type
     * @param  string $title
     * @return Zend_View_Helper_HeadLink
     */
    public function appendAlternate($href, $type, $title)
    {
        return $this->append($this->_createAlternateLinkData($href, $type, $title));
    }
    
    // Navigation?  need to find docs on this
    //public function appendStart()
    //public function appendContents()
    //public function appendPrev()
    //public function appendNext()
    //public function appendIndex()
    //public function appendEnd()
    //public function appendGlossary()
    //public function appendAppendix()
    //public function appendHelp()
    //public function appendBookmark()
    
    // Other?
    //public function appendCopyright()
    //public function appendChapter()
    //public function appendSection()
    //public function appendSubsection()
    
    /**
     * Build a link from the attributes array provided, return false if there is nothing
     * in the link itself
     *
     * @param  array $attributesArray
     * @return string|false
     */
    protected function _buildLinkFromAttributesArray(Array $attributes)
    {
        if (empty($attributes)) {
            return false;
        }
        
        $link = '<link ';
        
        foreach ($this->_itemKeys as $itemKey) {
            if (isset($attributes[$itemKey])) {
                $link .= $itemKey . '="' . $attributes[$itemKey] . '" ';
            }
        }
        
        $link .= '/>';
        
        if ($link == '<link />') {
            return false;
        }
        
        return $link;
    }
}
