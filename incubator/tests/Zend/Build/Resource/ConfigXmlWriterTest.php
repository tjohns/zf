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
 * @package    Zend_Build_Resource
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Zend_Build_Resource_ConfigXmlWriter
 */
require_once 'Zend/Build/Resource/ConfigXmlWriter.php';

class Zend_Build_Resource_ConfigXmlWriterTest extends PHPUnit_Framework_TestCase
{
    const FILES_DIR = '_files';
    const OUTPUT_DIR = 'output';
    
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;
    protected $_outputFilesPath;

    /**
     * Some sample configs
     */
    private $_config1 = null;

    private $_config2 = null;
    
    public function setup()
    {
        // Make the output directory
        if(!file_exists($this->_outputFilesPath)) mkdir($this->_outputFilesPath);
        $this->_config1 = new Zend_Config(
            array(
                'webhost'  => 'www.example.com',
                'database' => array(
                    'adapter' => 'pdo_mysql',
                    'params'  => array(
                        'host'     => 'db.example.com',
                        'username' => 'dbuser',
                        'password' => 'secret',
                        'dbname'   => 'mydatabase'
                    )
                )
            )
        );

        $this->_config2 = new Zend_Config(
            array(
                'project' => array(
                    'name' => 'basic_mvc_project',
                    'application_dir' => array(
                        'name' => 'application',
                        'controller_dir' => array(
                            'name' => 'controllers'
                        ),
                        'model_dir' => array(
                            'name' => 'models'
                        ),
                        'view_dir' => array(
                            'name' => 'views'
                        )
                    ), 
                    'data_dir' => array(
                        'name' => 'data'
                    ),
                    'public_dir' => array(
                        'name' => 'htdocs'
                    ),
                    'library_dir' => array(
                        'name' => 'lib'    
                    ),
                    'trash_dir' => array(
                        'name' => 'trash',
                        'maxSize' => 100
                    )
                )
            )
        );
    }

    public function teardown()
    {
        unset($this->_config1);
        unset($this->_config2);
        
        // Clobber the output directory
        $files = glob($this->_outputFilesPath . DIRECTORY_SEPARATOR . '*');
        foreach($files as $file) {
            unlink($file);
        }
        rmdir($this->_outputFilesPath);
    }

    /**
     * Set up test configuration
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::FILES_DIR;
        $this->_outputFilesPath = $this->_filesPath . DIRECTORY_SEPARATOR . self::OUTPUT_DIR;
    }

    public function testGetXmlForConfig()
    {
        $config1Xml = Zend_Build_Resource_ConfigXmlWriter::getXmlForConfig($this->_config1);
        $this->assertNotNull($config1Xml);
        $this->assertEquals($config1Xml, $this->_getGoldenOutput('config1.xml'));
        
        $config2Xml = Zend_Build_Resource_ConfigXmlWriter::getXmlForConfig($this->_config2);
        $this->assertNotNull($config2Xml);
        $this->assertEquals($config2Xml, $this->_getGoldenOutput('config2.xml'));
    }
    
    public function testWriteConfigToXmlFile()
    {
        $outputConfig1Filename = $this->_outputFilesPath . DIRECTORY_SEPARATOR . 'config1.xml';
        Zend_Build_Resource_ConfigXmlWriter::writeConfigToXmlFile($this->_config1, $outputConfig1Filename);
        $this->assertFileExists($outputConfig1Filename);
        $this->assertXmlFileEqualsXmlFile($this->_filesPath . DIRECTORY_SEPARATOR . 'config1.xml', $outputConfig1Filename);
        
        $outputConfig2Filename = $this->_outputFilesPath . DIRECTORY_SEPARATOR . 'config2.xml';
        Zend_Build_Resource_ConfigXmlWriter::writeConfigToXmlFile($this->_config2, $outputConfig2Filename);
        $this->assertFileExists($outputConfig2Filename);
        $this->assertXmlFileEqualsXmlFile($this->_filesPath . DIRECTORY_SEPARATOR . 'config2.xml', $outputConfig2Filename);
    }
    
    protected function _getGoldenOutput($filename)
    {
        return file_get_contents($this->_filesPath . DIRECTORY_SEPARATOR . $filename);
    }
}