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
if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * ZendFramework_Sniffs_Classes_SelfMemberReferenceSniff
 *
 * Tests self member references
 *
 * @category   Zend
 * @package    ZendFramework_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class ZendFramework_Sniffs_Classes_SelfMemberReferenceSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * Constructs a Squiz_Sniffs_Classes_SelfMemberReferenceSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_DOUBLE_COLON));
    }//end __construct()

    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $className = ($stackPtr - 1);
        if ($tokens[$className]['code'] === T_SELF) {
            if (strtolower($tokens[$className]['content']) !== $tokens[$className]['content']) {
                $error = 'Must use "self::" for local static member reference; found "'.$tokens[$className]['content'].'::"';
                $phpcsFile->addError($error, $className);
                return;
            }
        } else if ($tokens[$className]['code'] === T_STRING) {
            // Make sure this is another class reference.
            $declarationName = $phpcsFile->getDeclarationName($currScope);
            if ($declarationName === $tokens[$className]['content']) {
                // Class name is the same as the current class.
                $error = 'Must use "self::" for local static member reference';
                $phpcsFile->addError($error, $className);
                return;
            }
        }

        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $found = strlen($tokens[($stackPtr - 1)]['content']);
            $error = "Expected 0 spaces before double colon; $found found";
            $phpcsFile->addError($error, $className);
        }

        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $found = strlen($tokens[($stackPtr + 1)]['content']);
            $error = "Expected 0 spaces after double colon; $found found";
            $phpcsFile->addError($error, $className);
        }

    }//end processTokenWithinScope()

}//end class