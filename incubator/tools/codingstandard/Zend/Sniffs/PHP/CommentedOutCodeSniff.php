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
 * Zend_Sniffs_PHP_CommentedOutCodeSniff
 *
 * Warn about commented out code
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
class Zend_Sniffs_PHP_CommentedOutCodeSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * If a comment is more than $maxPercentage% code, a warning will be shown.
     *
     * @var int
     */
    protected $maxPercentage = 45;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$commentTokens;

    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Process whole comment blocks at once, so skip all but the first token.
        if ($tokens[$stackPtr]['code'] === $tokens[($stackPtr - 1)]['code']) {
            return;
        }

        $content = '<?php ';
        for ($i = $stackPtr; $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$stackPtr]['code'] !== $tokens[$i]['code']) {
                break;
            }

            /*
                Trim as much off the comment as possible so we don't
                have additional whitespace tokens or comment tokens
            */

            $tokenContent = trim($tokens[$i]['content']);

            if (substr($tokenContent, 0, 2) === '//') {
                $tokenContent = substr($tokenContent, 2);
            }

            if (substr($tokenContent, 0, 1) === '#') {
                $tokenContent = substr($tokenContent, 1);
            }

            if (substr($tokenContent, 0, 3) === '/**') {
                $tokenContent = substr($tokenContent, 3);
            }

            if (substr($tokenContent, 0, 2) === '/*') {
                $tokenContent = substr($tokenContent, 2);
            }

            if (substr($tokenContent, -2) === '*/') {
                $tokenContent = substr($tokenContent, 0, -2);
            }

            if (substr($tokenContent, 0, 1) === '*') {
                $tokenContent = substr($tokenContent, 1);
            }

            $content .= $tokenContent.$phpcsFile->eolChar;
        }

        $content = trim($content).' ?>';

        $stringTokens = PHP_CodeSniffer_File::tokenizeString($content);

        $emptyTokens = array(
                        T_WHITESPACE,
                        T_STRING,
                        T_STRING_CONCAT,
                        T_ENCAPSED_AND_WHITESPACE,
                       );

        $numTokens = count($stringTokens);

        /*
            We know what the first two and last two tokens should be
            (because we put them there) so ignore this comment if those
            tokens were not parsed correctly. It obvously means this is not
            valid code.
        */

        // First token is always the opening PHP tag.
        if ($stringTokens[0]['code'] !== T_OPEN_TAG) {
            return;
        }

        // Last token is always the closing PHP tag.
        if ($stringTokens[($numTokens - 1)]['code'] !== T_CLOSE_TAG) {
            return;
        }

        // Second last token is always whitespace or a comment, depending
        // on the code inside the comment.
        if (in_array($stringTokens[($numTokens - 2)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
            return;
        }

        $numComment = 0;
        $numCode    = 0;

        for ($i = 0; $i < $numTokens; $i++) {
            if (in_array($stringTokens[$i]['code'], $emptyTokens) === true) {
                // Looks like comment.
                $numComment++;
            } else {
                // Looks like code.
                $numCode++;
            }
        }

        // We subtract 3 from the token number so we ignore the start/end tokens
        // and their surrounding whitespace. We take 2 off the number of code
        // tokens so we ignore the start/end tokens.
        if ($numTokens > 3) {
            $numTokens -= 3;
        }

        if ($numCode >= 2) {
            $numCode -= 2;
        }

        $percentCode = ceil((($numCode / $numTokens) * 100));
        if ($percentCode > $this->maxPercentage) {
            // Just in case.
            $percentCode = min(100, $percentCode);

            $error = "This comment is ${percentCode}% valid code; is this commented out code?";
            $phpcsFile->addWarning($error, $stackPtr);
        }
    }

}