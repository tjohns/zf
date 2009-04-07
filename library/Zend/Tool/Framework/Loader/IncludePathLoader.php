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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Tool_Framework_Loader_Abstract
 */
require_once 'Zend/Tool/Framework/Loader/Abstract.php';

/**
 * @see Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator
 */
require_once 'Zend/Tool/Framework/Loader/IncludePathLoader/RecursiveFilterIterator.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Framework_Loader_IncludePathLoader extends Zend_Tool_Framework_Loader_Abstract 
{

    protected $_filterDenyDirectoryPattern = '.*(/|\\\\).svn';
    protected $_filterAcceptFilePattern    = '.*(?:Manifest|Provider)\.php$';
    
    /**
     * _getFiles()
     *
     * @return array Array of files to load
     */
    protected function _getFiles()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        // used for checking similarly named files
        $relativeItems = array();
        $files = array();
        
        foreach ($paths as $path) {
            
            if (!file_exists($path) || $path[0] == '.') {
                continue;
            }
            
            $realIncludePath = realpath($path);
            
            $rdi = new RecursiveDirectoryIterator($path);
            
            // ideally, we can pass in the regexes via the constructor below.
            $filter = new Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator(
                $rdi);

                /* @todo
                 * This will not work right! For some reason, if we
                 * try to pass these regex's into the filter, they are mangled
                 * by the constructor and not applied correctly to the regex in
                 * accet()
                 * 
                 *, 
                $this->_filterDenyDirectoryPattern,
                $this->_filterAcceptFilePattern
                );*/

            // build the rii with the filter
            $iterator = new RecursiveIteratorIterator($filter);
            
            // iterate
            foreach ($iterator as $item) {
                
                // ensure that the same named file from separate include_paths is not loaded
                $relativeItem = preg_replace('#^' . preg_quote($realIncludePath . DIRECTORY_SEPARATOR, '#') . '#', '', $item->getRealPath());
                
                // no links allowed here for now
                if (!$item->isLink() && !in_array($relativeItem, $relativeItems)) {
                    $relativeItems[] = $relativeItem;
                    $files[] = $item->getRealPath();
                }
            }
        }

        return $files;
    }
    
}
