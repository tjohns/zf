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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once "Zend/Doctrine2/Tool/ProviderAbstract.php";

class Zend_Doctrine2_Tool_Dc2Query extends Zend_Doctrine2_Tool_ProviderAbstract
{
    /**
     * @var array
     */
    protected $_specialties = array('Dql');

    /**
     *
     * @param string $query
     * @param string $hydrateMode
     * @param int $maxDepth
     */
    public function run($query, $hydrateMode="array", $maxDepth=4)
    {
        $this->runDql($query, $hydrateMode, $maxDepth);
    }

    /**
     * @param string $query
     * @param string $hydrateMode
     * @param int $maxDepth
     */
    public function runDql($query, $hydrateMode="array", $maxDepth=4)
    {
        $em = $this->_getEntityManager();

        if($hydrateMode == "array") {
            $resultSet = $em->createQuery($query)->getArrayResult();
        } else if($hydrateMode == "object") {
            $resultSet = $em->createQuery($query)->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
        } else if($hydrateMode == "scalar") {
            $resultSet = $em->createQuery($query)->getScalarResult();
        } else if($hydrateMode == "singlescalar") {
            $resultSet = $em->createQuery($query)->getSingleScalarResult();
        } else {
            throw new Zend_Doctrine2_Exception("Unknown Hydration Mode '".$hydrateMode."'");
        }
        $maxDepth = (int)$maxDepth;

        if(count($resultSet) > 0) {
            \Doctrine\Common\Util\Debug::dump($resultSet, $maxDepth);
        } else {
            $this->_registry->getResponse()->appendContent("No result found", array(
                "aligncenter" => 70,
                "color" => array('hiWhite', 'bgRed'),
                'blockize' => 70
            ));
        }
    }
}