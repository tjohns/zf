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
 * @category     Zend
 * @package        Zend_Gdata
 * @copyright    Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd         New BSD License
 * @version        $Id: Entry.php 3941 2007-03-14 21:36:13Z darby $
 */


/**
 * @see Zend_Gdata_Entry
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_Spreadsheets_Extension_RowCount
 */
require_once 'Zend/Gdata/Spreadsheets/Extension/RowCount.php';

/**
 * @see Zend_Gdata_Spreadsheets_Extension_ColCount
 */
require_once 'Zend/Gdata/Spreadsheets/Extension/ColCount.php';


/**
 * Concrete class for working with Worksheet entries.
 *
 * @category     Zend
 * @package        Zend_Gdata_Spreadsheets
 * @copyright    Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd         New BSD License
 */
class Zend_Gdata_Spreadsheets_WorksheetEntry extends Zend_Gdata_Entry
{

    protected $_entryClassName = 'Zend_Gdata_Spreadsheets_WorksheetEntry';
    
    protected $_rowCount = null;
    protected $_colCount = null;
    
    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_rowCount != null) {
            $element->appendChild($this->_rowCount->getDOM($element->ownerDocument));
        }
        if ($this->_colCount != null) {
            $element->appendChild($this->_colCount->getDOM($element->ownerDocument));
        }
        return $element;
    }
    
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case Zend_Gdata_App_Data::lookupNamespace('gs') . ':' . 'rowCount';
                $rowCount = new Zend_Gdata_Spreadsheets_Extension_RowCount();
                $rowCount->transferFromDOM($child);
                $this->_rowCount = $rowCount;
                break;
            case Zend_Gdata_App_Data::lookupNamespace('gs') . ':' . 'colCount';
                $colCount = new Zend_Gdata_Spreadsheets_Extension_ColCount();
                $colCount->transferFromDOM($child);
                $this->_colCount = $colCount;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }
        
    public function getRowCount()
    {
        return $this->_rowCount;
    }
    
    public function getColumnCount()
    {
        return $this->_colCount;
    }
    
    public function setRowCount($rowCount)
    {
        $this->_rowCount = $rowCount;
        return $this;
    }
    
    public function setColumnCount($colCount)
    {
        $this->_colCount = $colCount;
        return $this;
    }
    
    public function issetRowCount()
    {
        return isset($this->_rowCount);
    }
    
    public function issetColumnCount()
    {
        return isset($this->_colCount);
    }
    
    public function unsetRowCount()
    {
        $this->_rowCount = null;
        return $this;
    }
    
    public function unsetColumnCount()
    {
        $this->_colCount = null;
        return $this;
    }

}