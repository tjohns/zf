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
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Document_OpenXml */
require_once 'Zend/Search/Lucene/Document/OpenXml.php';

if (class_exists('ZipArchive')) {

/**
 * Pptx document.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Document_Pptx extends Zend_Search_Lucene_Document_OpenXml
{
    /**
     * Xml Schema - PresentationML
     *
     * @var string
     */
    const SCHEMA_PRESENTATIONML = 'http://schemas.openxmlformats.org/presentationml/2006/main';
    
    /**
     * Xml Schema - DrawingML
     *
     * @var string
     */
    const SCHEMA_DRAWINGML = 'http://schemas.openxmlformats.org/drawingml/2006/main';    
    
    /**
     * Xml Schema - Slide relation
     *
     * @var string
     */
    const SCHEMA_SLIDERELATION = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide';

    /**
     * Object constructor
     *
     * @param string  $fileName
     * @param boolean $storeContent
     */
    private function __construct($fileName, $storeContent)
    {
        // Document data holders
        $slides = array();
    	$documentBody = array();
        $coreProperties = array();

        // Open OpenXML package
        $package = new ZipArchive();
        $package->open($fileName);

        // Read relations and search for officeDocument
        $relations = simplexml_load_string($package->getFromName("_rels/.rels"));
        foreach ($relations->Relationship as $rel) {
            if ($rel["Type"] == Zend_Search_Lucene_Document_OpenXml::SCHEMA_OFFICEDOCUMENT) {
            	// Found office document! Search for slides...
            	$slideRelations = simplexml_load_string($package->getFromName(dirname($rel["Target"]) . "/_rels/" . basename($rel["Target"]) . ".rels"));
            	foreach ($slideRelations->Relationship as $slideRel) {
            		if ($slideRel["Type"] == Zend_Search_Lucene_Document_Pptx::SCHEMA_SLIDERELATION) {
            			// Found slide!
            			$slides[ str_replace( 'rId', '', (string)$slideRel["Id"] ) ] = simplexml_load_string(
		                    $package->getFromName(dirname($rel["Target"]) . "/" . dirname($slideRel["Target"]) . "/" . basename($slideRel["Target"]))
		                );
            		}
            	}
            	
            	break;
            }
        }
        
        // Sort slides
        ksort($slides);
        
        // Extract contents from slides
        foreach ($slides as $slide) {
        	// Register namespaces
        	$slide->registerXPathNamespace("p", Zend_Search_Lucene_Document_Pptx::SCHEMA_PRESENTATIONML);
        	$slide->registerXPathNamespace("a", Zend_Search_Lucene_Document_Pptx::SCHEMA_DRAWINGML);
        	
        	// Fetch all text
        	$textElements = $slide->xpath('//a:t');
        	foreach ($textElements as $textElement) {
        		$documentBody[] = (string)$textElement;
        	}
        }   
        
        // Read core properties
        $coreProperties = $this->extractMetaData($package);

        // Close file
        $package->close();

        // Store filename
        $this->addField(Zend_Search_Lucene_Field::Text('filename', $fileName));

            // Store contents
        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('body', implode(' ', $documentBody)));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('body', implode(' ', $documentBody)));
        }

        // Store meta data properties
        foreach ($coreProperties as $key => $value)
        {
            $this->addField(Zend_Search_Lucene_Field::Text($key, $value));
        }

        // Store title (if not present in meta data)
        if (!isset($coreProperties['title']))
        {
            $this->addField(Zend_Search_Lucene_Field::Text('title', $fileName));
        }
    }

    /**
     * Load Pptx document from a file
     *
     * @param string  $fileName
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Pptx
     */
    public static function loadPptxFile($fileName, $storeContent = false)
    {
        return new Zend_Search_Lucene_Document_Pptx($fileName, $storeContent);
    }
}

} // end if (class_exists('ZipArchive'))
