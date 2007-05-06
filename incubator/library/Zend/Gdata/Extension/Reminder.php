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
 * @package    Zend_Gdata_Extension
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * Implements the gd:reminder element used to set/retrieve notifications 
 *
 * @category   Zend
 * @package    Zend_Gdata_Extension
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Extension_Reminder extends Zend_Gdata_Extension
{

    protected $_rootElement = 'gd:reminder';
    protected $_minutes = null;
    protected $_method = null;

    public function __construct($minutes = null, $method = null)
    {
        parent::__construct();
        $this->_minutes = $minutes; 
        $this->_method = $method; 
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_minutes != null) {
            $element->setAttribute('minutes', $this->_minutes);
        }
        if ($this->_method != null) {
            $element->setAttribute('method', $this->_method);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'minutes':
            $this->_minutes = $attribute->nodeValue;
            break;
        case 'method':
            $this->_method = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    public function __toString() 
    {
        return 'Starts: ' . $this->getMinutes() . ' ' .
               'Ends: ' .  $this->getMethod();
    }

    public function getMinutes()
    {
        return $this->_minutes;
    }

    public function setMinutes($value)
    {
        $this->_minutes = $value;
        return $this;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function setMethod($value)
    {
        $this->_method = $value;
        return $this;
    }

}
