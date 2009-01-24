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
 * @package    Zend_Tag
 * @subpackage Cloud
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Tag_Cloud_Decorator_Tag
 */
require_once 'Zend/Tag/Cloud/Decorator/Tag.php';

/**
 * Simple HTML decorator for tags
 *
 * @category  Zend
 * @package   Zend_Tag
 * @uses      Zend_Tag_Cloud_Decorator_Tag
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tag_Cloud_Decorator_HtmlTag extends Zend_Tag_Cloud_Decorator_Tag
{
    /**
     * List of HTML tags
     *
     * @var array
     */
    protected $_htmlTags = array(
        'li'
    );
    
    /**
     * Minimum fontsize
     *
     * @var integer
     */
    protected $_minFontSize = 10;
    
    /**
     * Maximum fontsize
     *
     * @var integer
     */
    protected $_maxFontSize = 20;
    
    /**
     * Unit for the fontsize
     *
     * @var string
     */
    protected $_fontSizeUnit = 'px';
    
    /**
     * List of tags which get assigned to the inner element instead of
     * font-sizes.
     * 
     * @var array
     */
    protected $_classList = null;
    
    /**
     * Set the HTML tags surrounding the <a> element
     *
     * @param  array $htmlTags
     * @return Zend_Tag_Cloud_Decorator_HtmlTag
     */
    public function setHtmlTags(array $htmlTags)
    {
        $this->_htmlTags = $htmlTags;
        return $this;   
    }
    
    /**
     * Set minimum font size
     *
     * @param  string $minFontSize
     * @return Zend_Tag_Cloud_Decorator_HtmlTag
     */
    public function setMinFontSize($minFontSize)
    {
        $this->_minFontSize = (int) $minFontSize;
        return $this;
    }
    
    /**
     * Set maximum font size
     * 
     * Automatically resets the class list
     *
     * @param  string $maxFontSize
     * @return Zend_Tag_Cloud_Decorator_HtmlTag
     */
    public function setMaxFontSize($maxFontSize)
    {
        $this->_maxFontSize = (int) $maxFontSize;
        $this->_classList   = null;
        return $this;
    }
    
    /**
     * Set the font size unit
     *
     * Automatically resets the class list
     * 
     * @param  string $fontSizeUnit
     * @return Zend_Tag_Cloud_Decorator_HtmlTag
     */
    public function setFontSizeUnit($fontSizeUnit)
    {
        $this->_fontSizeUnit = $fontSizeUnit;
        $this->_classList    = null;
        return $this;
    }
    
    /**
     * Set a list of classes to use instead of fontsizes
     *
     * @param  array $classList
     * @return Zend_Tag_Cloud_Decorator_HtmlTag
     */
    public function setClassList(array $classList)
    {
        $this->_classList = $classList;
        return $this;
    }
    
    /**
     * Defined by Zend_Tag_Cloud_Decorator_Tag 
     * 
     * @return array
     */
    public function getWeightList()
    {
        if ($this->_classList !== null) {
            return $this->_classList;
        } else {
            return range($this->_minFontSize, $this->_maxFontSize);
        }
    }
    
    /**
     * Defined by Zend_Tag_Cloud_Decorator_Tag
     *
     * @param  array $tags
     * @return array
     */
    public function render(array $tags)
    {
        $result = array();
        
        foreach ($tags as $tag) {
            if ($this->_classList === null) {
                $attribute = sprintf('style="font-size: %d%s"', $tag->getWeightValue(), $this->_fontSizeUnit);
            } else {
                $attribute = sprintf('class="%s"', htmlspecialchars($tag->getWeightValue()));
            }
            
            $tagHtml = sprintf('<a href="%s" %s>%s</a>', $tag->getUrl(), $attribute, $tag->getTitle());
            
            foreach ($this->_htmlTags as $key => $data) {
                if (is_array($data)) {
                    $htmlTag    = $key;
                    $attributes = '';
                    
                    foreach ($data as $param => $value) {
                        $attributes .= ' ' . $param . '="' . htmlspecialchars($value) . '"';
                    }
                } else {
                    $htmlTag    = $data;
                    $attributes = '';
                }
                
                $htmlTag = sprintf('<%1$s%3$s>%2$s</%1$s>', $htmlTag, $tagHtml, $attributes);
            }
            
            $result[] = $tagHtml;
        }
        
        return $result;
    }
}
