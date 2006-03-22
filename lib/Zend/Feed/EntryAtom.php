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
 * Zend_Feed_EntryAbstract
 */
require_once 'Zend/Feed/EntryAbstract.php';


/**
 * Zend_Feed_EntryAtom is the concrete entry subclass that users will deal
 * with whenever working with Atom data.
 *
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
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

