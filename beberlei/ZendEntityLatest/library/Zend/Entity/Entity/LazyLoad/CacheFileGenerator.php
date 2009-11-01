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
 * @subpackage LazyLoad
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Generate and load Entity Proxies from a Cache File mechanism that contains generated code.
 *
 * @uses       Zend_Entity_LazyLoad_GeneratorAbstract
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage LazyLoad
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_LazyLoad_CacheFileGenerator extends Zend_Entity_LazyLoad_GeneratorAbstract
{
    /**
     * @var string
     */
    protected $_proxyTempFile = '';

    /**
     * @param  string $tempFile
     * @return Zend_Entity_LazyLoad_CacheFileGenerator
     */
    public function setProxyTempFile($tempFile)
    {
        $this->_proxyTempFile = $tempFile;
        return $this;
    }

    public function getProxyTempFile()
    {
        return $this->_proxyTempFile;
    }

    public function generate()
    {
        if($this->_proxyTempFile == '' ||
            (!is_writable(dirname($this->_proxyTempFile)) && !is_writable($this->_proxyTempFile))) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(
                "LazyLoad Proxy Temporary directory or file '".$this->_proxyTempFile."' does not ".
                "exist or is not writeable."
            );
        }

        $tmpFile = $this->getProxyTempFile();

        $hashVersionFile = $tmpFile.".hash";
        if(!is_writable($hashVersionFile) && !is_writable(dirname($hashVersionFile))) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(
                "Cache Metadata File '".$hashVersionFile."' is not writeable!"
            );
        }

        if(!file_exists($tmpFile) || !file_exists($hashVersionFile) || file_get_contents($hashVersionFile) !== $this->_metadataVersion) {

            $proxyFile = new Zend_CodeGenerator_Php_File();
            $proxyFile->setClasses($this->_classes);
            $proxyFile->setFilename($tmpFile);
            $proxyFile->write();

            file_put_contents($hashVersionFile, $this->_metadataVersion);
        }

        require_once($tmpFile);
    }
}