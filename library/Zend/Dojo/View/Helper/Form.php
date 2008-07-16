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
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/** Zend_Dojo_View_Helper_Dijit */
require_once 'Zend/Dojo/View/Helper/Dijit.php';

/**
 * Dojo Form dijit
 * 
 * @uses       Zend_Dojo_View_Helper_Dijit
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class Zend_Dojo_View_Helper_Form extends Zend_Dojo_View_Helper_Dijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.Form';

    /**
     * Module being used
     * @var string
     */
    protected $_module = 'dijit.form.Form';

    /**
     * dijit.form.Form
     * 
     * @param  int $id 
     * @param  null|array $attribs HTML attributes
     * @param  false|string $content 
     * @return string
     */
    public function form($id, $attribs = null, $content = false)
    {
        if (!is_array($attribs)) {
            $attribs = (array) $attribs;
        }
        $attribs['id'] = $id;

        if (false === $content) {
            $content = '';
        }

        $attribs = $this->_prepareDijit($attribs, array(), 'layout');

        $html = '<form' . $this->_htmlAttribs($attribs) . '>'
              . $content
              . '</form>';

        return $html;
    }
}
