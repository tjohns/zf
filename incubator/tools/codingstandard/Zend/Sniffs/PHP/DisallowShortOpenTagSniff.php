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
 * Zend_Sniffs_PHP_DisallowShortOpenTagSniff.
 *
 * Makes sure that shorthand PHP open tags are not used.
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Sniffs_PHP_DisallowShortOpenTagSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_TAG,
                T_OPEN_TAG_WITH_ECHO
               );
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
        // If short open tags are off, then any short open tags will be converted
        // to inline_html tags so we can just ignore them.
        // If its on, then we want to ban the use of them.
        $option = ini_get('short_open_tag');

        // Ini_get returns a string "0" if short open tags is off.
        if ($option === '0') {
            return;
        }

        $tokens  = $phpcsFile->getTokens();
        $openTag = $tokens[$stackPtr];

        if ($openTag['content'] === '<?') {
            $error = 'Short PHP opening tag used. Found "'.$openTag['content'].'" Expected "<?php".';
            $phpcsFile->addError($error, $stackPtr);
        }
    }

}
