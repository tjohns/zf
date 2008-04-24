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
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */

/**
 * Zend_Sniffs_Formatting_OutputBufferingIndentSniff
 *
 * Checks the indenting used when an ob_start() call occurs
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
class Zend_Sniffs_Formatting_OutputBufferingIndentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    }


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

        if ($tokens[$stackPtr]['content'] !== 'ob_start') {
            return;
        }

        $bufferEnd = $stackPtr;
        while (($bufferEnd = $phpcsFile->findNext(array(T_STRING, T_FUNCTION), ($bufferEnd + 1), null, false)) !== false) {
            if ($tokens[$bufferEnd]['code'] === T_FUNCTION) {
                // We should not cross funtions or move into functions.
                $bufferEnd = false;
                break;
            }

            $stringContent = $tokens[$bufferEnd]['content'];
            if (($stringContent === 'ob_end_clean') || ($stringContent === 'ob_end_flush')) {
                break;
            }

            if (($stringContent === 'ob_get_clean') || ($stringContent === 'ob_get_flush')) {
                // Generate the error because the functions are not allowed, but
                // continue to check the indentation.
                $phpcsFile->addError('Output buffering must be closed using ob_end_clean or ob_end_flush', $bufferEnd);
                break;
            }
        }

        if ($bufferEnd === false) {
            $phpcsFile->addError('Output buffering, started here, was never stopped', $stackPtr);
            return;
        }

        $requiredIndent = ($tokens[$stackPtr]['column'] + 3);

        for ($stackPtr; $stackPtr < $bufferEnd; $stackPtr++) {
            if (strpos($tokens[$stackPtr]['content'], $phpcsFile->eolChar) === false) {
                continue;
            }

            $nextContent = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), $bufferEnd, true);
            if ($tokens[$nextContent]['line'] !== ($tokens[$stackPtr]['line'] + 1)) {
                // Empty line.
                continue;
            }

            // The line has content, now if it is less than the required indent, throw error.
            $foundIndent = ($tokens[$nextContent]['column'] - 1);
            if ($foundIndent < $requiredIndent) {
                $error = "Buffered line not indented correctly. Expected at least $requiredIndent spaces; found $foundIndent.";
                $phpcsFile->addError($error, $nextContent);
            }
        }
    }

}
