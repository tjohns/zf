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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * Data model class to represent a location 
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Extension_Where extends Zend_Gdata_Extension
{

    protected $_rootElement = 'gd:where';
    protected $_valueString = null;

    public function __construct($valueString = null) 
    {
        parent::__construct();
        $this->setValueString($valueString);
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        $element->setAttribute('valueString', $this->_valueString);
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'valueString':
            $this->_valueString = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    public function __toString() 
    {
        return $this->_valueString;
    }

    public function getValueString()
    {
        return $this->_valueString;
    }

    public function setValueString($value)
    {
        $this->_valueString = $value;
        return $this;
    }

}
