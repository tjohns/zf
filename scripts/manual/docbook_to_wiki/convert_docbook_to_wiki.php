<?php

require_once 'libs/Conversion.php';

$path  = '../../../documentation/manual/en/module_specs/';
$style = './xsl/wiki.xsl';

$confluenceWsdl  = 'http://framework.zend.com/wiki//rpc/soap-axis/confluenceservice-v1?wsdl';
$confluenceUser  = '';
$confluencePass  = '';
$confluenceSpace = 'ZFDOCDEV';

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
    $conversion = new Conversion();
    $filename   = substr(basename($wikipage), 0, -4);

    $soap  = new SoapClient($confluenceWsdl);
    $token = $soap->login($confluenceUser, $confluencePass);
    
    echo $filename;
    echo "\n";
    
    $parentPage = $soap->getPage($token, $confluenceSpace, 'Home');
    
    $page = new stdClass;
    $page->id          = false;
    $page->permissions = true;
    $page->parentId    = false;
    $page->current     = true;
    $page->homePage    = false;
    $page->version     = false;
    $page->space       = $confluenceSpace;
    $page->title       = $filename;
    $page->content     = $conversion->convert($wikipage, $style);
                  
    if ((strpos($filename, '_')) && (!strpos($filename, '-'))) {
        $parents = explode('_', $filename);
        array_pop($parents);
        $parent = implode('_', $parents);
                                                       
        if ($parent !== 'Zend') {
            try {
                $parentPage = $soap->getPage($token, $confluenceSpace, $parent);
            } catch (Exception $e) {
                $parentPage = $soap->getPage($token, $confluenceSpace, 'Home');
            }
        }
    }
    
    $page->parentId = $parentPage->id;
    
    $soap->storePage($token, $page);
}

?>