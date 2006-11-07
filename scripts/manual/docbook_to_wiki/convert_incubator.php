<?php

error_reporting(E_ALL);

$language   	 = $argv[1];
$thisPath   	 = '/home/virtual/andries.zfdev.com/home/andries/scripts/manual/';
$manualPath 	 = '/home/virtual/andries.zfdev.com/home/andries/scripts/trunk/documentation/manual/' . $language . '/';
$manualIncPath 	 = '/home/virtual/andries.zfdev.com/home/andries/scripts/trunk/incubator/documentation/manual/' . $language . '/';
$confluenceWsdl  = 'http://framework.zend.com/wiki/rpc/soap-axis/confluenceservice-v1?wsdl';
$confluenceUser  = '';
$confluencePass  = '';

if ($language == 'en') {
    $confluenceSpace = 'ZFDOC';
} else {
    $confluenceSpace = 'DOCDEV' . strtoupper(str_replace('-', '', $language));
}

require_once $thisPath . 'functions.php';

$file = @file_get_contents($manualPath . 'manual.xml');
$file = preg_replace_callback('/(&module_specs.)(.+)(;)/', 'processChapters', $file);
$sxml = @simplexml_load_string($file);
$soap = new SoapClient($confluenceWsdl);
$ver  = 0.1;

$wikiPages         = array();
$oldwikiPages      = array();
$chapters          = array();
$incubatorChapters = array();

/*
$file = file_get_contents($manualPath . 'manual.xml');
$file = preg_replace_callback('/(&module_specs.)(.+)(;)/', 'processChapters', $file);
$sxml = @simplexml_load_string($file);
*/

$soap = new SoapClient($confluenceWsdl);
$ver  = 0.1;

$chapters          = array();
$incubatorChapters = array();

if ($language == 'en') {
    $incubatorFile = file_get_contents($manualIncPath . 'manual.xml');
    $incubatorFile = preg_replace('/(<!--.*-->)/isU', '', $incubatorFile);
    $incubatorFile = preg_replace_callback('/(&module_specs.)(.+)(;)/', 'processIncubatorChapters', $incubatorFile);
    $isxml         = @simplexml_load_string($incubatorFile);

    foreach ($isxml->chapter as $key => $chapter) {
        array_push($incubatorChapters, $chapter);
    }
}

$token = $soap->login($confluenceUser, $confluencePass);
/*
foreach ($sxml->chapter as $key => $chapter) {
    array_push($chapters, $chapter);
}
*/
if (count($incubatorChapters)) {
    $chapters = array_merge($chapters, $incubatorChapters);
}

$homePage = $soap->getPage($token, $confluenceSpace, 'Home');

foreach ($chapters as $key => $chapter)
{
    $filename = $chapter['id'];
    
    echo $chapter->title . "\n";
    
    $mytmp = $chapter->sect1[0]->asXML();
    $mytmp = cleanup($mytmp);
    
    $xml = new DOMDocument;
    $xsl = new DOMDocument();
    
    $xml->loadXML($mytmp);
    $xsl->load($thisPath . 'xsl/wiki.xsl');
    
    $proc = new XSLTProcessor;
    $proc->importStyleSheet($xsl);
    
    $temp = html_entity_decode($proc->transformToXML($xml));
    $temp = preg_replace('/^(\\s*)(\\|\\|[^\\r\\n]+?)(\\s+)(\\|[^\\|]+)/', "\\2||\r\n\\4", $temp);
    $temp = preg_replace('/^(\\s*)\\|([^|]+.+?)(\\s*)$/m', '|\\2|', $temp);
    
    $title = (string)$chapter->title;
    
    array_push($wikiPages, $title);
    
    try {
	    $page = $soap->getPage($token, $confluenceSpace, $title);
	    
        $page->version = $page->version + 0.1;
        $page->title   = $chapter->title;
        $page->content = $temp;
    } catch (Exception $e) {
        $page = new stdClass;
        $page->id          = false;
        $page->permissions = false;
        $page->parentId    = false;
        $page->current     = false;
        $page->homePage    = false;
        $page->version     = $ver;
        $page->space       = $confluenceSpace;
	    $page->title 	   = $title;
        $page->content     = $temp;
        $page->parentId    = $homePage->id;
    }
    
    $soap->storePage($token, $page);
    $parentPage = $soap->getPage($token, $confluenceSpace, $title);
    
    for ($i = 1; $i < count($chapter->sect1); $i++) {
        
        echo $chapter->sect1[$i]->title . "\n";
        
        $name    = $chapter->sect1[$i]['id'];
        $parent  = explode('.', $name);
        
        array_pop($parent);
        
        $parent   = implode('.', $parent);
        $filename = $chapter->sect1[$i]->title;
                
        $mytmp = $chapter->sect1[$i]->asXML();
        $mytmp = cleanup($mytmp);
        
        $xml = new DOMDocument;
        $xsl = new DOMDocument();
      
        $xml->loadXML($mytmp);
        $xsl->load($thisPath . 'xsl/wiki.xsl');
        
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);
        
        $temp = html_entity_decode($proc->transformToXML($xml));
        $temp = preg_replace('/^(\\s*)(\\|\\|[^\\r\\n]+?)(\\s+)(\\|[^\\|]+)/', "\\2||\r\n\\4", $temp);
        $temp = preg_replace('/^(\\s*)\\|([^|]+.+?)(\\s*)$/m', '|\\2|', $temp);
        
        $title = str_replace('::', ' - ', $chapter->sect1[$i]->title);
        $title = str_replace(':', ' ', $chapter->sect1[$i]->title);
        $title = ($chapter['incubator']) ? $title . ' (incubator)' : $title;
        
        array_push($wikiPages, $title);
        
        try {
	        $page = $soap->getPage($token, $confluenceSpace, $title);
	        
            $page->version = $page->version + 0.1;
            $page->title   = $title;
            $page->content = $temp;
        } catch (Exception $e) {
            $page = new stdClass;
            $page->id          = false;
            $page->permissions = false;
            $page->parentId    = false;
            $page->current     = false;
            $page->homePage    = false;
            $page->version     = false;
            $page->space       = $confluenceSpace;
            $page->title       = $title;
            $page->content     = $temp;
            $page->parentId    = $parentPage->id;
        }
        
        $soap->storePage($token, $page);
    }
}
$diff =  array_diff($oldwikiPages, $wikiPages);

foreach ($diff as $key => $pagename) {
    
    if ($pagename != 'Home' && $pagename != 'manual-template') {
        echo 'Removing ' . $pagename . "\n";
        $page = $soap->getPage($token, $confluenceSpace, $pagename);
        $soap->removePage($token, $page->id);
    }
}
