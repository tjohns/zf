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
 * Zend_Sniffs_Functions_FunctionCallSignatureSniff
 *
 * Checks for the right spacing in fuction declaration
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_Functions_FunctionCallSignatureSniff implements PHP_CodeSniffer_Sniff
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

        // Find the next non-empty token.
        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        if (isset($tokens[$next]['parenthesis_closer']) === false) {
            // Not a function call.
            return;
        }

        // Find the previous non-empty token.
        $previous = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($tokens[$previous]['code'] === T_FUNCTION) {
            // It's a function definition, not a function call.
            return;
        }

        if ($tokens[$previous]['code'] === T_NEW) {
            // We are creating an object, not calling a function.
            return;
        }

        if (($stackPtr + 1) !== $next) {
            // Checking this: $value = my_function[*](...).
            $error = 'Space before opening parenthesis of function call prohibited';
            $phpcsFile->addError($error, $stackPtr);
        }

        if ($tokens[($next + 1)]['code'] === T_WHITESPACE) {
            // Checking this: $value = my_function([*]...).
            $error = 'Space after opening parenthesis of function call prohibited';
            $phpcsFile->addError($error, $stackPtr);
        }

        $closer = $tokens[$next]['parenthesis_closer'];

        if ($tokens[($closer - 1)]['code'] === T_WHITESPACE) {
            // Checking this: $value = my_function(...[*]).
            $between = $phpcsFile->findNext(T_WHITESPACE, ($next + 1), null, true);

            // Only throw an error if there is some content between the parenthesis.
            // IE. Checking for this: $value = my_function().
            // If there is no content, then we would have thrown an error in the
            // previous IF statement because it would look like this:
            // $value = my_function( ).
            if ($between !== $closer) {
                $error = 'Space before closing parenthesis of function call prohibited';
                $phpcsFile->addError($error, $closer);
            }
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($closer + 1), null, true);

        if ($tokens[$next]['code'] === T_SEMICOLON) {
            if (in_array($tokens[($closer + 1)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                $error = 'Space after closing parenthesis of function call prohibited';
                $phpcsFile->addError($error, $closer);
            }
        }
    }

}
