<?php

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