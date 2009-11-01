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
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

class Zend_Entity_Definition_Id_UUID implements Zend_Entity_Definition_Id_Interface
{
    /**
     * @var string
     */
    protected $_prefix = '';

    /**
     * @var boolean
     */
    protected $_moreEntropy = false;

    /**
     * @var string
     */
    protected $_lastSequenceId = null;

    /**
     * @param boolean $moreEntropy
     * @param string $prefix
     */
    public function __construct($moreEntropy=false, $prefix='')
    {
        $this->_prefix = $prefix;
        $this->_moreEntropy = $moreEntropy;
    }

    /**
     * @return bool
     */
    public function isPrePersistGenerator()
    {
        return true;
    }

    /**
     * Generate a Id for the given entity.
     *
     * @param  Zend_Entity_Manager_Interface $manager
     * @param  object $entity
     * @return mixed
     */
    public function generate(Zend_Entity_Manager_Interface $manager, $entity)
    {
        return uniqid($this->_prefix, $this->_moreEntropy);
    }
}