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
 * Zend_Sniffs_Strings_ConcatenationSpacingSniff
 *
 * Makes sure there are no spaces between the concatenation operator (.) and
 * the strings being concatenated
 *
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Sniffs_Strings_ConcatenationSpacingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING_CONCAT);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $found    = '';
        $expected = '';
        $error    = false;

        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
            $error     = true;
        }
        $expected .= trim(substr($tokens[($stackPtr - 1)]['content'], -5)) . " " . $tokens[$stackPtr]['content'];
        $found    .= substr($tokens[($stackPtr - 1)]['content'], -5) . $tokens[$stackPtr]['content'];

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $error     = true;
        }
        $expected .= " " . trim(substr($tokens[($stackPtr + 1)]['content'], 0, 5));
        $found    .= substr($tokens[($stackPtr + 1)]['content'], 0, 6);

        if ($error === true) {
            $found    = str_replace("\r\n", '\n', $found);
            $found    = str_replace("\n", '\n', $found);
            $found    = str_replace("\r", '\n', $found);
            $expected = str_replace("\r\n", '\n', $expected);
            $expected = str_replace("\n", '\n', $expected);
            $expected = str_replace("\r", '\n', $expected);

            $message = "Concat operator must be surrounded by one space. Found \"$found\"; expected \"$expected\"";
            $phpcsFile->addError($message, $stackPtr);
        }

    }

}