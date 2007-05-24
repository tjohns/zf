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
 * Represents the gCal:accessLevel element used by the Calendar data API
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_Extension_AccessLevel extends Zend_Gdata_Extension
{

    protected $_rootNamespace = 'gCal';
    protected $_rootElement = 'gCal:accesslevel';
    protected $_value = null;

    public function __construct($value = null) 
    {
        parent::__construct();
        foreach (Zend_Gdata_Calendar::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        $this->_value = $value; 
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        $element->setAttribute('value', $this->_value);
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'value':
            $this->_value = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    public function __toString() 
    {
        return $this->_value;
    }

}
