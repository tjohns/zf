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
 * @package    Zend_View
 * @subpackage Inflector
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_View_Inflector_Rule_Abstract */
require_once 'Zend/View/Inflector/Rule/Abstract.php';

/**
 * Transform a module name into a view script base path
 *
 * @package    Zend_View
 * @subpackage Inflector
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Inflector_Rule_ModulePath extends Zend_View_Inflector_Rule_Abstract
{
    /**
     * View script path specification string
     * @var string
     */
    protected $_pathSpec = ':module/views';

    /**
     * Transform a path name according to rules
     * 
     * @param  string $path 
     * @return string Inflected path
     */
    public function getParams($path, array $params = array())
    {
        $module     = $path;

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'module':
                    $$key = (string) $value;
                    break;
                default:
                    break;
            }
        }

        return compact('module');
    }

    public function inflectModule($module)
    {
        $this->initDelimiters();
        return str_replace(
            $this->_wordDelimiters,
            '-',
            strtolower(str_replace($this->_pathDelimiters, '/', $module)));
    }
}
