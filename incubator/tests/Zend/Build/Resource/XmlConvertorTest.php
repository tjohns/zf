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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * Include all resource files
 */
require_once 'Zend/Build/Resource/XMLConvertor.php';
require_once 'Zend/Build/Resource/ApplicationDirectory.php';
require_once 'Zend/Build/Resource/ControllerDirectory.php';
require_once 'Zend/Build/Resource/DataDirectory.php';
require_once 'Zend/Build/Resource/Directory.php';
require_once 'Zend/Build/Resource/LibraryDirectory.php';
require_once 'Zend/Build/Resource/ModelDirectory.php';
require_once 'Zend/Build/Resource/ModuleDirectory.php';
require_once 'Zend/Build/Resource/Project.php';
require_once 'Zend/Build/Resource/PublicDirectory.php';
require_once 'Zend/Build/Resource/TrashDirectory.php';
require_once 'Zend/Build/Resource/ViewDirectory.php';

class Zend_Build_Resource_XmlConvertorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Set up test configuration
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath = dirname(__FILE__) . '/_files';
    }

    public function testResourceToXml()
    {
		$project = $this->_getBasicMVCProjectAsResource();
		$project_xml = Zend_Build_Resource_XmlConvertor::resourceToXml($project);
		$this->assertNotNull($project_xml);
		$this->assertTrue(is_string($project_xml));
		$this->assertXmlStringEqualsXmlString($project_xml, $this->_getBasicMVCProjectAsXml());
		// @todo Add test for complex project
    }

    public function testXmlToResource()
    {
    	$project_xml = $this->_getBasicMVCProjectAsXml();
    	$project = Zend_Build_Resource_XmlConvertor::xmlToResource($project_xml);
    	$this->assertNotNull($project);
		$this->assertTrue($project instanceof Zend_Build_Resource_Interface);
		$this->assertEquals($project, $this->_getBasicMVCProjectAsResource());
		// @todo Add test for complex project
    }

	public function testSymetricConversion()
	{
		// XML to resources and back again
		$project_xml = $this->_getBasicMVCProjectAsXml();
    	$project = Zend_Build_Resource_XMLConvertor::xmlToResource($project_xml);
    	$round_trip_xml = Zend_Build_Resource_XMLConvertor::resourceToXml($project);
    	$this->assertXmlStringEqualsXmlString($project_xml, $round_trip_xml);
    	
    	// Resources to XML and back again
    	$project = $this->_getBasicMVCProjectAsResource();
    	$project_xml = Zend_Build_Resource_XMLConvertor::resourceToXml($project);
    	$round_trip = Zend_Build_Resource_XMLConvertor::xmlToResource($project_xml);
    	$this->assertEquals($project, $round_trip);
    	// @todo Add test for complex project
	}

	public function testNotWellFormedXml()
	{
		// Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
	}

	public function testInvalidXml()
	{
		// Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
	}
	
	public function testInvalidResourceTree()
	{
		// Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
	}
	
	protected function _getBasicMVCProjectAsResource()
	{
		$project = new Zend_Build_Resource_Project('basic_mvc_project', array(
			new Zend_Build_Resource_ApplicationDirectory('application', array(
				new Zend_Build_Resource_ControllerDirectory('controllers'),
				new Zend_Build_Resource_ModelDirectory('models'),
				new Zend_Build_Resource_ViewDirectory('views'))),
			new Zend_Build_Resource_DataDirectory('data'),
			new Zend_Build_Resource_PublicDirectory('htdocs'),
			new Zend_Build_Resource_LibraryDirectory('lib'),
			new Zend_Build_Resource_TrashDirectory('trash')));
		return $project;
	}
	
	protected function _getBasicMVCProjectAsXml()
	{
		return $this->_readGoldenOutput('basicMVCProject');
	}
	
	/**
	 * Helper function: read golden output in the _files directory
	 *
	 * @param string $response
	 * @return string
	 */
	protected function _readGoldenOutput($name)
	{
		return file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . $name . '.xml');
	}
}