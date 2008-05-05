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
if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * PHP_CodeSniffer_Standards_ZendClassCommentParser
 *
 * Parses Class doc comments
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
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
        $uses          = new PHP_CodeSniffer_CommentParser_SingleElement($this->previousElement, $tokens, 'uses', $this->phpcsFile);
        $this->_uses[] = $uses;
        return $uses;

    }//end parseLicense()


    /**
     * Returns the use of this class comment.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    public function getUses()
    {
        return $this->_uses;
    }

}//end class

?>
