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
 * Zend_Sniffs_PHP_LowercasePHPFunctionsSniff.
 *
 * Ensures all calls to inbuilt PHP functions are lowercase
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_PHP_LowercasePHPFunctionsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_ISSET,
                T_ECHO,
                T_PRINT,
                T_RETURN,
                T_BREAK,
                T_CONTINUE,
                T_EMPTY,
                T_EVAL,
                T_EXIT,
                T_LIST,
                T_UNSET,
                T_INCLUDE,
                T_INCLUDE_ONCE,
                T_REQUIRE,
                T_REQUIRE_ONCE,
                T_NEW,
                T_DECLARE,
                T_STRING
               );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_STRING) {
            $content = $tokens[$stackPtr]['content'];
            if ($content !== strtolower($content)) {
                $type     = strtoupper($content);
                $expected = strtolower($content);
                $error    = "$type keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                $phpcsFile->addError($error, $stackPtr);
            }

            return;
        }

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($next === false) {
            // Not a function call.
            return;
        }

        if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_FUNCTION) {
            // Function declaration, not a function call.
            return;
        }

        if ($tokens[$prev]['code'] === T_OBJECT_OPERATOR) {
            // Not an inbuilt function.
            return;
        }

        if ($tokens[$prev]['code'] === T_DOUBLE_COLON) {
            // Not an inbuilt function.
            return;
        }

        // Make sure it is an inbuilt PHP function.
        // PHP_CodeSniffer doesn't include/require any files, so no
        // user defined global functions can exist, except for
        // PHP_CodeSniffer ones.
        $content = $tokens[$stackPtr]['content'];
        if (function_exists($content) === false) {
            return;
        }

        if ($content !== strtolower($content)) {
            $expected = strtolower($content);
            $error    = "Calls to inbuilt PHP functions must be lowercase; expected \"$expected\" but found \"$content\"";
            $phpcsFile->addError($error, $stackPtr);
        }

    }

}
