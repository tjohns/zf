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
 * @category  Zend
 * @package   Zend_TagCloud
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @see Zend_TagCloud_Decorator_Cloud
 */
require_once 'Zend/TagCloud/Decorator/Cloud.php';

/**
 * Simple HTML decorator for clouds
 *
 * @category  Zend
 * @package   Zend_TagCloud
 * @uses      Zend_TagCloud_Decorator_Cloud
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_TagCloud_Decorator_HtmlCloud extends Zend_TagCloud_Decorator_Cloud
{
    /**
     * List of HTML tags
     *
     * @var array
     */
    protected $_htmlTags = array(
        'ul'
    );
    
    /**
     * Separator for the single tags
     *
     * @var string
     */
    protected $_separator = '';
    
    /**
     * Set the HTML tags surrounding all tags
     *
     * @param  array $htmlTags
     * @return Zend_TagCloud_Decorator_HtmlCloud
     */
    public function setHtmlTags(array $htmlTags)
    {
        $this->_htmlTags = $htmlTags;
        return $this;   
    }
    
    /**
     * Set the separator between the single tags
     *
     * @param  string
     * @return Zend_TagCloud_Decorator_HtmlCloud
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }
    
    /**
     * Defined by Zend_TagCloud_Decorator_Cloud
     *
     * @param  array $tags
     * @return array
     */
    public function render(array $tags)
    {
        $cloudHtml = implode($this->_separator, $tags);
        
        foreach ($this->_htmlTags as $htmlTag) {
            $cloudTag = sprintf('<%1$s>%2$s</%1$s>', $htmlTag, $cloudHtml);
        }
        
        return $cloudHtml;
    }
}
