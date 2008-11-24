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
 * @version   $Id: Row.php 12529 2008-11-10 21:05:43Z dasprid $
 */

/**
 * @see Zend_Text_Table_Cell
 */
require_once 'Zend/Text/Table/Cell.php';

/**
 * Row class for Zend_Text_Table
 *
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Text_Table_Row
{
    /**
     * List of all cells
     *
     * @var array
     */
    protected $_cells = array();

    /**
     * Temporary stored column widths
     *
     * @var array
     */
    protected $_columnWidths = null;

    /**
     * Creates a new cell and append it to the row.
     *
     * @param  string $content
     * @param  array  $options 
     * @return Zend_Text_Table_Row
     */
    public function createCell($content, array $options = null)
    {
        $alignment  = null;
        $columnSpan = null;
        $encoding   = null;
        
        if ($options !== null) {
            extract($options, EXTR_IF_EXISTS);
        }
        
        $cell = new Zend_Text_Table_Cell($content, $alignment, $columnSpan, $encoding);
        $this->addCell($cell);
        
        return $this;
    }
    
    /**
     * Adds a cell to the row.
     *
     * @param  Zend_Text_Table_Cell $cell The cell to append to the row
     * @return Zend_Text_Table_Row
     */
    public function addCell(Zend_Text_Table_Cell $cell)
    {
        $this->_cells[] = $cell;
        
        return $this;
    }
    
    /**
     * Returns a cell by its index.
     * 
     * Returns null, when the index is out of range
     *
     * @param  integer $index
     * @return Zend_Text_Table_Cell|null
     */
    public function getCell($index)
    {
        if (!isset($this->_cells[$index])) {
            return null;
        }
        
        return $this->_cells[$index];
    }
    
    /**
     * Returns all cells of the row.
     *
     * @return array
     */
    public function getCells()
    {
        return $this->_cells;
    }

    /**
     * Returns the widths of all cells which were rendered last.
     *
     * @throws Zend_Text_Table_Exception When no cells were rendered yet
     * @return integer
     */
    public function getColumnWidths()
    {
        if ($this->_columnWidths === null) {
            /**
             * @see Zend_Text_Table_Exception
             */
            require_once 'Zend/Text/Table/Exception.php';

            throw new Zend_Text_Table_Exception('No cells were rendered yet');
        }

        return $this->_columnWidths;
    }

    /**
     * Renders the row.
     *
     * @param  array                               $columnWidths Width of all columns
     * @param  Zend_Text_Table_Border_Interface $border    Border for the row borders
     * @param  integer                             $padding      Padding for the columns
     * @throws Zend_Text_Table_Exception When there are too many cells 
     * @return string
     */
    public function render(array $columnWidths,
                           Zend_Text_Table_Border_Interface $border,
                           $padding = 0)
    {
        // Prepare an array to store all column widths
        $this->_columnWidths = array();

        // If there is no single cell, create a cell which spans over the
        // entire row
        if (count($this->_cells) < 1) {
            $this->addCell(new Zend_Text_Table_Cell(null, null, count($columnWidths)));
        }
        
        // First we have to render all cells, to get the maximum height
        $renderedCells = array();
        $maxHeight     = 0;
        $columnNumber  = 0;

        foreach ($this->_cells as $cell) {
            // Get the colspan of the cell
            $columnSpan = $cell->getColumnSpan();

            // Verify if there are enough column widths defined
            if (($columnNumber + $columnSpan) > count($columnWidths)) {
                /**
                 * @see Zend_Text_Table_Exception
                 */
                require_once 'Zend/Text/Table/Exception.php';

                throw new Zend_Text_Table_Exception('Too many cells');
            }

            // Calculate the column width
            $columnWidth = ($columnSpan - 1 + array_sum(array_slice($columnWidths,
                                                                    $columnNumber,
                                                                    $columnSpan)));

            // Render the cell and split it's lines into an array
            $result = explode("\n", $cell->render($columnWidth, $padding));

            // Store the width of the rendered cell
            $this->_columnWidths[] = $columnWidth;

            // Store the rendered cell and calculate the new max height
            $renderedCells[] = $result;
            $maxHeight       = max($maxHeight, count($result));

            // Set up the internal cell number
            $columnNumber += $columnSpan;
        }

        // If the row doesnt contain enough cells to fill the entire row, fill
        // it with an empty cell
        if ($columnNumber < count($columnWidths)) {
            $remainingWidth = (count($columnWidths) - $columnNumber - 1) +
                               array_sum(array_slice($columnWidths,
                                                     $columnNumber));
            $renderedCells[] = array(str_repeat(' ', $remainingWidth));

            $this->_columnWidths[] = $remainingWidth;
        }

        // Add each single cell line to the result
        $result = '';
        for ($line = 0; $line < $maxHeight; $line++) {
            $result .= $border->getVertical();

            foreach ($renderedCells as $renderedCell) {
                if (isset($renderedCell[$line])) {
                    $result .= $renderedCell[$line];
                } else {
                    $result .= str_repeat(' ', strlen($renderedCell[0]));
                }

                $result .= $border->getVertical();
            }

            $result .= "\n";
        }

        return $result;
    }

    /**
     * Deprecated.  Use createCell() instead.
     *
     * @deprecated Since 1.7.1
     * @param      string $content
     * @param      array  $options 
     * @return     Zend_Text_Table_Row
     */
    public function createColumn($content, array $options = null)
    {
        //trigger_error('createColumn() has been renamed createCell()', E_USER_NOTICE);

        return $this->createCell($content, $options);
    }

    /**
     * Deprecated.  Use addCell() instead.
     *
     * @deprecated Since 1.7.1
     * @param      Zend_Text_Table_Cell $cell The cell to append to the row
     * @return     Zend_Text_Table_Row
     */
    public function appendColumn(Zend_Text_Table_Cell $cell)
    {
        //trigger_error('appendColumn() has been renamed addCell()', E_USER_NOTICE);

        return $this->addCell($cell);
    }

    /**
     * Deprecated.  Use getCell() instead.
     *
     * @deprecated Since 1.7.1
     * @param      integer $index
     * @return     Zend_Text_Table_Cell|null
     */
    public function getColumn($index)
    {
        //trigger_error('getColumn() has been renamed getCell()', E_USER_NOTICE);

        return $this->getCell($index);
    }
    
    /**
     * Deprecated.  Use getCells() instead.
     *
     * @deprecated Since 1.7.1
     * @return     array
     */
    public function getColumns()
    {
        //trigger_error('getColumns() has been renamed getCells()', E_USER_NOTICE);

        return $this->getCells();
    }
}