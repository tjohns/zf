<?php
/**
 * Zend Framework Coding Standard
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
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

if (class_exists('Generic_Sniffs_Files_LineLengthSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Files_LineLengthSniff not found');
}

/**
 * Zend_Sniffs_Files_LineLengthSniff
 *
 * Checks all lines in the file, and throws warnings if they are over 100
 * characters in length and errors if they are over 120.
 *
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Sniffs_Files_LineLengthSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The limit that the length of a line should not exceed.
     *
     * @var int
     */
    protected $lineLimit = 100;

    /**
     * The limit that the length of a line must not exceed.
     *
     * Set to zero (0) to disable.
     *
     * @var int
     */
    protected $absoluteLineLimit = 120;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure this is the first open tag.
        $previousOpenTag = $phpcsFile->findPrevious(array(T_OPEN_TAG), ($stackPtr - 1));
        if ($previousOpenTag !== false) {
            return;
        }

        $tokenCount         = 0;
        $currentLineContent = '';
        $currentLine        = 1;

        for (; $tokenCount < $phpcsFile->numTokens; $tokenCount++) {
            if ($tokens[$tokenCount]['line'] === $currentLine) {
                $currentLineContent .= $tokens[$tokenCount]['content'];
            } else {
                $currentLineContent = trim($currentLineContent, $phpcsFile->eolChar);

                // If the content is a CVS or SVN id in a version tag, or it is
                // a license tag with a name and URL, there is nothing the
                // developer can do to shorten the line, so don't throw errors.
                // Also the copyright tag should be only on one line and will be ignored
                if (preg_match('|@version[^\$]+\$Id|', $currentLineContent) === 0 &&
                    preg_match('|@license|', $currentLineContent) === 0 &&
                    preg_match('|@copyright|', $currentLineContent) === 0) {
                    $lineLength = strlen($currentLineContent);
                    if ($this->absoluteLineLimit > 0 && $lineLength > $this->absoluteLineLimit) {
                        $error = 'Line exceeds maximum limit of '.$this->absoluteLineLimit." characters; contains $lineLength characters";
                        $phpcsFile->addError($error, ($tokenCount - 1));
                    } else if ($lineLength > $this->lineLimit) {
                        $warning = 'Line exceeds '.$this->lineLimit." characters; contains $lineLength characters";
                        $phpcsFile->addWarning($warning, ($tokenCount - 1));
                    }
                }

                $currentLineContent = $tokens[$tokenCount]['content'];
                $currentLine++;
            }
        }

    }//end process()

}//end class