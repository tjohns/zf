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

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/** Zend_View_Exception */
require_once 'Zend/View/Exception.php';

/**
 * Zend_Layout_View_Helper_HeadMeta
 *
 * @see        http://www.w3.org/TR/xhtml1/dtds.html
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_HeadMeta extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**#@+
     * Types of attributes
     * @var array
     */
    protected $_typeKeys     = array('name', 'http-equiv');
    protected $_requiredKeys = array('content');
    protected $_modifierKeys = array('lang', 'scheme');
    /**#@-*/

    /**
     * @var string registry key
     */
    protected $_regKey = 'Zend_View_Helper_HeadMeta';

    /**
     * Retrieve object instance; optionally add meta tag
     * 
     * @param  string $content 
     * @param  string $key 
     * @param  string $keyType 
     * @param  string $placement 
     * @param  array $modifiers 
     * @return Zend_View_Helper_HeadMeta
     */
    public function headMeta($content = null, $key = null, $keyType = 'name', $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND, $modifiers = array())
    {
        if ((null !== $content) && (null !== $key)) {
            $this->_buildMeta($keyType, $key, $content, $modifiers);
        }
        return $this;
    }

    /**
     * Build meta HTML string
     * 
     * @param  string $type 
     * @param  string $typeValue 
     * @param  string $content 
     * @param  array $modifiers 
     * @return string
     */
    protected function _buildMeta($type, $typeValue, $content, array $modifiers)
    {
        if (!in_array($type, $this->_typeKeys)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(sprintf('Invalid type "%s" provided for meta', $type));
        }

        $modifiersString = '';
        foreach ($modifiers as $key => $value) {
            if (!in_array($key, $this->_modifierKeys)) {
                unset($modifiers[$key]);
            }
            $modifiersString .= $key . '="' . htmlentities($value) . '" ';
        }

        $meta = sprintf(
            '<meta %s="%s" content="%s" %s/>',
            $type,
            htmlentities($typeValue),
            htmlentities($content),
            $modifiersString
        );
        return $meta;
    }
}
