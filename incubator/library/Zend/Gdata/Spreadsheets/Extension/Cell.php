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
 * @version    $Id: Entry.php 3941 2007-03-14 21:36:13Z darby $
 */

/**
 * @see Zend_Gdata_Entry
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_Data
 */
require_once 'Zend/Gdata/Data.php';

/**
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';


/**
 * Concrete class for working with cell elements.
 *
 * @category   Zend
 * @package    Zend_Gdata_Spreadsheets
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Spreadsheets_Extension_Cell extends Zend_Gdata_Extension
{
    protected $_rootElement = 'cell';
    protected $_rootNamespace = 'gs';
    protected $_row = null;
    protected $_col = null;
    protected $_inputValue = null;

    public function __construct($row = null, $col = null, $inputValue = null) 
    {
        parent::__construct();
        $this->_row = $row; 
        $this->_col = $col; 
        $this->_inputValue = $inputValue; 
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        $element->setAttribute('row', $this->_row);
        $element->setAttribute('col', $this->_col);
        $element->setAttribute('inputValue', $this->_inputValue);
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'row':
            $this->_row = $attribute->nodeValue;
            break;
        case 'col':
            $this->_col = $attribute->nodeValue;
            break;
        case 'inputValue':
            $this->_inputValue = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    public function getRow()
    {
        return $this->_row;
    }
    
    public function getColumn()
    {
        return $this->_col;
    }
    
    public function getInputValue() 
    {
        return $this->_inputValue;
    }
    
    public function setRow($row) 
    { 
        $this->_row = $row;
        return $this;
    }
    
    public function setColumn($col) 
    { 
        $this->_col = $col;
        return $this;
    }
    
    public function setInputValue($inputValue) 
    { 
        $this->_inputValue = $inputValue;
        return $this;
    }
    
}
