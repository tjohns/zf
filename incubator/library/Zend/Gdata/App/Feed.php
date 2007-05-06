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
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_App_Entry
 */
require_once 'Zend/Gdata/App/Entry.php';

/**
 * @see Zend_Gdata_App_FeedEntryParent
 */
require_once 'Zend/Gdata/App/FeedEntryParent.php';

/**
 * Atom feed class
 *
 * @category   Zend
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App_Feed extends Zend_Gdata_App_FeedEntryParent implements Iterator 
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend_Gdata_App_Entry';

    /**
     * Root XML element for Atom entries.
     *
     * @var string
     */
    protected $_rootElement = 'feed';

    /**
     * Cache of feed entries.
     *
     * @var array
     */
    protected $_entry = array();

    /**
     * Current location in $_entry array
     *
     * @var int
     */
    protected $_entryIndex = 0;


    /**
     * Make accessing some individual elements of the feed easier.
     *
     * Special accessors 'entry' and 'entries' are provided so that if
     * you wish to iterate over an Atom feed's entries, you can do so
     * using foreach ($feed->entries as $entry) or foreach
     * ($feed->entry as $entry).
     *
     * @param  string $var The property to access.
     * @return mixed
     */
    public function __get($var)
    {
        switch ($var) {
            case 'entries':
                return $this;
            default:
                return parent::__get($var);
        }
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case Zend_Gdata_Data::lookupNamespace('atom') . ':' . 'entry':
            $newEntry = new $this->_entryClassName(
                null,
                $child);
            $newEntry->setHttpClient($this->getHttpClient());
            $this->_entry[] = $newEntry;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Get the number of entries in this feed object.
     *
     * @return integer Entry count.
     */
    public function count()
    {
        return count($this->_entry);
    }

    /**
     * Required by the Iterator interface.
     *
     * @return void
     */
    public function rewind()
    {
        $this->_entryIndex = 0;
    }

    /**
     * Required by the Iterator interface.
     *
     * @return mixed The current row, or null if no rows.
     */
    public function current()
    {
        return $this->_entry[$this->_entryIndex];
    }

    /**
     * Required by the Iterator interface.
     *
     * @return mixed The current row number (starts at 0), or NULL if no rows
     */
    public function key()
    {
        return $this->_entryIndex;
    }

    /**
     * Required by the Iterator interface.
     *
     * @return mixed The next row, or null if no more rows.
     */
    public function next()
    {
        ++$this->_entryIndex;
    }

    /**
     * Required by the Iterator interface.
     *
     * @return boolean Whether the iteration is valid
     */
    public function valid()
    {
        return 0 <= $this->_entryIndex && $this->_entryIndex < $this->count();
    }

    /**
     * Returns Entry elements from this feed
     *
     * @return array Zend_Feed_App_Entry array
     */
    public function getEntry()
    {
        return $this->_entry;
    }

}
