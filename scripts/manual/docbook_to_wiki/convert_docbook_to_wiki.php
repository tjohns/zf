<?php

$path            = '../../../documentation/manual/en/module_specs/';
$style           = './xsl/wiki.xsl';
$confluenceWsdl  = 'http://framework.zend.com/wiki//rpc/soap-axis/confluenceservice-v1?wsdl';
$confluenceUser  = '';
$confluencePass  = '';
$confluenceSpace = 'ZFDOCDEV';

function processIncludes($haystack)
{
    preg_match_all('/&module_specs.([a-zA-Z_]*);/', $haystack, $matches);
    
    if (count($matches)) {
        for ($i = 0; $i < count($matches[0]); $i++) {
            $haystack = str_replace($matches[0][$i], '', $haystack);
        }
    }
    
    return $haystack;
}

function stripSpaces($haystack)
{
    return preg_replace('/\s+/u', ' ', $haystack[0]);
}

function stripSpacesNewline($haystack)
{
    return preg_replace('/\s+/u', ' ', $haystack[0]) . "\n";
}

$dir   = new DirectoryIterator($path);
$pages = array();

while($dir->valid()) {    
    if((!$dir->isDot()) && (substr($dir->getFilename(), -3) == 'xml')) {
        array_push($pages, $dir->getPathname());
    }
    
    $dir->next();
}

asort($pages);

foreach ($pages as $key => $wikipage) {
    
    $tmp = processIncludes(file_get_contents($wikipage));
    
    $tmp = preg_replace_callback('/(?:<para>\s*)+(.+?)<\/para>/si',         'stripSpaces', $tmp);
    $tmp = preg_replace_callback('/(?:<note>\s*)+(.+?)<\/note>/si',         'stripSpaces', $tmp);
    $tmp = preg_replace_callback('/(?:<tip>\s*)+(.+?)<\/tip>/si',           'stripSpaces', $tmp);
    $tmp = preg_replace_callback('/(?:<thead>\s*)+(.+?)<\/thead>/si',       'stripSpaces', $tmp);
    $tmp = preg_replace_callback('/(?:<tbody>\s*)+(.+?)<\/tbody>/si',       'stripSpaces', $tmp);
    $tmp = preg_replace_callback('/(?:<tip>\s*)+(.+?)<\/tip>/si',           'stripSpaces', $tmp);
    $tmp = preg_replace_callback('/(?:<listitem>\s*)+(.+?)<\/listitem>/si', 'stripSpaces', $tmp);
    
    $tmp = preg_replace_callback('/(?:<row>\s*)+(.+?)<\/row>/si',           'stripSpacesNewLine', $tmp);
    
    $data = '<chapter>';
    $data.= $tmp;
    $data.= '</chapter>';
    
    $xml = new DOMDocument;
    $xsl = new DOMDocument();
    
    $xml->loadXML($data);
    $xsl->load($style);
    
    $proc = new XSLTProcessor;
    $proc->importStyleSheet($xsl);
        
    $temp = html_entity_decode($proc->transformToXML($xml));
    $temp = preg_replace('/^(\\s*)(\\|\\|[^\\r\\n]+?)(\\s+)(\\|[^\\|]+)/', "\\2||\r\n\\4", $temp);
    $temp = preg_replace('/^(\\s*)\\|([^|]+.+?)(\\s*)$/m', '|\\2|', $temp);
    
    $filename   = substr(basename($wikipage), 0, -4);
    $soap       = new SoapClient($confluenceWsdl);
    $token      = $soap->login($confluenceUser, $confluencePass);
    $origiFile  = $filename;
    
    echo 'Parsing ' . $filename;
    echo "\n";
    
    $parentPage = $soap->getPage($token, $confluenceSpace, 'Home');
                      
    if (strpos($filename, '_')) {
        if (strpos($filename, '-')) {
            $filename = substr($filename, 0, strpos($filename, '-'));
            $parent   = $filename;
        } else {
            $parents = explode('_', $filename);
            array_pop($parents);
            $parent = implode('_', $parents);
            $filename = $parent;
        }
        
        if ($parent !== 'Zend') {
            try {
                $parentPage = $soap->getPage($token, $confluenceSpace, $parent);
            } catch (Exception $e) {
                
                $content = '{section}' . "\n" .
                           '{column:width=330}{pageTree:root=Home}' . "\n" .
                           '{column}' . "\n" .
                           '{column}' . "\n" .
                           '{toc:style=none|indent=25px}' . "\n" .
                           '{children}' . "\n" .
                           '{column}' . "\n" .
                           '{section}';
                
                $page = new stdClass;
                $page->id          = false;
                $page->permissions = true;
                $page->parentId    = false;
                $page->current     = true;
                $page->homePage    = false;
                $page->version     = false;
                $page->space       = $confluenceSpace;
                $page->title       = $filename;
                $page->content     = $content;
                $page->parentId    = $parentPage->id;
                
                $soap->storePage($token, $page);
                
                $parentPage = $soap->getPage($token, $confluenceSpace, $filename);
            }
        }
    }
    
    $page = new stdClass;
    $page->id          = false;
    $page->permissions = true;
    $page->parentId    = false;
    $page->current     = true;
    $page->homePage    = false;
    $page->version     = false;
    $page->space       = $confluenceSpace;
    $page->title       = $origiFile;
    $page->content     = $temp;
    $page->parentId    = $parentPage->id;
    
    $soap->storePage($token, $page);
}

?>