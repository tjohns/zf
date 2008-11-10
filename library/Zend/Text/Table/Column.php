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
 * @version   $Id$
 */

/**
 * Column class for Zend_Text_Table_Row
 *
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Text_Table_Column
{
    /**
     * Aligns for columns
     */
    const ALIGN_LEFT   = 'left';
    const ALIGN_CENTER = 'center';
    const ALIGN_RIGHT  = 'right';

    /**
     * Content of the column
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Align of the column
     *
     * @var string
     */
    protected $_align = self::ALIGN_LEFT;

    /**
     * Colspan of the column
     *
     * @var integer
     */
    protected $_colSpan = 1;

    /**
     * Create a column for a Zend_Text_Table_Row object.
     *
     * @param string  $content  The content of the column
     * @param string  $align    The align of the content
     * @param integer $colSpan  The colspan of the column
     * @param string  $encoding The encoding of the content
     */
    public function __construct($content = null, $align = null, $colSpan = null, $encoding = null)
    {
        if ($content !== null) {
            $this->setContent($content, $encoding);
        }

        if ($align !== null) {
            $this->setAlign($align);
        }

        if ($colSpan !== null) {
            $this->setColSpan($colSpan);
        }
    }

    /**
     * Set the content.
     *
     * If $encoding is not defined, it is assumed that $content is UTF-8
     * encoded.
     *
     * @param  string $content  Content of the column
     * @param  string $encoding The Encoding of the content
     * @throws InvalidArgumentException When $content is not a string
     * @return Zend_Text_Table_Column
     */
    public function setContent($content, $encoding = 'UTF-8')
    {
        if (is_string($content) === false) {
            throw new InvalidArgumentException('$content must be a string');
        }

        if ($encoding !== 'UTF-8') {
            $content = iconv($encoding, 'UTF-8', $content);
        }

        $this->_content = $content;

        return $this;
    }

    /**
     * Set the align
     *
     * @param  string $align Align of the column
     * @throws InvalidArgumentException When supplied align is invalid
     * @return Zend_Text_Table_Column
     */
    public function setAlign($align)
    {
        if (in_array($align, array(self::ALIGN_LEFT,
                                   self::ALIGN_CENTER,
                                   self::ALIGN_RIGHT)) === false) {
            throw new InvalidArgumentException('Invalid align supplied');
        }

        $this->_align = $align;

        return $this;
    }

    /**
     * Set the colspan
     *
     * @param  int $colSpan
     * @throws InvalidArgumentException When $colSpan is smaller than 1
     * @return Zend_Text_Table_Column
     */
    public function setColSpan($colSpan)
    {
        if (is_int($colSpan) === false or $colSpan < 1) {
            throw new InvalidArgumentException('$colSpan must be an integer and greater than 0');
        }

        $this->_colSpan = $colSpan;

        return $this;
    }

    /**
     * Get the colspan
     *
     * @return integer
     */
    public function getColSpan()
    {
        return $this->_colSpan;
    }

    /**
     * Render the column width the given column width
     *
     * @param  integer $columnWidth The width of the column
     * @throws InvalidArgumentException When $columnWidth is lower than 1
     * @return string
     */
    public function render($columnWidth)
    {
        if (is_int($columnWidth) === false or $columnWidth < 1) {
            throw new InvalidArgumentException('$columnWidth must be an integer and greater than 0');
        }

        switch ($this->_align) {
            case self::ALIGN_LEFT:
                $padding = STR_PAD_RIGHT;
                break;

            case self::ALIGN_CENTER:
                $padding = STR_PAD_BOTH;
                break;

            case self::ALIGN_RIGHT:
                $padding = STR_PAD_LEFT;
                break;

            default:
                // This can never happen, but the CS tells I have to have it ...
                break;
        }

        $lines       = explode("\n", wordwrap($this->_content, $columnWidth, "\n"));
        $paddedLines = array();

        foreach ($lines AS $line) {
            $paddedLines[] = str_pad($line, $columnWidth, ' ', $padding);
        }

        $result = implode("\n", $paddedLines);

        return $result;

    }
}
