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

/**
 * Entity Manager Factory for Module/Non-Module based Entity Manager Creation
 *
 * @category   Zend
 * @package    Zend_Doctrine2
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Doctrine2_EntityManagerFactory
{
    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @var bool
     */
    protected $_registeredAutoloader = false;

    /**
     * @param array|Zend_Config $options
     */
    public function __construct($options)
    {
        if($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if(!is_array($options)) {
            throw new Zend_Doctrine2_Exception("Invalid Options for EntityManager Factory");
        }
        $this->_options = $options;
    }

    /**
     * @param string $path
     */
    public function addMetadataPath($path)
    {
        $this->_options['metadata']['paths'][] = $path;
        return $this;
    }

    /**
     * Create the entity manager instance.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function createEntityManager()
    {
        return $this->_createEntityManager();
    }

    /**
     * Setup the metadata driver if necessary options are set. Otherwise Doctrine defaults are used (AnnotationReader).
     *
     * @param array $options
     * @param Doctrine\ORM\Configuration $config
     * @param Doctrine\Common\Cache\AbstractCache $cache
     * @param Doctrine\DBAL\Connection $conn
     */
    protected function _setupMetadataDriver($options, $config, $cache, $conn)
    {
        $driver = false;

        if(isset($options['metadata'])) {
            if(isset($options['metadata']['driver'])) {
                $driverName = $options['metadata']['driver'];
                switch(strtolower($driverName)) {
                    case 'annotation':
                        $driverName = 'Doctrine\ORM\Mapping\Driver\AnnotationDriver';
                        break;
                    case 'yaml':
                        $driverName = 'Doctrine\ORM\Mapping\Driver\YamlDriver';
                        break;
                    case 'xml':
                        $driverName = 'Doctrine\ORM\Mapping\Driver\XmlDriver';
                        break;
                    case 'php':
                        $driverName = 'Doctrine\ORM\Mapping\Driver\PhpDriver';
                        break;
                    case 'database':
                        $driverName = 'Doctrine\ORM\Mapping\Driver\DatabaseDriver';
                        break;
                }

                if(!class_exists($driverName)) {
                    throw new Zend_Doctrine2_Exception("MetadataDriver class '".$driverName."' does not exist");
                }

                if(in_array('Doctrine\ORM\Mapping\Driver\AbstractFileDriver', class_parents($driverName))) {
                    if(!isset($options['metadata']['paths'])) {
                        throw new Zend_Doctrine2_Exception("Metadata Driver is file based, but no config file paths were given.");
                    }
                    $driver = new $driverName($options['metadata']['paths']);
                } elseif($driverName == 'Doctrine\ORM\Mapping\Driver\AnnotationDriver') {
                    $reader = new \Doctrine\Common\Annotations\AnnotationReader($cache);
                    $reader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
                    $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader);

                    if(isset($options['metadata']['classDirectory'])) {
                        $driver->setClassDirectory($options['metadata']['classDirectory']);
                    } else {
                        throw new Zend_Doctrine2_Exception("Doctrine Annotation Driver requires to set a class directory for the entities.");
                    }
                } elseif($driverName == 'Doctrine\ORM\Mapping\Driver\DatabaseDriver') {
                    $schemaManager = $conn->getSchemaManager();
                    $driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver($schemaManager);
                }

                if(!($driver instanceof \Doctrine\ORM\Mapping\Driver\Driver)) {
                    throw new Zend_Doctrine2_Exception("No metadata driver could be loaded.");
                }

                $config->setMetadataDriverImpl($driver);
            }
        }
    }

    /**
     * @param array $options
     * @return \Doctrine\ORM\EntityManager
     */
    protected function _createEntityManager()
    {
        $options = $this->_options;

        $cache = $this->_setupCache($options);

        if(!isset($options['proxyDir']) || !file_exists($options['proxyDir'])) {
            throw new Zend_Doctrine2_Exception("No Doctrine2 'proxyDir' option was given, but is required.");
        }

        if(!isset($options['proxyNamespace'])) {
            $options['proxyNamespace'] = 'MyProject/Proxies';
        }

        if(!isset($options['autoGenerateProxyClasses'])) {
            $options['autoGenerateProxyClasses'] = true;
        }

        if(!isset($options['useCExtension'])) {
            $options['useCExtension'] = false;
        }

        $eventManager = new \Doctrine\Common\EventManager();

        $config = new \Doctrine\ORM\Configuration;
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir($options['proxyDir']);
        $config->setProxyNamespace($options['proxyNamespace']);
        $config->setUseCExtension((bool)$options['useCExtension']);

        if(!isset($options['connectionOptions']) || !is_array($options['connectionOptions'])) {
            throw new Zend_Doctrine2_Exception("Invalid Doctrine DBAL Connection Options given.");
        }
        $connectionOptions = $options['connectionOptions'];

        if(isset($options['sqllogger'])) {
            if(is_string($options['sqllogger']) && class_exists($options['sqllogger'])) {
                $logger = new $options['sqllogger']();
                if(!($logger instanceof \Doctrine\DBAL\Logging\SqlLogger)) {
                    throw new Zend_Doctrine2_Exception("Invalid SqlLogger class specified, has to implement \Doctrine\DBAL\Logging\SqlLogger");
                }
                $config->setSqlLogger($logger);
            } else {
                throw new Zend_Doctrine2_Exception("Invalid SqlLogger configuration specified, have to give class string.");
            }
        }

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionOptions, $config, $eventManager);

        $this->_setupMetadataDriver($options, $config, $cache, $conn);

        $em = \Doctrine\ORM\EntityManager::create($conn, $config, $eventManager);

        return $em;
    }

    /**
     * Setup Cache Driver
     * 
     * @param array $options
     * @return Doctrine\Common\Cache\Cache
     */
    protected function _setupCache(array $options)
    {
        if(!isset($options['cache'])) {
            throw new Zend_Doctrine2_Exception("No Cache Class Implementation was given.");
        }
        if(!class_exists($options['cache'], true)) {
            throw new Zend_Doctrine2_Exception("Given Cache Class '".$options['cache']."' does not exist!");
        }
        $cache = new $options['cache'];
        return $cache;
    }
}