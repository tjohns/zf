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
 * @subpackage Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

class Zend_Doctrine2_Tool_Dc2Project extends Zend_Doctrine2_Tool_ProviderAbstract
{
    public function enable()
    {
        $proxyDirCreated = false;

        $resp = $this->_registry->getResponse();
        $resp->appendContent('[Doctrine2] Enabling Doctrine 2 support for current project.', array('color' => 'green'));

        $profile = $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);

        $applicationInstance = $this->_getApplicationInstance();

        if ($applicationInstance->getBootstrap()->hasPluginResource('EntityManagerFactory')) {
            return $resp->appendContent('[Doctrine2] FAILURE - EntityManagerFactory resource already enabled.', array('color' => 'hiRed'));
        }

        if ( ($proxyDir = $profile->search('Dc2ProxyDirectory')) !== false) {
            $proxyDirPath = $proxyDir->getPath();
            $resp->appendContent('[Doctrine2] FAILURE - Proxy Directory already exists at '.$proxyDirPath, array('color' => 'hiRed'));
        } else {

            $dataDirectory = $profile->search('dataDirectory');
            $resp->appendContent("[Doctrine2] Searching for Data directory..");

            if (!$dataDirectory->isEnabled()) {
                $dataDirectory->setEnabled(true);
                $resp->appendContent("[Doctrine2] ..Data Directory was disabled. Enabled automatically.");
            }

            $proxyDir = $dataDirectory->createResource('Dc2ProxyDirectory');
            $proxyDirPath = $proxyDir->getPath();
            $resp->appendContent('[Doctrine2] ..Proxy directory created at '.$proxyDirPath, array('color' => 'green'));
            $proxyDirCreated = true;
        }

        // add EntityManager Resource here, desperatly waiting for ralphs Static INI Writer

        if ($proxyDirCreated) {
            $resp->appendContent('[Doctrine2] SUCCESS - Enabled project for Doctrine 2 support', array('color' => 'green'));
            $profile->storeToFile();
        }
    }
}