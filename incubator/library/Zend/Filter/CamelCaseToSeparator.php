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
 * @version    $Id: CamelCaseToDashed.php 6695 2007-10-30 20:09:32Z darby $
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
class Zend_Filter_CamelCaseToSeperated extends Zend_Filter_PregReplace
{
    public function __construct($seperator = ' ')
    {
        if (self::isUnicodeSupportEnabled()) {
            $pregMatches = array(
                '#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#' => $seperator . '\1', 
                '#(?<=(?:\p{Ll}))(\p{Lu})#'       => $seperator . '\1'
                );
        } else {
            $pregMatches = array(
                '#(?<=(?:[A-Z]))([A-Z]+)([A-Z][A-z])#' => '\1' . $seperator . '\2',
                '#(?<=(?:[a-z]))([A-Z])#'              => $seperator . '\1'
                );
        }
        
        $this->setMatchPattern(array_keys($pregMatches));
        $this->setReplacement(array_values($pregMatches));
    }
}
