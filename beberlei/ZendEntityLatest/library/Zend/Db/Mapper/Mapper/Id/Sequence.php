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
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Sequence Id Generator
 *
 * @uses       Zend_Entity_Definition_Interface
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Id_Sequence implements Zend_Entity_Definition_Id_Interface
{
    /**
     * @var string
     */
    protected $_sequenceName = null;

    /**
     * @param string $sequenceName
     */
    public function __construct($sequenceName=null)
    {
        $this->_sequenceName = $sequenceName;
    }

    /**
     * @param string $sequenceName
     */
    public function setSequenceName($sequenceName)
    {
        $this->_sequenceName = $sequenceName;
    }

    /**
     * @return string
     */
    public function getSequenceName()
    {
        return $this->_sequenceName;
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
        $db = $manager->getMapper()->getAdapter();
        return $db->nextSequenceId($this->_sequenceName);
    }
}