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
 * Zend_Feed_EntryRss
 */
require_once 'Zend/Feed/EntryRss.php';


/**
 * The Zend_Feed_Rss object is a concrete subclass of Zend_Feed_Abstract meant
 * for representing RSS channels. It does not add any methods to its
 * parent, just provides a classname to check against with the
 * instanceof operator, and expects to be handling RSS-formatted data
 * instead of Atom.
 *
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Feed_Rss extends Zend_Feed_Abstract
{
    /**
     */
    protected $_entryClassName = 'Zend_Feed_EntryRss';

    /**
     */
    protected $_entryElementName = 'item';

    /**
     */
    protected $_defaultNamespace = 'rss';


    /**
     * Override Zend_Feed_Abstract to set up the $_element and $_entries aliases.
     */
    public function __wakeup()
    {
        parent::__wakeup();

        // Find the base feed element and create an alias to it.
        $this->_element = $this->_element->getElementsByTagName('channel')->item(0);
        if (!$this->_element) {
            throw new Zend_Feed_Exception('No root <channel> element found, cannot parse feed.');
        }

        // Find the entries and save a pointer to them for speed and
        // simplicity.
        $this->_buildEntryCache();
    }

}

