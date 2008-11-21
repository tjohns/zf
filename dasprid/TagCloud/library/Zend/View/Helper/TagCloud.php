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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_View_Helper_Abstract
 */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_View_Helper_TagCloud extends Zend_View_Helper_Abstract
{
    /**
     * Minimum fontsize
     *
     * @var integer
     */
    protected $_minFontSize = 10;
    
    /**
     * Maximum fontsize
     *
     * @var string
     */
    protected $_maxFontSize = 20;
    
    /**
     * Unit used for the fontsize
     *
     * @var string
     */
    protected $_fontSizeUnit = 'px';
    
    /**
     * Key in the tag array which defines the weight
     *
     * @var string
     */
    protected $_weightKey = 'weight';
    
    /**
     * Key in the tag array which defines the target URL
     *
     * @var string
     */
    protected $_urlKey = 'url';
    
    /**
     * Key in the tag array which defines the title
     *
     * @var string
     */
    protected $_titleKey = 'title';
    
    /**
     * ID of the outer <ul> element
     *
     * @var string
     */
    protected $_htmlId = null;
    
    /**
     * Class of the outer <ul> element
     *
     * @var string
     */
    protected $_htmlClass = null;
    
    /**
     * Tags for the tagcloud
     *
     * @var array
     */
    protected $_tags = null;
    
    /**
     * Create a new tag cloud
     *
     * @param  array $tags
     * @return Zend_View_Helper_TagCloud
     */
    public function tagCloud(array $tags = null)
    {
        if ($tags !== null) {
            $this->_tags = $tags;
        }
        
        return $this;
    }
    
    /**
     * Set minimum font size
     *
     * @param  string $minFontSize
     * @return Zend_View_Helper_TagCloud
     */
    public function setMinFontSize($minFontSize)
    {
        $this->_minFontSize = (int) $minFontSize;
        return $this;
    }
    
    /**
     * Set maximum font size
     *
     * @param  string $maxFontSize
     * @return Zend_View_Helper_TagCloud
     */
    public function setMaxFontSize($maxFontSize)
    {
        $this->_maxFontSize = (int) $maxFontSize;
        return $this;
    }
    
    /**
     * Set the font size unit
     *
     * @param  string $fontSizeUnit
     * @return Zend_View_Helper_TagCloud
     */
    public function setFontSizeUnit($fontSizeUnit)
    {
        $this->_fontSizeUnit = $fontSizeUnit;
        return $this;
    }
    
    /**
     * Set the id attribute for the <ul> tag
     *
     * @param  string $htmlId
     * @return Zend_View_Helper_TagCloud
     */
    public function setHtmlId($htmlId = null)
    {
        $this->_htmlId = $htmlId;
        return $this;
    }
    
    /**
     * Set the class attribute for the <ul> tag
     *
     * @param  string $htmlClass
     * @return Zend_View_Helper_TagCloud
     */
    public function setHtmlClass($htmlClass = null)
    {
        $this->_htmlClass = $htmlClass;
        return $this;
    }
    
    /**
     * Set the weight key in the tags array
     *
     * @param  string $weightKey
     * @return Zend_View_Helper_TagCloud
     */
    public function setWeightKey($weightKey)
    {
        $this->_weightKey = $weightKey;
        return $this;
    }
    
    /**
     * Set the url key in the tags array
     *
     * @param  string $urlKey
     * @return Zend_View_Helper_TagCloud
     */
    public function setUrlKey($urlKey)
    {
        $this->_urlKey = $urlKey;
        return $this;
    }
    
    /**
     * Set the title key in the tags array
     *
     * @param  string $titleKey
     * @return Zend_View_Helper_TagCloud
     */
    public function setTitleKey($titleKey)
    {
        $this->_titleKey = $titleKey;
        return $this;
    }
    
    /**
     * Render the tag cloud
     *
     * @see    http://files.blog-city.com/files/J05/88284/b/insearchofperfecttagcloud.pdf
     * @return string
     */
    public function render()
    {
        // Check if a tag array was supplied
        if ($this->_tags === null) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('You must supply a tag array');
        }
        
        // Calculate min and max weight
        $minWeight = null;
        $maxWeight = null;
        
        foreach ($this->_tags as $tag) {
            if ($minWeight === null && $maxWeight === null) {
                $minWeight = $tag[$this->_weightKey];
                $maxWeight = $tag[$this->_weightKey];
            } else {
                $minWeight = min($minWeight, $tag[$this->_weightKey]);
                $maxWeight = max($maxWeight, $tag[$this->_weightKey]);                
            }
        }
        
        // First calculate the thresholds
        $steps      = ($this->_maxFontSize - $this->_minFontSize);
        $delta      = ($maxWeight - $minWeight) / $steps;
        $thresholds = array();
        for ($i = 0; $i <= $steps; $i++) {
            $thresholds[$i] = 100 * log(($minWeight + $i * $delta) + 2);
        }
       
        // Then generate the html
        $xhtml = '<ul';
        
        if ($this->_htmlId !== null) {
            $xhtml .= ' id="' . $this->view->escape($this->_htmlId) . '"';
        }
        
        if ($this->_htmlClass !== null) {
            $xhtml .= ' class="' . $this->view->escape($this->_htmlClass) . '"';
        }
        
        $xhtml .= '>';

        foreach ($this->_tags as $tag) {
            if (!isset($tag[$this->_weightKey])) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('Weight key not found in tag');            
            }
            
            if (!isset($tag[$this->_titleKey])) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('Title key not found in tag');            
            }
            
            $threshold = 100 * log($tag[$this->_weightKey] + 2); 
            for ($i = 0; $i <= $steps; $i++) {
                if ($threshold <= $thresholds[$i]) {
                    $fontSize = $this->_minFontSize + $i;
                    break;
                }
            }
            
            $xhtml .= '<li>';
            
            if (isset($tag[$this->_urlKey])) {
                $xhtml .= '<a href="' . $this->view->escape($tag[$this->_urlKey]) . '">';
            }
            
            $xhtml .= $this->view->escape($tag[$this->_titleKey]);
            
            if (isset($tag[$this->_urlKey])) {
                $xhtml .= '</a>';
            }
            
            $xhtml .= '</li>';
        }
        
        $xhtml .= '</ul>';

        // And return it
        return $xhtml;
    }
    
    /**
     * Render the tag cloud
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $xhtml = $this->render();
            return $xhtml;
        } catch (Exception $e) {
            $message = "Exception caught by tagCloud: " . $e->getMessage()
                     . "\nStack Trace:\n" . $e->getTraceAsString();
            trigger_error($message, E_USER_WARNING);
            return '';
        }
    }
}
