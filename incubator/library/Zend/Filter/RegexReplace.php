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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_RegexReplace implements Zend_Filter_Interface
{
    /**
     * Pattern to match
     * @var string
     */
    protected $_match = null;

    /**
     * Replacement pattern
     * @var string
     */
    protected $_replace = null;
    
    /**
     * Constructor
     * 
     * @param  string $match 
     * @param  string $replace 
     * @return void
     */
    public function __construct($match, $replace)
    {
        $this->_match = $match;
        $this->_replace = $replace;
    }
    
    /**
     * Perform regexp replacement as filter
     * 
     * @param  string $value 
     * @return string
     */
    public function filter($value)
    {
        return preg_replace($this->_match, $this->_replace, $value);
    }
}
