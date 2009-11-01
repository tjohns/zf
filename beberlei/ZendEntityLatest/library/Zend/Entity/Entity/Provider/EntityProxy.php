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

require_once "Zend/Loader.php";

require_once "Zend/Tool/Framework/Provider/Abstract.php";

require_once "Zend/Tool/Framework/Provider/Pretendable.php";

/**
 * Generate LazyLoad Proxies from definition
 *
 * @uses       Zend_Tool_Framework_Provider_Abstract
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Provider
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Provider_EntityProxy extends Zend_Tool_Framework_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Pretendable
{
    /**
     * @var bool
     */
    protected $_forceGenerate = false;

    /**
     * Print the PHP code for a lazy-load proxy of a single given entity name,.
     *
     * @param string $metadataDefinitionPath
     * @param string $entityName
     */
    public function printSingle($metadataDefinitionPath, $entityName)
    {
        throw new Zend_Entity_Provider_Exception("not implemented yet!");
    }

    /**
     * Generate the Lazy Load Proxies for the pointed to entities.
     *
     * @param string $metadataDefinitionPath
     * @param string $proxyDirectory
     * @param bool $expandUnderscoreToDirectory
     * @throws Zend_Entity_Provider_Exception
     */
    public function generate($metadataDefinitionPath, $proxyDirectory, $expandUnderscoreToDirectory=true)
    {
        require_once "Zend/Loader/Autoloader.php";
        $loader = Zend_Loader_Autoloader::getInstance();

        $response = $this->_registry->getResponse();

        $metadataFactory = null;
        if(file_exists($metadataDefinitionPath)) {
            if(is_dir($metadataDefinitionPath)) {
                require_once "Zend/Entity/MetadataFactory/Code.php";
                $metadataFactory = new Zend_Entity_MetadataFactory_Code($metadataDefinitionPath);
            }
        }

        $request = $this->_registry->getRequest();
        /* @var $request Zend_Tool_Framework_Client_Request */

        $lazyLoadGenerator = new Zend_Entity_LazyLoad_Generator();

        $entityNames = $metadataFactory->getDefinitionEntityNames();
        $proxyFiles = array();
        foreach($entityNames AS $entityName) {
            Zend_Loader::loadClass($entityName);

            $entityDef = $metadataFactory->getDefinitionByEntityName($entityName);
            $proxyClass = $lazyLoadGenerator->generateLazyLoadProxyClass($entityDef);

            if($expandUnderscoreToDirectory) {
                $entityName = str_replace("_", DIRECTORY_SEPARATOR, $entityName);
            }

            $proxyFilePath = sprintf('%s/%sProxy.php', $proxyDirectory, $entityName);
            if(file_exists($proxyFilePath) && $this->_forceGenerate == false) {
                throw new Zend_Entity_Provider_Exception(
                    "A proxy file already exists for '".$entityName."' at location '".$proxyFilePath."'. ".
                    "Use 'zf force-generate entity-proxy <options>' to overwrite the existing proxies or delete ".
                    "the proxy files before running 'generate'."
                );
            } else if(!is_writable(dirname($proxyFilePath))) {
                throw new Zend_Entity_Provider_Exception(
                    "The directory '".dirname($proxyFilePath)."' is not writeable to generate proxies into."
                );
            }

            $proxyFile = new Zend_CodeGenerator_Php_File();
            $proxyFile->setClass($proxyClass);
            $proxyFile->setFilename($proxyFilePath);

            $proxyFiles[] = $proxyFile;

            if($request->isVerbose()) {
                $response->appendContent("Generated Proxy Representation for '".$entityName."'.");
            }
        }

        foreach($proxyFiles AS $proxyFile) {
            if($request->isPretend()) {
                $response->appendContent(
                    "Would write proxy class '".$proxyFile->getClass()->getName()."' ".
                    "into file '".$proxyFile->getFilename()."'."
                );
            } else {
                $proxyFile->write();

                $response->appendContent("Generated proxy '".$proxyFile->getClass()->getName()."' in file '".$proxyFile->getFilename()."'.");
            }
        }
    }

    /**
     * Force to generate the Lazy Load Proxies for the pointed to entities.
     *
     * @param string $metadataDefinitionPath
     * @param string $proxyDirectory
     * @param bool $expandUnderscoreToDirectory
     */
    public function forceGenerate($metadataDefinitionPath, $proxyDirectory, $expandUnderscoreToDirectory=true)
    {
        $this->_forceGenerate = true;
        $this->generate($metadataDefinitionPath, $proxyDirectory, $expandUnderscoreToDirectory);
        $this->_forceGenerate = false;
    }
}