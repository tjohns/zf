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
 * @package    ZendFramework_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * ZendFramework_Sniffs_NamingConventions_ValidClassNameSniff.
 *
 * Ensures class and interface names start with a capital letter
 * and use _ separators.
 *
 * @category   Zend
 * @package    ZendFramework_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class ZendFramework_Sniffs_NamingConventions_ValidClassNameSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE
               );
    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being processed.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $className = $phpcsFile->findNext(T_STRING, $stackPtr);
        $name      = trim($tokens[$className]['content']);

        // Make sure the first letter is a capital.
        if (preg_match('|^[A-Z]|', $name) === 0) {
            $error = ucfirst($tokens[$stackPtr]['content']).' name must begin with a capital letter';
            $phpcsFile->addError($error, $stackPtr);
        }

        // Check that each new word starts with a capital as well, but don't
        // check the first word, as it is checked above.
        $validName = true;
        $nameBits  = explode('_', $name);
        $firstBit  = array_shift($nameBits);
        foreach ($nameBits as $bit) {
            if ($bit === '' || $bit{0} !== strtoupper($bit{0})) {
                $validName = false;
                break;
            }
        }

        if ($validName !== true) {
            // Strip underscores because they cause the suggested name
            // to be incorrect.
            $nameBits = explode('_', trim($name, '_'));
            $firstBit = array_shift($nameBits);

            $newName = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
            foreach ($nameBits as $bit) {
                $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
            }

            $newName = rtrim($newName, '_');
            $error   = ucfirst($tokens[$stackPtr]['content'])." name is not valid; consider $newName instead";
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()

}//end class