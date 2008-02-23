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
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Decorator_Abstract */
require_once 'Zend/Form/Decorator/Abstract.php';

/**
 * Zend_Form_Decorator_Image
 *
 * Accepts the options:
 * - separator: separator to use between image and content (defaults to PHP_EOL)
 * - placement: whether to append or prepend label to content (defaults to append)
 * - tag: if set, used to wrap the label in an additional HTML tag
 *
 * Any other options passed will be used as HTML attributes of the image tag.
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form_Decorator_Image extends Zend_Form_Decorator_Abstract
{
    /**
     * Image
     * @var string
     */
    protected $_image;

    /**
     * Default placement: append
     * @var string
     */
    protected $_placement = 'APPEND';

    /**
     * HTML tag with which to surround image
     * @var string
     */
    protected $_tag;

    /**
     * Set HTML tag with which to surround label
     * 
     * @param  string $tag 
     * @return Zend_Form_Decorator_Image
     */
    public function setTag($tag)
    {
        $this->_tag = (string) $tag;
        return $this;
    }

    /**
     * Get HTML tag, if any, with which to surround label
     * 
     * @return void
     */
    public function getTag()
    {
        if (null === $this->_tag) {
            $tag = $this->getOption('tag');
            if (null !== $tag) {
                $this->removeOption('tag');
                $this->setTag($tag);
            }
            return $tag;
        }

        return $this->_tag;
    }

    /**
     * Set image
     * 
     * @param  string $image 
     * @return Zend_Form_Decorator_Image
     */
    public function setImage($image)
    {
        $this->_image = (string) $image;
        return $this;
    }

    /**
     * Get image
     *
     * If not set internally, attempts to pull from element
     * 
     * @return string
     */
    public function getImage()
    {
        if (null === $this->_image) {
            if (null !== ($element = $this->getElement())) {
                if (method_exists($element, 'getValue')) {
                    $this->setImage($element->getValue());
                }
            }
        }

        return $this->_image;
    }

    /**
     * Render a form image
     * 
     * @param  string $content 
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $image     = $this->getImage();
        $tag       = $this->getTag();
        $placement = $this->getPlacement();
        $separator = $this->getSeparator();
        $options   = $this->getOptions();

        $image = $view->formImage($element->getName(), $image, $options); 

        if (null !== $tag) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $decorator->setOptions(array('tag' => $tag));
            $image = $decorator->render($image);
        }

        switch ($placement) {
            case self::PREPEND:
                return $image . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $image;
        }
    }
}
