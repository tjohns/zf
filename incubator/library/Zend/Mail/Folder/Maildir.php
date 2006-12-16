<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/**
 * Zend_Mail_Folder
 */
require_once 'Zend/Mail/Folder.php';

/**
 * Zend_Mail_Folder_Interface
 */
require_once 'Zend/Mail/Folder/Interface.php';

/**
 * Zend_Mail_Maildir
 */
require_once 'Zend/Mail/Maildir.php';

/**
 * Zend
 */
require_once 'Zend.php';

/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Folder_Maildir extends Zend_Mail_Maildir implements Zend_Mail_Folder_Interface
{
    /**
     * Zend_Mail_Folder root folder for folder structure
     */
    protected $_rootFolder;

    /**
     * rootdir of folder structure
     */
    protected $_rootdir;

    /**
     * name of current folder
     */
    protected $_currentFolder;

    /**
     * delim char for subfolders
     */
    protected $_delim;

    /**
     * Create instance with parameters
     * Supported parameters are:
     *   - rootdir rootdir of maildir structure
     *   - dirname alias for rootdir
     *   - delim   delim char for folder structur, default is '.'
     *   - folder intial selected folder, default is 'INBOX'
     *
     * @param  $params              array mail reader specific parameters
     * @throws Zend_Mail_Exception
     */
    public function __construct($params)
    {
        if(isset($params['dirname']) && !isset($params['rootdir'])) {
            $params['rootdir'] = $params['dirname'];
        }

        if(!isset($params['rootdir']) || !is_dir($params['rootdir'])) {
            throw Zend::exception('Zend_Mail_Exception', 'no valid rootdir given in params');
        }

        $this->_rootdir = rtrim($params['rootdir'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->_delim = isset($params['delim']) ? $params['delim'] : '.';

        $this->_buildFolderTree();
        $this->selectFolder(!empty($params['folder']) ? $params['folder'] : 'INBOX');
        $this->_has['top'] = true;
    }

    /**
     * find all subfolders and mbox files for folder structure
     *
     * Result is save in Zend_Mail_Folder instances with the root in $this->_rootFolder.
     * $parentFolder and $parentGlobalName are only used internally for recursion.
     *
     */
    private function _buildFolderTree()
    {
        $this->_rootFolder = new Zend_Mail_Folder('/', '/', false);
        $this->_rootFolder->INBOX = new Zend_Mail_Folder('INBOX', 'INBOX', true);

        $dh = @opendir($this->_rootdir);
        if(!$dh) {
            throw Zend::Exception('Zend_Mail_Exception', "can't read folders in maildir");
        }
        $dirs = array();
        while(($entry = readdir($dh)) !== false) {
            // maildir++ defines folders must start with .
            if($entry[0] != '.' || $entry == '.' || $entry == '..') {
                continue;
            }
            if($this->_isMaildir($this->_rootdir . $entry)) {
                $dirs[] = $entry;
            }
        }
        closedir($dh);

        sort($dirs);
        $stack = array(null);
        $folderStack = array(null);
        $parentFolder = $this->_rootFolder;
        $parent = '.';

        foreach($dirs as $dir) {
            do {
                if(strpos($dir, $parent) === 0) {
                    $local = substr($dir, strlen($parent));
                    if(strpos($local, $this->_delim) !== false) {
                        throw Zend::Exception('Zend_Mail_Exception', 'error while reading maildir');
                    }
                    array_push($stack, $parent);
                    $parent = $dir . $this->_delim;
                    $folder = new Zend_Mail_Folder($local, substr($dir, 1), true);
                    $parentFolder->$local = $folder;
                    array_push($folderStack, $parentFolder);
                    $parentFolder = $folder;
                    break;
                } else if($stack) {
                    $parent = array_pop($stack);
                    $parentFolder = array_pop($folderStack);
                }
            } while($stack);
            if(!$stack) {
                throw Zend::Exception('Zend_Mail_Exception', 'error while reading maildir');
            }
        }
    }

    /**
     * get root folder or given folder
     *
     * @param string $rootFolder get folder structure for given folder, else root
     * @return Zend_Mail_Folder root or wanted folder
     */
    public function getFolders($rootFolder = null)
    {
        if(!$rootFolder) {
            return $this->_rootFolder;
        }

        // rootdir is same as INBOX in maildir
        if(strpos($rootFolder, 'INBOX') === 0) {
            $rootFolder = substr($rootFolder, 6);
        }
        $currentFolder = $this->_rootFolder;
        $subname = trim($rootFolder, $this->_delim);
        while($currentFolder) {
            @list($entry, $subname) = @explode($this->_delim, $subname, 2);
            $currentFolder = $currentFolder->$entry;
            if(!$subname) {
                break;
            }
        }

        if($currentFolder->getGlobalName() != rtrim($rootFolder, $this->_delim)) {
            throw Zend::Exception('Zend_Mail_Exception', "folder $rootFolder not found");
        }
        return $currentFolder;
    }

    /**
     * select given folder
     *
     * folder must be selectable!
     *
     * @param Zend_Mail_Folder|string global name of folder or instance for subfolder
     * @throws Zend_Mail_Exception
     */
    public function selectFolder($globalName)
    {
        // TODO: check $globalName for ..! could be user submitted data
        $this->_currentFolder = (string)$globalName;
        // rootdir is same as INBOX in maildir
        if(strpos($this->_currentFolder, 'INBOX') === 0) {
            $this->_currentFolder = substr($this->_currentFolder, 6);
        }
        try {
            $this->_openMaildir($this->_currentFolder ? $this->_rootdir . '.' . $this->_currentFolder : $this->_rootdir);
        } catch(Zend_Mail_Exception $e) {
            // check what went wrong
            // if folder does not exist getFolders() throws an exception
            if(!$this->getFolders($this->_currentFolder)->isSelectable()) {
                throw Zend::Exception('Zend_Mail_Exception', "{$this->_currentFolder} is not selectable");
            }
            // seems like file has vanished; rebuilding folder tree - but it's still an exception
            $this->_buildFolderTree($this->_rootdir);
            throw Zend::Exception('Zend_Mail_Exception', 'seems like the mbox file has vanished, I\'ve rebuild the ' .
                                                         'folder tree, search for an other folder and try again');
        }
    }

    /**
     * get Zend_Mail_Folder instance for current folder
     *
     * @return Zend_Mail_Folder instance of current folder
     * @throws Zend_Mail_Exception
     */
    public function getCurrentFolder()
    {
        return $this->_currentFolder;
    }
}