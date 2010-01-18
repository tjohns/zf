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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once "Zend/Doctrine2/Tool/ProviderAbstract.php";

class Zend_Doctrine2_Tool_Dc2Cache extends Zend_Doctrine2_Tool_ProviderAbstract
{
    public function clearAll()
    {
        $this->clearMetadata();
        $this->clearQuery();
        $this->clearResult();
    }

    public function clearMetadata()
    {
        $em = $this->_getEntityManager();

        $metadataCache = $em->getConfiguration()->getMetadataCacheImpl();
        if($metadataCache->getManageCacheIds() == false) {
            throw new Zend_Doctrine2_Exception("Cannot clear metadata cache when cache-ids are not managed.");
        }
        $metadataCache->deleteAll();
    }

    public function clearQuery()
    {
        $em = $this->_getEntityManager();

        $queryCache = $em->getConfiguration()->getQueryCacheImpl();
        if($queryCache->getManageCacheIds() == false) {
            throw new Zend_Doctrine2_Exception("Cannot clear query cache when cache-ids are not managed.");
        }
        $queryCache->deleteAll();
    }

    public function clearResult()
    {
        $em = $this->_getEntityManager();

        $resultCache = $em->getConfiguration()->getQueryCacheImpl();
        if($resultCache->getManageCacheIds() == false) {
            throw new Zend_Doctrine2_Exception("Cannot clear query cache when cache-ids are not managed.");
        }
        $resultCache->deleteAll();
    }
}