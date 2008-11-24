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
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Cell.php 12550 2008-11-11 15:26:47Z dasprid $
 */

/**
 * A table cell representation.
 *
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Text_Table_Cell
{
    /**
     * Left alignment
     *
     * @const string
     */
    const ALIGN_LEFT   = 'left';

    /**
     * Center alignment
     *
     * @const string
     */
    const ALIGN_CENTER = 'center';

    /**
     * Right alignment
     *
     * @const string
     */
    const ALIGN_RIGHT  = 'right';

    /**
     * Content of the cell
     *
     * @var string
     */
    protected $_content = null;

    /**
     * Alignment of the cell
     *
     * @var string
     */
    protected $_alignment = self::ALIGN_LEFT;

    /**
     * Column span of the cell
     *
     * @var integer
     */
    protected $_columnSpan = 1;
    
    /**
     * Allowed alignment parameters
     *
     * @var array
     */
    protected $_allowedAlignments = array(self::ALIGN_LEFT, self::ALIGN_CENTER, self::ALIGN_RIGHT);

    /**
     * Creates a cell for a Zend_Text_Table_Row object.
     *
     * @param string $content Cell content
     * @param string $alignment Alignment
     * @param integer $columnSpan Column span
     * @param string $encoding Character encoding
     */
    public function __construct($content = null, $alignment = null, $columnSpan = null, $encoding = null)
    {
        if ($content !== null) {
            $this->setContent($content, $encoding);
        }

        if ($alignment !== null) {
            $this->setAlignment($alignment);
        }

        if ($columnSpan !== null) {
            $this->setColumnSpan($columnSpan);
        }
    }

    /**
     * Sets the cell content.
     *
     * If $encoding is not defined, it is assumed that $content is encoded in
     * the encoding defined via Zend_Text_Table::setInputEncoding() (defaults
     * to utf-8).
     *
     * @param  string $content Content of the cell
     * @param  string $encoding The encoding of the content
     * @throws Zend_Text_Table_Exception When $content is not a string
     * @return Zend_Text_Table_Cell
     */
    public function setContent($content, $encoding = null)
    {
        if (!is_string($content)) {
            /**
             * @see Zend_Text_Table_Exception
             */
            require_once 'Zend/Text/Table/Exception.php';

            throw new Zend_Text_Table_Exception('Cell content must be a string');
        }

        if ($encoding === null) {
            $inputEncoding = Zend_Text_Table::getInputEncoding();
        } else {
            $inputEncoding = strtolower($encoding);
        }
        
        $outputEncoding = Zend_Text_Table::getOutputEncoding();
        
        if ($inputEncoding != $outputEncoding) {
            $content = iconv($inputEncoding, $outputEncoding, $content);
        }

        $this->_content = $content;

        return $this;
    }

    /**
     * Sets the alignment.
     *
     * @param  string $alignment Alignment
     * @throws Zend_Text_Table_Exception When supplied alignment is invalid
     * @return Zend_Text_Table_Cell
     */
    public function setAlignment($alignment)
    {
        if (!in_array($alignment, $this->_allowedAlignments)) {
            /**
             * @see Zend_Text_Table_Exception
             */
            require_once 'Zend/Text/Table/Exception.php';

            throw new Zend_Text_Table_Exception('Invalid alignment supplied');
        }

        $this->_alignment = $alignment;

        return $this;
    }

    /**
     * Sets the column span.
     *
     * @param  int $columnSpan
     * @throws Zend_Text_Table_Exception When $columnSpan is smaller than 1
     * @return Zend_Text_Table_Cell
     */
    public function setColumnSpan($columnSpan)
    {
        if (!is_integer($columnSpan) || $columnSpan < 1) {
            /**
             * @see Zend_Text_Table_Exception
             */
            require_once 'Zend/Text/Table/Exception.php';

            throw new Zend_Text_Table_Exception('Column span must be an integer and greater than 0');
        }

        $this->_columnSpan = $columnSpan;

        return $this;
    }

    /**
     * Returns the column span.
     *
     * @return integer
     */
    public function getColumnSpan()
    {
        return $this->_columnSpan;
    }

    /**
     * Renders the cell using the given column width.
     *
     * @param  integer $columnWidth Width of the column
     * @param  integer $padding Padding for the column
     * @throws Zend_Text_Table_Exception When $columnWidth is lower than 1
     * @throws Zend_Text_Table_Exception When padding is greater than columnWidth
     * @return string
     */
    public function render($columnWidth, $padding = 0)
    {
        if (!is_integer($columnWidth) || $columnWidth < 1) {
            /**
             * @see Zend_Text_Table_Exception
             */
            require_once 'Zend/Text/Table/Exception.php';

            throw new Zend_Text_Table_Exception('Column width must be an integer and greater than 0');
        }

        $lines  = explode("\n", wordwrap($this->_content, $columnWidth, "\n"));

        $paddedLines = array();
        foreach ($lines AS $line) {
            $paddedLines[] = str_repeat(' ', $padding)
                           . str_pad($line, $columnWidth, ' ', $this->_getPadType())
                           . str_repeat(' ', $padding);
        }

        $result = implode("\n", $paddedLines);

        return $result;
    }
    
    protected function _getPadType()
    {
        switch ($this->_alignment) {
            case self::ALIGN_LEFT:
                return STR_PAD_RIGHT;

            case self::ALIGN_CENTER:
                return STR_PAD_BOTH;

            case self::ALIGN_RIGHT:
                return STR_PAD_LEFT;

            default:
                return null;
        }
    }

    /**
     * Deprecated.  Use Zend_Text_Table_Cell::setAlignment() instead.
     *
     * @deprecated Since 1.7.1
     * @param      string $align Align of the column
     * @throws     Zend_Text_Table_Exception When supplied align is invalid
     * @return     Zend_Text_Table_Column
     */
    public function setAlign($align)
    {
        //trigger_error('setAlign() has been renamed setAlignment()', E_USER_NOTICE);

        return $this->setAlignment($align);
    }

    /**
     * Deprecated.  Use Zend_Text_Table_Cell::setColumnSpan() instead.
     *
     * @deprecated Since 1.7.1
     * @param      integer $colSpan
     * @throws     Zend_Text_Table_Exception When $colSpan is smaller than 1
     * @return     Zend_Text_Table_Column
     */
    public function setColSpan($colSpan)
    {
        //trigger_error('setColSpan() has been renamed setColumnSpan()', E_USER_NOTICE);

        return $this->setColumnSpan($colSpan);
    }

    /**
     * Deprecated.  Use Zend_Text_Table_Cell::getColumnSpan() instead.
     *
     * @deprecated Since 1.7.1
     * @param      integer $colSpan
     * @throws     Zend_Text_Table_Exception When $colSpan is smaller than 1
     * @return     Zend_Text_Table_Column
     */
    public function getColSpan()
    {
        //trigger_error('getColSpan() has been renamed getColumnSpan()', E_USER_NOTICE);

        return $this->getColumnSpan();
    }
}