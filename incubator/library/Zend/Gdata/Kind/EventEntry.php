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
 * @package    Zend_Gdata_Kind
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_Entry
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_App_Data
 */
require_once 'Zend/Gdata/App/Data.php';

/**
 * @see Zend_Gdata_App_Extension
 */
require_once 'Zend/Gdata/App/Extension.php';

/**
 * @see Zend_Gdata_Extension_Where
 */
require_once 'Zend/Gdata/Extension/Where.php';

/**
 * @see Zend_Gdata_Extension_When
 */
require_once 'Zend/Gdata/Extension/When.php';

/**
 * @see Zend_Gdata_Extension_Recurrence
 */
require_once 'Zend/Gdata/Extension/Recurrence.php';

/**
 * Data model for the GData Event "Kind".  Google Calendar has a separate 
 * EventEntry class which extends this.
 *
 * @category   Zend
 * @package    Zend_Gdata_Kind
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Kind_EventEntry extends Zend_Gdata_Entry
{

    protected $_when = array();
    protected $_where = array();
    protected $_recurrence = null; 
    protected $_eventStatus = null;
  
    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_when != null) {
            foreach ($this->_when as $when) {
                $element->appendChild($when->getDOM($element->ownerDocument));
            }
        }
        if ($this->_where != null) {
            foreach ($this->_where as $where) {
                $element->appendChild($where->getDOM($element->ownerDocument));
            }
        }
        if ($this->_recurrence != null) {
            $element->appendChild($this->_recurrence->getDOM($element->ownerDocument));
        }
        if ($this->_eventStatus != null) {
            $element->appendChild($this->_eventStatus->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gd') . ':' . 'where';
            $where = new Zend_Gdata_Extension_Where();
            $where->transferFromDOM($child);
            $this->_where[] = $where;
            break;
        case $this->lookupNamespace('gd') . ':' . 'when';
            $when = new Zend_Gdata_Extension_When();
            $when->transferFromDOM($child);
            $this->_when[] = $when;
            break;
        case $this->lookupNamespace('gd') . ':' . 'recurrence';
            $recurrence = new Zend_Gdata_Extension_Recurrence();
            $recurrence->transferFromDOM($child);
            $this->_recurrence = $recurrence;
            break;
        case $this->lookupNamespace('gd') . ':' . 'eventStatus';
            $eventStatus = new Zend_Gdata_Extension_EventStatus();
            $eventStatus->transferFromDOM($child);
            $this->_eventStatus = $eventStatus;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    public function getWhen()
    {
        return $this->_when;
    } 

    /**
     * @param array $value
     * @return Zend_Gdata_Extension_EventEntry Provides a fluent interface
     */
    public function setWhen($value)
    {
        $this->_when = $value;
        return $this;
    }

    public function getWhere()
    {
        return $this->_where;
    } 

    /**
     * @param array $value
     * @return Zend_Gdata_Extension_EventEntry Provides a fluent interface
     */
    public function setWhere($value)
    {
        $this->_where = $value;
        return $this;
    }

    public function getRecurrence()
    {
        return $this->_recurrence;
    } 

    /**
     * @param array $value
     * @return Zend_Gdata_Extension_EventEntry Provides a fluent interface
     */
    public function setRecurrence($value)
    {
        $this->_recurrence = $value;
        return $this;
    }

}
