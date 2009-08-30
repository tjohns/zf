<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

class Zend_Entity_Definition_Id_AutoIncrement implements Zend_Entity_Definition_Id_Interface
{
    /**
     * @return bool
     */
    public function isPrePersistGenerator()
    {
        return false;
    }

    /**
     * Returns null
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @return null
     */
    public function nextSequenceId(Zend_Db_Adapter_Abstract $db)
    {
        return null;
    }

    /**
     * Returns Last Insert Id
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @return int
     */
    public function lastSequenceId(Zend_Db_Adapter_Abstract $db)
    {
        return $db->lastInsertId();
    }
}