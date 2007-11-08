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
 * @version    $Id: CamelCaseToSeparator.php 6779 2007-11-08 15:10:41Z matthew $
 */

/**
 * @see Zend_Filter_PregReplace
 */
require_once 'Zend/Filter/PregReplace.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_SeparatorToCamelCase extends Zend_Filter_PregReplace
{
    /**
     * Constructor
     * 
     * @param  string $separator Space by default
     * @return void
     */
    public function __construct($separator = ' ')
    {
        if ($separator == null) {
            throw new Zend_Filter_Exception('Separator must not be empty.');
        }
        
        // a unicode safe way of converting characters to \x00\x00 notation
        $hexSeparator = '\x' . implode('\x', array_map("bin2hex", preg_split('//', $separator, -1, PREG_SPLIT_NO_EMPTY)));
        
        if (self::isUnicodeSupportEnabled()) {
            $pregMatches = array(
                '#('.$hexSeparator.')(\p{L}{1})#e' => "strtoupper('\\2')", 
                '#(^\p{Ll}{1})#e' => "strtoupper('\\1')"
                );
        } else {    
            $pregMatches = array(
                '#('.$hexSeparator.')([A-Z]{1})#e' => "strtoupper('\\2')", 
                '#(^[a-z]{1})#e' => "strtoupper('\\1')"
                );
        }
        
        $this->setMatchPattern(array_keys($pregMatches));
        $this->setReplacement(array_values($pregMatches));
    }
}
