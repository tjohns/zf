<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Feed_Abstract
 */
require_once 'Zend/Feed/Abstract.php';

/**
 * Zend_Feed_EntryAtom
 */
require_once 'Zend/Feed/EntryAtom.php';


/**
 * The Zend_Feed_Atom class is a concrete subclass of the general
 * Zend_Feed_Abstract class, tailored for representing an Atom feed. It shares
 * all of the same methods with its abstract parent. The distinction
 * is made in the format of data that Zend_Feed_Atom expects, and as a
 * further pointer for users as to what kind of feed object they have
 * been passed.
 *
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Feed_Atom extends Zend_Feed_Abstract
{

    /**
     */
    protected $_entryClassName = 'Zend_Feed_EntryAtom';

    /**
     */
    protected $_entryElementName = 'entry';

    /**
     */
    protected $_defaultNamespace = 'atom';


    /**
     * Override Zend_Feed_Abstract to set up the $_element and $_entries aliases.
     */
    public function __wakeup()
    {
        parent::__wakeup();

        // Find the base feed element and create an alias to it.
        $element = $this->_element->getElementsByTagName('feed')->item(0);
        if (!$element) {
            // Try to find a single <entry> instead.
            $element = $this->_element->getElementsByTagName($this->_entryElementName)->item(0);
            if (!$element) {
                throw new Zend_Feed_Exception('No root <feed> or <' . $this->_entryElementName
                                            . '> element found, cannot parse feed.');
            }

            $doc = new DOMDocument($this->_element->version,
                                   $this->_element->actualEncoding);
            $feed = $doc->appendChild($doc->createElement('feed'));
            $feed->appendChild($doc->importNode($element, true));
            $element = $feed;
        }

        $this->_element = $element;

        // Find the entries and save a pointer to them for speed and
        // simplicity.
        $this->_buildEntryCache();
    }

}

