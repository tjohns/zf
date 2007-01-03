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
 * @package    Zend_Session
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * This is a stub, does this belong? Can its efforts be more focused?  For the community to decide.
 *
 * @todo
 *
 * NOT TO GO INTO CORE
 */



class Zend_Session_Debug
{

    static public function dumpMemory()
    {
        echo "<pre>";
        echo "In Memory:\n";
        print_r($_SESSION);
    }


    static public function dumpDisk($id)
    {
        $path = (strpos(($full_path = session_save_path()), ";")) ? substr(strrchr($full_path, ";"), 1) : $full_path;
        $file = $path . DIRECTORY_SEPARATOR . "sess_" . $id;

        echo "<pre>";
        echo "On Disk: " . filectime($file) . "\n";
        print_r(self::unserialize_session_data(file_get_contents($file)));
    }

    static private function unserialize_session_data( $serialized_string )
    {
        $variables = array();

        $a = preg_split( "/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );

        for( $i = 0; $i < count( $a ); $i = $i+2 )
            $variables[$a[$i]] = unserialize( $a[$i+1] );

        return $variables;
    }
}