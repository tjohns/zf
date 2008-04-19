<?php
/**
 * Parses Class doc comments.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ClassCommentParser.php,v 1.11 2007/11/30 01:18:41 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses Class doc comments.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.0.1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_ZendClassCommentParser extends PHP_CodeSniffer_CommentParser_ClassCommentParser
{

    /**
     * The package element of this class.
     *
     * @var SingleElement
     */
    private $_uses = null;


    /**
     * Returns the allowed tags withing a class comment.
     *
     * @return array(string => int)
     */
    protected function getAllowedTags()
    {
        return array(
                'category'   => false,
                'package'    => true,
                'subpackage' => true,
                'author'     => false,
                'uses'       => false,
                'copyright'  => true,
                'license'    => false,
                'version'    => true,
               );

    }//end getAllowedTags()


    /**
     * Parses the uses tag of this class comment.
     *
     * @param array $tokens The tokens that comprise this tag.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    protected function parseUses($tokens)
    {
        $this->_uses = new PHP_CodeSniffer_CommentParser_PairElement($this->previousElement, $tokens, 'uses', $this->phpcsFile);
        return $this->_uses;

    }//end parseLicense()


    /**
     * Returns the use of this class comment.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    public function getUses()
    {
        return $this->_uses;
    }//end getLicense()

}//end class

?>
