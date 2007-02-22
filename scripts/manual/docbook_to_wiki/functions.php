<?php

function processChapters($matches)
{
    global $manualPath;
    
    $filename = $matches[2] . '.xml';
    $content  = @file_get_contents($manualPath . 'module_specs/' . $filename);
    $content  = preg_replace_callback('/(&module_specs.)(.+)(;)/', 'processChapters', $content);
    
    $sxml     = @simplexml_load_string($content);
    if ($sxml) {
        return $content;
    } else {
	return '';
    }
}

function processIncubatorChapters($matches)
{
    global $manualIncPath;
    
    $filename = $matches[2] . '.xml';
    
    if (is_readable($manualIncPath . 'module_specs/' . $filename)) {
        $content  = @file_get_contents($manualIncPath . 'module_specs/' . $filename);
        $content  = preg_replace_callback('/(&module_specs.)(.+)(;)/', 'processIncubatorChapters', $content);
        $sxml     = @simplexml_load_string($content);
        
	if ($sxml) {
            return $content;
	} else {
            return '';
	}
        
    } else {
        return processChapters($matches);
    }
}

function stripSpaces($haystack)
{
    return preg_replace('/\s+/u', ' ', $haystack[0]);
}

function stripSpacesNewline($haystack)
{
    return preg_replace('/\s+/u', ' ', $haystack[0]) . "\n";
}

function cleanup($content)
{
    $content = preg_replace_callback('/(?:<para>\s*)+(.+?)<\/para>/si',         'stripSpaces', $content);
    $content = preg_replace_callback('/(?:<note>\s*)+(.+?)<\/note>/si',         'stripSpaces', $content);
    $content = preg_replace_callback('/(?:<tip>\s*)+(.+?)<\/tip>/si',           'stripSpaces', $content);
    $content = preg_replace_callback('/(?:<thead>\s*)+(.+?)<\/thead>/si',       'stripSpaces', $content);
    $content = preg_replace_callback('/(?:<tbody>\s*)+(.+?)<\/tbody>/si',       'stripSpaces', $content);
    $content = preg_replace_callback('/(?:<tip>\s*)+(.+?)<\/tip>/si',           'stripSpaces', $content);
    $content = preg_replace_callback('/(?:<listitem>\s*)+(.+?)<\/listitem>/si', 'stripSpaces', $content);
    
    $content = preg_replace_callback('/(?:<row>\s*)+(.+?)<\/row>/si',           'stripSpacesNewLine', $content);
    
    $data = '<chapter>';
    $data.= $content;
    $data.= '</chapter>';
    
    return $data;
}

?>
