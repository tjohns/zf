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
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Index_SegmentInfo */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';

/** Zend_Search_Lucene_Index_SegmentWriter */
require_once 'Zend/Search/Lucene/Index/SegmentWriter.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_SegmentMerger
{
    /**
     * Target segment writer
     *
     * @var Zend_Search_Lucene_Index_SegmentStreamWriter
     */
    private $_writer;

    /**
     * Number of docs in a new segment
     *
     * @var integer
     */
    private $_docCount = 0;

    /**
     * A set of segments to be merged
     *
     * @var array Zend_Search_Lucene_Index_SegmentInfo
     */
    private $_segmentInfos = array();

    /**
     * Flag to signal, that merge is already done
     *
     * @var boolean
     */
    private $_mergeDone = false;

    /**
     * Field map
     * [<segment_name>][<field_number>] => <target_field_number>
     *
     * @var array
     */
    private $_fieldsMap = array();



    /**
     * Object constructor.
     *
     * Creates new segment merger with $directory as target to merge segments into
     * and $name as a name of new segment
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @param string $name
     */
    public function __construct($directory, $name)
    {
        $this->_writer = new Zend_Search_Lucene_Index_SegmentWriter_StreamWriter($directory, $name);
    }


    /**
     * Add segmnet to a collection of segments to be merged
     *
     * @param Zend_Search_Lucene_Index_SegmentInfo $segment
     */
    public function addSource(Zend_Search_Lucene_Index_SegmentInfo $segmentInfo)
    {
        $this->_segmentInfos[$segmentInfo->getName()] = $segmentInfo;
    }


    /**
     * Do merge.
     *
     * Returns number of documents in newly created segment
     *
     * @return Zend_Search_Lucene_Index_SegmentInfo
     * @throws Zend_Search_Lucene_Exception
     */
    public function merge()
    {
        if ($this->_mergeDone) {
            throw new Zend_Search_Lucene_Exception('Merge is already done.');
        }

        if (count($this->_segmentInfos) < 2) {
            throw new Zend_Search_Lucene_Exception('Wrong number of segments to be merged ('
                                                 . count($this->_segmentInfos)
                                                 . ').');
        }

        $this->_mergeFields();

        $this->_mergeDone = true;

        return $this->_writer->close();
    }


    /**
     * Merge fields information
     */
    private function _mergeFields()
    {
        foreach ($this->_segmentInfos as $segName => $segmentInfo) {
            $segmentFields = $segmentInfo->getFields();

            foreach ($segmentFields as $fieldInfo) {
                $this->_fieldsMap[$segName][$fieldInfo->number] = $this->_writer->addFieldInfo($fieldInfo);
            }
        }
    }


    /**
     * Merge fields information
     */
    private function _mergeTerms()
    {
        foreach ($this->_segmentInfos as $segName => $segmentInfo) {
            $segmentFields = $segmentInfo->getFields();

            foreach ($segmentFields as $fieldInfo) {
                $this->_fieldsMap[$segName][$fieldInfo->number] = $this->_writer->addFieldInfo($fieldInfo);
            }
        }
    }
}
