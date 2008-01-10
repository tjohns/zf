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
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element */
require_once 'Zend/Form/Element.php';

/**
 * Base element for XHTML elements
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
abstract class Zend_Form_Element_Xhtml extends Zend_Form_Element
{
    /**
     * XHTML attribute properties
     * @var array
     */
    protected $_attribs = array(
        'accept'      => null,
        'align'       => null,
        'alt'         => null,
        'disabled'    => null,
        'readOnly'    => null,
        'id'          => null,
        'title'       => null,
        'class'       => null,
        'style'       => null,
        'tabIndex'    => null,
        'accessKey'   => null,
        'onFocus'     => null,
        'onBlur'      => null,
        'onSelect'    => null,
        'onChange'    => null,
        'onClick'     => null,
        'onDblClick'  => null,
        'onMouseDown' => null,
        'onMouseUp'   => null,
        'onMouseOver' => null,
        'onMouseMove' => null,
        'onMouseOut'  => null,
        'onKeyPress'  => null,
        'onKeyDown'   => null,
        'onKeyUp'     => null,
    );

    /**
     * Retrieve accept
     *
     * @return mixed
     */
    public function getAccept()
    {
        return $this->_attribs['accept'];
    }

    /**
     * Set accept
     *
     * @param  string $accept
     * @return Zend_Form_Element_Xhtml
     */
    public function setAccept($accept)
    {
        $this->_attribs['accept'] = $accept;
        return $this;
    }

    /**
     * Retrieve align
     *
     * @return mixed
     */
    public function getAlign()
    {
        return $this->_attribs['align'];
    }

    /**
     * Set align
     *
     * @param  string $align
     * @return Zend_Form_Element_Xhtml
     */
    public function setAlign($align)
    {
        $this->_attribs['align'] = $align;
        return $this;
    }

    /**
     * Retrieve alt
     *
     * @return mixed
     */
    public function getAlt()
    {
        return $this->_attribs['alt'];
    }

    /**
     * Set alt
     *
     * @param  string $alt
     * @return Zend_Form_Element_Xhtml
     */
    public function setAlt($alt)
    {
        $this->_attribs['alt'] = $alt;
        return $this;
    }

    /**
     * Retrieve disabled
     *
     * @return mixed
     */
    public function getDisabled()
    {
        return $this->_attribs['disabled'];
    }

    /**
     * Set disabled
     *
     * @param  string $disabled
     * @return Zend_Form_Element_Xhtml
     */
    public function setDisabled($disabled)
    {
        $this->_attribs['disabled'] = $disabled;
        return $this;
    }

    /**
     * Retrieve readOnly
     *
     * @return mixed
     */
    public function getReadOnly()
    {
        return $this->_attribs['readOnly'];
    }

    /**
     * Set readOnly
     *
     * @param  string $readOnly
     * @return Zend_Form_Element_Xhtml
     */
    public function setReadOnly($readOnly)
    {
        $this->_attribs['readOnly'] = $readOnly;
        return $this;
    }

    /**
     * Retrieve id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->_attribs['id'];
    }

    /**
     * Set id
     *
     * @param  string $id
     * @return Zend_Form_Element_Xhtml
     */
    public function setId($id)
    {
        $this->_attribs['id'] = $id;
        return $this;
    }

    /**
     * Retrieve title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->_attribs['title'];
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Zend_Form_Element_Xhtml
     */
    public function setTitle($title)
    {
        $this->_attribs['title'] = $title;
        return $this;
    }

    /**
     * Retrieve class
     *
     * @return mixed
     */
    public function getClass()
    {
        return $this->_attribs['class'];
    }

    /**
     * Set class
     *
     * @param  string $class
     * @return Zend_Form_Element_Xhtml
     */
    public function setClass($class)
    {
        $this->_attribs['class'] = $class;
        return $this;
    }

    /**
     * Retrieve style
     *
     * @return mixed
     */
    public function getStyle()
    {
        return $this->_attribs['style'];
    }

    /**
     * Set style
     *
     * @param  string $style
     * @return Zend_Form_Element_Xhtml
     */
    public function setStyle($style)
    {
        $this->_attribs['style'] = $style;
        return $this;
    }

    /**
     * Retrieve tabIndex
     *
     * @return mixed
     */
    public function getTabIndex()
    {
        return $this->_attribs['tabIndex'];
    }

    /**
     * Set tabIndex
     *
     * @param  string $tabIndex
     * @return Zend_Form_Element_Xhtml
     */
    public function setTabIndex($tabIndex)
    {
        $this->_attribs['tabIndex'] = $tabIndex;
        return $this;
    }

    /**
     * Retrieve accessKey
     *
     * @return mixed
     */
    public function getAccessKey()
    {
        return $this->_attribs['accessKey'];
    }

    /**
     * Set accessKey
     *
     * @param  string $accessKey
     * @return Zend_Form_Element_Xhtml
     */
    public function setAccessKey($accessKey)
    {
        $this->_attribs['accessKey'] = $accessKey;
        return $this;
    }

    /**
     * Retrieve onFocus
     *
     * @return mixed
     */
    public function getOnFocus()
    {
        return $this->_attribs['onFocus'];
    }

    /**
     * Set onFocus
     *
     * @param  string $onFocus
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnFocus($onFocus)
    {
        $this->_attribs['onFocus'] = $onFocus;
        return $this;
    }

    /**
     * Retrieve onBlur
     *
     * @return mixed
     */
    public function getOnBlur()
    {
        return $this->_attribs['onBlur'];
    }

    /**
     * Set onBlur
     *
     * @param  string $onBlur
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnBlur($onBlur)
    {
        $this->_attribs['onBlur'] = $onBlur;
        return $this;
    }

    /**
     * Retrieve onSelect
     *
     * @return mixed
     */
    public function getOnSelect()
    {
        return $this->_attribs['onSelect'];
    }

    /**
     * Set onSelect
     *
     * @param  string $onSelect
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnSelect($onSelect)
    {
        $this->_attribs['onSelect'] = $onSelect;
        return $this;
    }

    /**
     * Retrieve onChange
     *
     * @return mixed
     */
    public function getOnChange()
    {
        return $this->_attribs['onChange'];
    }

    /**
     * Set onChange
     *
     * @param  string $onChange
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnChange($onChange)
    {
        $this->_attribs['onChange'] = $onChange;
        return $this;
    }

    /**
     * Retrieve onClick
     *
     * @return mixed
     */
    public function getOnClick()
    {
        return $this->_attribs['onClick'];
    }

    /**
     * Set onClick
     *
     * @param  string $onClick
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnClick($onClick)
    {
        $this->_attribs['onClick'] = $onClick;
        return $this;
    }

    /**
     * Retrieve onDblClick
     *
     * @return mixed
     */
    public function getOnDblClick()
    {
        return $this->_attribs['onDblClick'];
    }

    /**
     * Set onDblClick
     *
     * @param  string $onDblClick
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnDblClick($onDblClick)
    {
        $this->_attribs['onDblClick'] = $onDblClick;
        return $this;
    }

    /**
     * Retrieve onMouseDown
     *
     * @return mixed
     */
    public function getOnMouseDown()
    {
        return $this->_attribs['onMouseDown'];
    }

    /**
     * Set onMouseDown
     *
     * @param  string $onMouseDown
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnMouseDown($onMouseDown)
    {
        $this->_attribs['onMouseDown'] = $onMouseDown;
        return $this;
    }

    /**
     * Retrieve onMouseUp
     *
     * @return mixed
     */
    public function getOnMouseUp()
    {
        return $this->_attribs['onMouseUp'];
    }

    /**
     * Set onMouseUp
     *
     * @param  string $onMouseUp
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnMouseUp($onMouseUp)
    {
        $this->_attribs['onMouseUp'] = $onMouseUp;
        return $this;
    }

    /**
     * Retrieve onMouseMove
     *
     * @return mixed
     */
    public function getOnMouseMove()
    {
        return $this->_attribs['onMouseMove'];
    }

    /**
     * Set onMouseMove
     *
     * @param  string $onMouseMove
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnMouseMove($onMouseMove)
    {
        $this->_attribs['onMouseMove'] = $onMouseMove;
        return $this;
    }

    /**
     * Retrieve onMouseOut
     *
     * @return mixed
     */
    public function getOnMouseOut()
    {
        return $this->_attribs['onMouseOut'];
    }

    /**
     * Set onMouseOut
     *
     * @param  string $onMouseOut
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnMouseOut($onMouseOut)
    {
        $this->_attribs['onMouseOut'] = $onMouseOut;
        return $this;
    }

    /**
     * Retrieve onKeyPress
     *
     * @return mixed
     */
    public function getOnKeyPress()
    {
        return $this->_attribs['onKeyPress'];
    }

    /**
     * Set onKeyPress
     *
     * @param  string $onKeyPress
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnKeyPress($onKeyPress)
    {
        $this->_attribs['onKeyPress'] = $onKeyPress;
        return $this;
    }

    /**
     * Retrieve onKeyDown
     *
     * @return mixed
     */
    public function getOnKeyDown()
    {
        return $this->_attribs['onKeyDown'];
    }

    /**
     * Set onKeyDown
     *
     * @param  string $onKeyDown
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnKeyDown($onKeyDown)
    {
        $this->_attribs['onKeyDown'] = $onKeyDown;
        return $this;
    }

    /**
     * Retrieve onKeyUp
     *
     * @return mixed
     */
    public function getOnKeyUp()
    {
        return $this->_attribs['onKeyUp'];
    }

    /**
     * Set onKeyUp
     *
     * @param  string $onKeyUp
     * @return Zend_Form_Element_Xhtml
     */
    public function setOnKeyUp($onKeyUp)
    {
        $this->_attribs['onKeyUp'] = $onKeyUp;
        return $this;
    }
}
