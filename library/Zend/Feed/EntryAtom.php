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
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Feed_EntryAbstract
 */
require_once 'Zend/Feed/EntryAbstract.php';


/**
 * Zend_Feed_EntryAtom is the concrete entry subclass that users will deal
 * with whenever working with Atom data.
 *
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_EntryAtom extends Zend_Feed_EntryAbstract
{
    /**
     * Root XML element for ATOM entries.
     *
     * @var string
     */
    protected $_rootElement = 'entry';

    /**
     * Root namespace for ATOM entries.
     */
    protected $_rootNamespace = 'atom';

}

