<?php

require_once 'Zend/View/Helper/Placeholder/Container/Abstract.php';

/**
 * Zend_Layout_View_Helper_HeadLink
 *
 * @see http://www.w3.org/TR/xhtml1/dtds.html
 */
class Zend_View_Helper_HeadLink extends Zend_View_Helper_Placeholder_Container_Abstract
{
    
    /**
     * $_validAttributes
     *
     * @var array
     */
    protected $_itemKeys = array('charset', 'href', 'hreflang', 'media', 'rel', 'rev', 'type');
    
    /**
     * headLink() - View Helper Method
     *
     * @return unknown
     */
    public function headLink()
    {
        return $this;
    }
    
    /**
     * append()
     *
     * @param array $attributes
     * @param int $index
     * @return Zend_Layout_ViewHelper_HeadLink
     */
    public function append(Array $attributes)
    {
        if ($link = $this->_buildLinkFromAttributesArray($attributes)) {
            return parent::append($link);
        }
        
        throw new Zend_View_Exception('No valid attributes where provided to Zend_View_Helper_HeadLink::append() to build a link with.');
    }
    
    public function offsetSet($index, Array $attributes) 
    {
        if ($link = $this->_buildLinkFromAtrributesArray($attributes)) {
            return parent::offsetSet($index, $link);
        }
        
        throw new Zend_View_Exception('No valid attributes where provided to Zend_View_Helper_HeadLink::offsetSet() to build a link with.');
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
    
    public function offsetSetStylesheet($index, $href, $media = 'screen', $conditionalStylesheet = null)
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
        
        return parent::offsetSet($index, $link);
    }

    /**
     * appendAlternate()
     *
     * @param string $href
     * @param string $type
     * @param string $title
     * @return Xend_Layout_ViewHelper_HeadLink
     */
    public function appendAlternate($href, $type, $title)
    {
        $attrs = array(
            'rel'   => 'alternate',
            'href'  => $href,
            'type'  => $type,
            'title' => $title
            );
        $this->append($attrs);
        return $this;
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
     * @param array $attributesArray
     * @return string|false
     */
    protected function _buildLinkFromAttributesArray(Array $attributesArray)
    {
        if ($attributesArray == null) {
            return false;
        }
        
        $link = '<link ';
        
        foreach ($this->_itemKeys as $itemKey) {
            if (isset($attributesArray[$itemKey])) {
                $link .= $name . '="' . $value . '" ';
            }
        }
        
        $link .= '/>';
        
        if ($link == '<link />') {
            return false;
        }
        
        return $link;
    }
    
}