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
 * @package    Zend_Gdata_Calendar
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_EntryAtom
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_Extension_Color
 */
require_once 'Zend/Gdata/Calendar/Extension/Color.php';

/**
 * @see Zend_Gdata_Extension_AccessLevel
 */
require_once 'Zend/Gdata/Calendar/Extension/AccessLevel.php';

/**
 * Represents a Calendar entry in the Calendar data API meta feed of a user's
 * calendars. 
 *
 * @category   Zend
 * @package    Zend_Gdata_Calendar
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_ListEntry extends Zend_Gdata_Entry
{

    protected $_color = null;
    protected $_accessLevel = null;
    protected $_hidden = null;
    protected $_selected = null;
    protected $_timezone = null;

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        return $element;
    }
    
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case Zend_Gdata_Data::lookupNamespace('gCal') . ':' . 'color';
            $color = new Zend_Gdata_Calendar_Extension_Color();
            $color->transferFromDOM($child);
            $this->_color = $color;
            break;
        case Zend_Gdata_Data::lookupNamespace('gCal') . ':' . 'accesslevel';
            $accessLevel = new Zend_Gdata_Calendar_Extension_AccessLevel();
            $accessLevel->transferFromDOM($child);
            $this->_accessLevel = $accessLevel;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

}
