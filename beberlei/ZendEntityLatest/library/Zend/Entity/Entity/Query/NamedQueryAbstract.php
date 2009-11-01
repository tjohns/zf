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
 * @package    Zend_Entity
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract class for named query support
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_Query_NamedQueryAbstract
{
    /**
     * @var array
     */
    protected $_params = array();

    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_manager = null;

    /**
     * @param Zend_Entity_Manager_Interface $manager
     */
    public function setEntityManager(Zend_Entity_Manager_Interface $manager)
    {
        $this->_manager = $manager;
    }

    /**
     * @return Zend_Entity_Manager_Interface
     */
    public function getEntityManager()
    {
        return $this->_manager;
    }

    /**
     * @param Zend_Entity_Manager_Interface $manager
     * @return Zend_Entity_Query_QueryAbstract
     */
    abstract public function create();
}
