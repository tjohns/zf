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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/Util/Filter.php';

// Set error reporting to the level to which Zend Framework code must comply.
error_reporting( E_ALL | E_STRICT );

// Determine the root, library, and tests directories of the framework distribution.
$zfRoot    = dirname(dirname(__FILE__));
$zfLibrary = $zfRoot . DIRECTORY_SEPARATOR . 'library';
$zfTests   = $zfRoot . DIRECTORY_SEPARATOR . 'tests';

// Prepend the Zend Framework library/ and tests/ directories to the 
// include_path. This allows the tests to run out of the box and helps prevent 
// loading other copies of the framework code and tests that would supersede 
// this copy.
set_include_path($zfLibrary . PATH_SEPARATOR
               . $zfTests   . PATH_SEPARATOR
               . get_include_path());

// Load the user-defined test configuration file, if it exists;
// otherwise, load the default configuration.
if (is_readable($zfTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

// Add Zend Framework library/ directory to the PHPUnit code coverage
// whitelist. This has the effect that only production code source files appear
// in the code coverage report and that all production code source files, even
// those that are not covered by a test yet, are processed.
if (TESTS_GENERATE_REPORT === TRUE &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
    PHPUnit_Util_Filter::addDirectoryToWhitelist($zfLibrary);
}

// Unset global variables that are no longer needed.
unset($zfRoot, $zfLibrary, $zfTests);
