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
 * @package    Zend_Doctrine2
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract Filter for Doctrine 2 related primitive value to entity filtering.
 *
 * @uses       Zend_Filter_Interface
 * @category   Zend
 * @package    Zend_Doctrine2
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Doctrine2_Filter_FilterAbstract implements Zend_Filter_Interface
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_entityManager = null;

    /**
     * @var string
     */
    protected $_entityClass = null;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->_entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->_entityClass;
    }

    /**
     * From a given input like an entity id the original object is re-construtected and returned.
     *
     * @throws Zend_Doctrine2_Filter_FilterException
     * @param  string|int|array $value
     * @return object
     */
    public function filter($value)
    {
        if ($this->_entityManager == null || !class_exists($this->_entityClass)) {
            throw new Zend_Doctrine2_Filter_FilterException(
                "EntityManager and Entity class are required values for the FromEntityId Filters."
            );
        }

        return $this->doFilter($value);
    }

    /**
     * Perform the filtering work.
     *
     * @param  string|int|array $value
     * @return object
     */
    abstract protected function doFilter($value);
}