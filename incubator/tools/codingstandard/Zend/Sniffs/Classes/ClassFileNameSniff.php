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
 * Zend_Sniffs_Classes_ClassFileNameSniff
 *
 * Tests that the file name and the name of the class contained within the file
 * match.
 *
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Sniffs_Classes_ClassFileNameSniff implements PHP_CodeSniffer_Sniff
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
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens   = $phpcsFile->getTokens();
        $decName  = $phpcsFile->findNext(T_STRING, $stackPtr);
        $fullPath = dirname($phpcsFile->getFilename());
        $fullPath = substr($fullPath, strpos($fullPath, DIRECTORY_SEPARATOR.'Zend'.DIRECTORY_SEPARATOR) + 1);
        $fullPath = str_replace(DIRECTORY_SEPARATOR, '_', $fullPath) . "_";
        $fullPath .= basename($phpcsFile->getFilename());
        $fileName = substr($fullPath, 0, strrpos($fullPath, '.'));

        if ($tokens[$decName]['content'] !== $fileName) {
            $error  = ucfirst($tokens[$stackPtr]['content']);
            $error .= ' name doesn\'t match filename. Expected ';
            $error .= '"'.$tokens[$stackPtr]['content'].' ';
            $error .= $fileName.'".';
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()

}//end class