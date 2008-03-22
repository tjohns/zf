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
 * @package    Zend_Build
 * @subpackage Zend_Build_Action
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @see Zend_Build_Resource_Interface
 */
require_once 'Zend/Build/Resource/Interface.php';

/**
 * Include Action files
 */
require_once 'Zend/Build/Action/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Build_Action
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Build_Action_Abstract
    extends Zend_Build_AbstractConfigurable
    implements Zend_Build_Action_Interface
{   
	/**
     * Default implementation of execute(). Should work or offer reuse for many commands.
     */
    public function execute (Project $projectProfile, array $resources)
    {
        // Delegate execution to each resource
        $_resources[0]->$this->_name();
    }
    
	/**
     * Default implementation of validate(). Should work or offer reuse for many commands.
     */
    public function validate (Project $projectProfile, array $resources)
    {
        if ($_resources == null)
            return false;
        return true;
    }

    /**
     * Return string representation (which will also be a valid CLI command) of this command.
     */
    public function __toString ()
    {
        $str = $this->getName() . ' ';
        foreach ($_options as $option) {
            $str .= $_option . toString() . next($_options) ? ' ' : '';
        }
        foreach ($_resources as $resource) {
            $str .= $_resource;
        }
    }
}