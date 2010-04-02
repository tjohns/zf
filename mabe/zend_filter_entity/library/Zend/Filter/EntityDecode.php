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
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: HtmlEntities.php 16217 2009-06-21 19:39:00Z thomas $
 */

/** @see Zend_Filter_Interface */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_EntityDecode implements Zend_Filter_Interface
{

    const ONILLEGALCHAR_EXCEPTION  = 'exception';
    const ONILLEGALCHAR_TRANSLIT   = 'translit';
    const ONILLEGALCHAR_IGNORE     = 'ignore';
    const ONILLEGALCHAR_ENTITY     = 'entity';
    const ONILLEGALCHAR_SUBSTITUTE = 'substitute';

    /**
     * The Action if an entity can't convert to the given charset
     * (Value of Zend_Filter_EntityDecode::ONILLEGALCHAR_*)
     *
     * @var string
     */
    protected $_onIllegalChar = self::ONILLEGALCHAR_IGNORE;

    /**
     * Output character encoding
     *
     * @var string
     */
    protected $_charSet = 'ISO-8859-1';

    /**
     * entity reference.
     *
     * @var array
     */
    protected $_entityReference = null;

    /**
     * Don't decode entities of special chars.
     * (", &, <, >)
     *
     * @var bool
     */
    protected $_keepSpecial = false;

    /**
     * The substituting character used with constant ONILLEGALCHAR_SUBSTITUTE
     *
     * @var string
     */
    protected $_substitute = '?';

    /**
     * Sets filter options
     *
     * @param  integer|array $quoteStyle
     * @param  string  $charSet
     * @return void
     */
    public function __construct($options = array())
    {
        foreach ($options as $k => $v) {
            if (method_exists($this, 'set'.$k)) {
                $this->{'set'.$k}($v);
            }
        }
    }

    /**
     * Get entity reference.
     *
     * @return array
     */
    public function getEntityReference()
    {
        if ($this->_entityReference === null) {
            $this->setEntityReference(
                Zend_Filter_EntityEncode::$_entityReferences['xml']
                + Zend_Filter_EntityEncode::$_entityReferences['html']
            );
        }
        return $this->_entityReference;
    }

    /**
     * Set entity reference.
     *
     * @param array $entityReference
     * @return Zend_Filter_EntityDecode
     */
    public function setEntityReference(array $entityReference)
    {
        $this->_entityReference = $entityReference;
        return $this;
    }

    /**
     * Get the action which is done if an illegal character was detected.
     *
     * @return string The current action string
     */
    public function getOnIllegalChar()
    {
        return $this->_onIllegalChar;
    }

    /**
     * Set the action which is done if an illegal character was detected.
     *
     * @param string $action The action string to set or empty to get the current action.
     * @return Zend_Filter_EntityDecode Provides a fluent interface
     * @throws Zend_Filter_Exception If an unknown $action was given.
     */
    public function setOnIllegalChar($action)
    {
        $action = strtolower($action);
        if (!in_array($action, array('exception', 'translit', 'ignore', 'entity', 'substitute'))) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Unknown action "'.$action.'"');
        }
        $this->_onIllegalChar = $action;
        return $this;
    }

    /**
     * Returns the charSet option
     *
     * @return string
     */
    public function getCharSet()
    {
        return $this->_charSet;
    }

    /**
     * Sets the charSet option
     *
     * @param  string $charSet
     * @return Zend_Filter_EntityDecode Provides a fluent interface
     */
    public function setCharSet($charSet)
    {
        $this->_charSet = $charSet;
        return $this;
    }

    /**
     * Get keep special option.
     *
     * @return bool
     */
    public function getKeepSpecial()
    {
        return $this->_keepSpecial;
    }

    /**
     * Sets keep special option
     *
     * @param bool $flag
     * @return Zend_Filter_EntityDecode Provides a fluent interface
     */
    public function setKeepSpecial($flag)
    {
        $this->_keepSpecial = (bool)$flag;
        return $this;
    }

    /**
     * Set the substituting character.
     *
     * @param string $substitute
     */
    public function setSubstitute($substitute) {
        $this->_substitute = (string)$substitute;
    }

    /**
     * Get the substituting character.
     *
     * @return string
     */
    public function getSubstitute() {
        return $this->_substitute;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, converting characters to their corresponding HTML entity
     * equivalents where they exist
     *
     * @param  string $value
     * @return string
     */
    public function filter($text)
    {
        // decode hex entities
        $pattern = '/&#x([a-f0-9]+);/ui';
        $text = preg_replace_callback($pattern, array($this, '_filterHexEntityMatches'), $text);

        // decode numeric entities
        $pattern = '/&#[^x](\d+);/ui';
        $text = preg_replace_callback($pattern, array($this, '_filterNumEntityMatches'), $text);

        // prepare entity reference
        $entRef = $this->getEntityReference();

        // do not decode special entities
        if ($this->getKeepSpecial()) {
            unset(
                $entRef['amp'],
                $entRef['lt'],
                $entRef['gt'],
                $entRef['quot'],
                $entRef['apos']
            );
        }

        // decode entity values
        $entDecoder = clone $this;
        foreach ($entRef as $entName => &$entValue) {
            $entRefTmp = $entRef;
            unset($entRefTmp[$entName]);
            $entDecoder->setEntityReference($entRefTmp);
            $entValue = $entDecoder->filter($entValue);
        }

        $text = strtr($text, $entRef);
        return $text;
    }

    protected function _filterNumEntityMatches($matches) {
        $uniCode = (int)$matches[1];

        if ($this->getKeepSpecial()
          && ( $uniCode == 34 // "
            || $uniCode == 38 // &
            || $uniCode == 39 // '
            || $uniCode == 60 // <
            || $uniCode == 62 // >
          )
        ) {
            return $matches[0];
        }

        $char = $this->_convertUnicode($uniCode);
        if (!$char && $this->getOnIllegalChar() == self::ONILLEGALCHAR_ENTITY) {
            return $matches[0];
        }
        return $char;
    }

    protected function _filterHexEntityMatches($matches) {
        $uniCode = hexdec($matches[1]);

        if ($this->getKeepSpecial()
          && ( $uniCode == 34 // "
            || $uniCode == 38 // &
            || $uniCode == 39 // '
            || $uniCode == 60 // <
            || $uniCode == 62 // >
          )
        ) {
            // rewrite entity to output a lowercase "x"
            return '&#x'.dechex($uniCode).';';
        }

        $char = $this->_convertUnicode($uniCode);
        if (!$char && $this->getOnIllegalChar() == self::ONILLEGALCHAR_ENTITY) {
            // rewrite entity to output a lowercase "x"
            return '&#x'.dechex($uniCode).';';
        }
        return $char;
    }

    protected function _convertUnicode($uniCode) {
        if ($uniCode < 0x80) { // 1Byte
            $utf8Char = chr($uniCode);

        } elseif ($uniCode < 0x800) { // 2Byte
            $utf8Char = chr(0xC0 | $uniCode >> 6)
                      . chr(0x80 | $uniCode & 0x3F);

        } elseif ($uniCode < 0x10000) { // 3Byte
            $utf8Char = chr(0xE0 | $uniCode >> 12)
                      . chr(0x80 | $uniCode >> 6 & 0x3F)
                      . chr(0x80 | $uniCode & 0x3F);

        } elseif ($uniCode < 0x110000) { // 4Byte
            $utf8Char  = chr(0xF0 | $uniCode >> 18)
                       . chr(0x80 | $uniCode >> 12 & 0x3F)
                       . chr(0x80 | $uniCode >> 6 & 0x3F)
                       . chr(0x80 | $uniCode & 0x3F);
        } else {
            if ($this->getOnIllegalChar() == self::ONILLEGALCHAR_EXCEPTION) {
                throw new Zend_Filter_Exception('Unsupported unicode number found "'.$uniCode.'"');
            } elseif ($this->getOnIllegalChar() == self::ONILLEGALCHAR_SUBSTITUTE) {
                return $this->getSubstitute();
            }
            return '';
        }

        $char = $this->_convertChar($utf8Char, 'UTF-8', $this->getCharSet());
        if (!$char && $this->getOnIllegalChar() == self::ONILLEGALCHAR_EXCEPTION) {
            throw new Zend_Filter_Exception('Can\'t convert "'.$utf8Char.'" (UTF-8) to '.$this->getCharSet());
        }

        return $char;
    }

    protected function _convertChar($in, $from, $to) {
        if ($from == $to) {
            return $in;
        }

        $iconvTo = $to.'//IGNORE';
        $onIllegalChar = $this->getOnIllegalChar();
        if ($onIllegalChar == self::ONILLEGALCHAR_TRANSLIT) {
            $iconvTo = $to.'//TRANSLIT';
        }

        $char = iconv($from, $iconvTo, $in);
        if (!$char) {
            if ( $onIllegalChar == self::ONILLEGALCHAR_SUBSTITUTE
              || $onIllegalChar == self::ONILLEGALCHAR_TRANSLIT ) {
                $char = $this->getSubstitute();
            } else {
                $char = '';
            }
        }

        return $char;
    }

}
