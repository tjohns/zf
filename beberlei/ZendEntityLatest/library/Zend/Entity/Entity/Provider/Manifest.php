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
 * @subpackage Provider
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once "Zend/Tool/Framework/Manifest/ProviderManifestable.php";

require_once "Zend/Entity/Provider/Entity.php";
require_once "Zend/Entity/Provider/EntityProxy.php";

/**
 * Manifest for all Zend Entity related providers
 *
 * @uses       Zend_Tool_Framework_Manifest_ProviderManifestable
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Provider
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Provider_Manifest implements Zend_Tool_Framework_Manifest_ProviderManifestable
{
    /**
     * @var array
     */
    protected $_providers = null;

    /**
     * @return array
     */
    public function getProviders()
    {
        if($this->_providers == null) {
            $this->_providers = array(
                new Zend_Entity_Provider_Entity(),
                new Zend_Entity_Provider_EntityProxy(),
            );
        }
        return $this->_providers;
    }
}