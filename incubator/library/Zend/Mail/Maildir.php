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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Mail_Abstract
 */
require_once 'Zend/Mail/Abstract.php';

/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * Zend_Mail_Exception
 */
require_once 'Zend/Mail/Exception.php';


/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Maildir extends Zend_Mail_Abstract
{
    private $_files = array();
    private static $_knownFlags = array('P' => 'Passed',
                               'R' => 'Replied',
                               'S' => 'Seen',
                               'T' => 'Trashed',
                               'D' => 'Draft',
                               'F' => 'Flagged');

    /**
     * Count messages all messages in current box
     * Flags are not supported (exceptions is thrown)
     *
     * @param  int $flags           filter by flags
     * @throws Zend_Mail_Exception
     * @return int                  number of messages
     */
    public function countMessages($flags = null)
    {
        return count($this->_files);
    }


    /**
     * Get a list of messages with number and size
     *
     * @param  int        $id  number of message
     * @return int|array      size of given message of list with all messages as array(num => size)
     */
    public function getSize($id = null)
    {

        if($id !== null) {
            if(!isset($this->_files[$id - 1])) {
                throw new Zend_Mail_Exception('id does not exist');
             }
            return filesize($this->_files[$id - 1]['filename']);
        }

        $result = array();
        foreach($this->_files as $num => $pos) {
            $result[$num + 1] = filesize($this->_files[$num]['filename']);
        }

        return $result;
    }



    /**
     * Get a message with headers and body
     *
     * @param  int $id            number of message
     * @return Zend_Mail_Message
     */
    public function getMessage($id)
    {
        if(!isset($this->_files[$id - 1])) {
            throw new Zend_Mail_Exception('id does not exist');
        }

        return new Zend_Mail_Message(file_get_contents($this->_files[$id - 1]['filename']));
    }


    /**
     * Get a message with only header and $bodyLines lines of body
     *
     * @param  int $id            number of message
     * @param  int $bodyLines     also retrieve this number of body lines
     * @return Zend_Mail_Message
     */
    public function getHeader($id, $bodyLines = 0)
    {
        if(!isset($this->_files[$id - 1])) {
            throw new Zend_Mail_Exception('id does not exist');
        }

        $inHeader = true;
        $message = '';
        $fh = fopen($this->_files[$id - 1]['filename'], 'r');
        while(!feof($fh) && ($inHeader || $bodyLines--)) {
            $line = fgets($fh);
            if ($inHeader && !trim($line)) {
                if (!$bodyLines) {
                    break;
                } else {
                    $inHeader = false;
                }
            }
            $message .= $line;
        }
        fclose($fh);

        if (!$inHeader) {
            return new Zend_Mail_Message($message);
        } else {
            return new Zend_Mail_Message('', $message);
        }
    }


    /**
     * Create instance with parameters
     * Supported parameters are:
     *   - filename filename of mbox file
     *
     * @param  $params              array mail reader specific parameters
     * @throws Zend_Mail_Exception
     */
    public function __construct($params)
    {
        if (!isset($params['dirname']) || !is_dir($params['dirname'])) {
            throw new Zend_Mail_Exception('no valid dirname given in params');
        }

        if(!$this->_isMaildir($params['dirname'])) {
            throw new Zend_Mail_Exception('invalid maildir given');
        }

        $this->_has['top'] = true;
        $this->_openMaildir($params['dirname']);
    }

    /**
     * check if a given dir is a valid maildir
     *
     * @param string $dirname name of dir
     * @return bool dir is valid maildir
     */
    protected function _isMaildir($dirname)
    {
        return is_dir($dirname . '/cur');
    }

    /**
     * open given dir as current maildir
     *
     * @param string $dirname name of maildir
     * @throws Zend_Mail_Exception
     */
    protected function _openMaildir($dirname)
    {
        if($this->_files) {
            $this->close();
        }

        $dh = @opendir($dirname . '/cur/');
        if(!$dh) {
            throw new Zend_Mail_Exception('cannot open maildir');
        }
        while(($entry = readdir($dh)) !== false) {
            if($entry[0] == '.' || !is_file($dirname . '/cur/' . $entry)) {
                continue;
            }
            list($uniq, $info) = explode(':', $entry, 2);
            list($version, $flags) = explode(',', $info, 2);
            if($version != 2) {
                $flags = '';
            } else {
                $named_flags = array();
                $length = strlen($flags);
                for($i = 0; $i < $length; ++$i) {
                    $flag = $flags[$i];
                    $named_flags[$flag] = isset(self::$_knownFlags[$flag]) ? self::$_knownFlags[$flag] : '';
                }
            }

            $this->_files[] = array('uniq'     => $uniq,
                                    'flags'    => $named_flags,
                                    'filename' => $dirname . '/cur/' . $entry);
        }
        closedir($dh);
    }


    /**
     * Close resource for mail lib. If you need to control, when the resource
     * is closed. Otherwise the destructor would call this.
     *
     * @return void
     */
    public function close()
    {
        $this->_files = array();
    }


    /**
     * Waste some CPU cycles doing nothing.
     *
     * @return void
     */
    public function noop()
    {
        return true;
    }


    /**
     * stub for not supported message deletion
     */
    public function removeMessage($id)
    {
        throw new Zend_Mail_Exception('maildir is (currently) read-only');
    }

}
