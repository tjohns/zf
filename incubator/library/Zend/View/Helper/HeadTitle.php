<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Placeholder.php 7078 2007-12-11 14:29:33Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder */
require_once 'Zend/View/Helper/Placeholder.php';

/**
 * Helper for setting and retrieving title element for HTML head
 *
 * @uses       Zend_View_Helper_Placeholder
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_View_Helper_HeadTitle extends Zend_View_Helper_Placeholder
{
    /**
     * Placeholder container for title
     * @var Zend_View_Helper_Placeholder_Container_Abstract
     */
    protected $_placeholder;

    /**
     * Constructor
     *
     * Retrieve container object for this helper and set in 
     * {@link $_placeholder}; set prefix/postfix for container.
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_placeholder = $this->_registry->getContainer(__CLASS__);
        $this->_placeholder->setPrefix('<title>')
                           ->setPostfix('</title>');
    }

    /**
     * Retrieve placeholder for title element and optionally set state
     *
     * @param  string $title
     * @param  string $setType
     * @param  string $separator
     * @return Zend_View_Helper_HeadTitle
     */
    public function headTitle($title = null, $setType = Zend_View_Helper_Placeholder_Container_Abstract::APPEND, $separator = null)
    {
        if ($title) {
            if ($setType == Zend_View_Helper_Placeholder_Container_Abstract::SET) {
                $this->_placeholder->set($title);
            } else {
                $this->_placeholder->append($title);
            }
        }
        
        if ($separator) {
            $this->_placeholder->setSeparator($separator);
        }
        
        return $this->_placeholder;
    }
}
