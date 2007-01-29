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
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_View_Abstract
 */
require_once('Zend/Environment/Module/Abstract.php');


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Environment_Module_Security extends Zend_Environment_Module_Abstract
{
    protected $_type = 'security';

    protected $_tests_to_run = array();

    protected $_tests_not_run  = array();

    protected $_result_counts = array();

    protected $_num_tests_run = 0;


    protected function _init()
    {
        $this->loadTests();
        $this->runTests();
    }


    public function resetStats() {
        $this->_tests_not_run = array();
        $this->_result_counts = array();
        $this->_num_tests_run = 0;
    }


    /**
	 * recurses through the Test subdir and includes classes in each test group subdir,
	 * then builds an array of classnames for the tests that will be run
	 *
	 */
	public function loadTests($test_path=NULL) {
	    $this->resetStats();

	    if ($test_path === NULL) {
	        // this seems hackey.  is it?  dunno.
            $test_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'Security'.DIRECTORY_SEPARATOR.'Test';
	    }
        $test_root = dir($test_path);

		while (false !== ($entry = $test_root->read())) {
			if ( is_dir($test_root->path.DIRECTORY_SEPARATOR.$entry) && !preg_match('|^\.(.*)$|', $entry) ) {
				$test_dirs[] = $entry;
			}
		}

		// include_once all files in each test dir
		foreach ($test_dirs as $test_dir) {
			$this_dir = dir($test_root->path.DIRECTORY_SEPARATOR.$test_dir);

			while (false !== ($entry = $this_dir->read())) {
				if (!is_dir($this_dir->path.DIRECTORY_SEPARATOR.$entry)
					&& preg_match("/[A-Za-z]+\.php/i", $entry)) {
					include_once $this_dir->path.DIRECTORY_SEPARATOR.$entry;
					$classNames[] = "Zend_Environment_Security_Test_".$test_dir."_".basename($entry, '.php');
				}
			}

		}

		$this->_tests_to_run = $classNames;
	}



	/**
	 * This runs the tests in the tests_to_run array and
	 * places returned data in the following arrays/scalars:
	 * - $this->_data
	 * - $this->_result_counts
	 * - $this->_num_tests_run
	 * - $this->_tests_not_run
	 *
	 */
	function runTests() {
		$this->resetStats();

		foreach ($this->_tests_to_run as $testClass) {

			$test = new $testClass();

			/* @var $test Zend_Environment_Security_Test */

			if ($test->isTestable()) {
				$test->test();

				$rs = array('name' => $test->getTestName(),
				            'group' => $test->getTestGroup(),
				            'result_code' => $test->getResult(),
				            'result' => $this->_getResultAsString($test->getResult()),
							'details' => $test->getMessage(),
							'current_value' => $test->getCurrentTestValue(),
							'recommended_value' => $test->getRecommendedTestValue(),
							'link' => $test->getMoreInfoURL(),
						);
				$sec_field = new Zend_Environment_Security_Field($rs);

				//$this->_test_results[] = $sec_field;

				// initialize if not yet set
				if (!isset ($this->_result_counts[$sec_field->result]) ) {
					$this->_result_counts[$sec_field->result] = 0;
				}
				$this->_result_counts[$sec_field->result]++;
				$this->_num_tests_run++;
			} else {
				$rs = array('name' => $test->getTestName(),
				            'group' => $test->getTestGroup(),
				            'result_code' => $test->getResult(),
				            'result' => $this->_getResultAsString($test->getResult()),
							'details' => $test->getMessage(),
							'current_value' => $test->getCurrentTestValue(),
							'recommended_value' => $test->getRecommendedTestValue(),
							'link' => $test->getMoreInfoURL(),
						);
                $sec_field = new Zend_Environment_Security_Field($rs);

                // initialize if not yet set
				if (!isset ($this->_result_counts[Zend_Environment_Security_Test::RESULT_NOTRUN]) ) {
					$this->_result_counts[Zend_Environment_Security_Test::RESULT_NOTRUN] = 0;
				}
				$this->_result_counts[Zend_Environment_Security_Test::RESULT_NOTRUN]++;
				$this->_tests_not_run[] = $sec_field;
			}

			$this->{$sec_field->group."__".$sec_field->name} = $sec_field;
		}
	}


	protected function _getResultAsString($result_code) {
	    switch ($result_code) {
	        case Zend_Environment_Security_Test::RESULT_ERROR :
	            return 'error';
	            break;
	        case Zend_Environment_Security_Test::RESULT_NOTICE :
	            return 'notice';
	            break;
	        case Zend_Environment_Security_Test::RESULT_NOTRUN :
	            return 'notrun';
	            break;
	        case Zend_Environment_Security_Test::RESULT_OK :
	            return 'ok';
	            break;
	        case Zend_Environment_Security_Test::RESULT_WARN :
	            return 'warning';
	            break;
	    }

	}


}
