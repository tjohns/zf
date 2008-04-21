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
 * Zend_Sniffs_ControlStructures_InlineControlStructureSniff
 *
 * Verifies that inline control statements are not present
 *
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Sniffs_ControlStructures_InlineControlStructureSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_IF,
                T_ELSE,
                T_FOREACH,
                T_WHILE,
                T_DO,
                T_SWITCH,
                T_FOR,
               );

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

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            // Ignore the ELSE in ELSE IF. We'll process the IF part later.
            if (($tokens[$stackPtr]['code'] === T_ELSE) && ($tokens[($stackPtr + 2)]['code'] === T_IF)) {
                return;
            }

            if ($tokens[$stackPtr]['code'] === T_WHILE) {
                // This could be from a DO WHILE, which doesn't have an opening brace.
                $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if ($tokens[$lastContent]['code'] === T_CLOSE_CURLY_BRACKET) {
                    $brace = $tokens[$lastContent];
                    if (isset($brace['scope_condition']) === true) {
                        $condition = $tokens[$brace['scope_condition']];
                        if ($condition['code'] === T_DO) {
                            return;
                        }
                    }
                }
            }

            // This is a control structure without an opening brace,
            // so it is an inline statement.
            $error = 'Inline control structures are not allowed.';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }//end if

    }//end process()


}//end class

?>
