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

/**
 * Zend_Sniffs_Arrays_ArrayBracketSpacingSniff
 *
 * Ensure that there are no spaces around square brackets
 *
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Sniffs_Arrays_ArrayBracketSpacingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_SQUARE_BRACKET,
                T_CLOSE_SQUARE_BRACKET
               );
    }

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // No bracket can have space before them.
        $prevType = $tokens[($stackPtr - 1)]['code'];
        if (in_array($prevType, PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
            $nonSpace = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens,
                                                 ($stackPtr - 2), null, true);
            $expected = $tokens[$nonSpace]['content'] . $tokens[$stackPtr]['content'];
            $found    = $phpcsFile->getTokensAsString($nonSpace, ($stackPtr - $nonSpace))
                      . $tokens[$stackPtr]['content'];
            $error    = 'Space found before square bracket; '
                      . "expected \"$expected\" but found \"$found\"";
            $phpcsFile->addError($error, $stackPtr);
        }

        if ($tokens[$stackPtr]['type'] === 'T_OPEN_SQUARE_BRACKET') {
            // Open brackets can't have spaces on after them either.
            $nextType = $tokens[($stackPtr + 1)]['code'];
            if (in_array($nextType, PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                $nonSpace = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens,
                                                 ($stackPtr + 2), null, true);
                $expected = $tokens[$stackPtr]['content'] . $tokens[$nonSpace]['content'];
                $found    = $phpcsFile->getTokensAsString($stackPtr, ($nonSpace - $stackPtr + 1));
                $error    = 'Space found after square bracket; '
                          . "expected \"$expected\" but found \"$found\"";
                $phpcsFile->addError($error, $stackPtr);
            }
        }

    }

}