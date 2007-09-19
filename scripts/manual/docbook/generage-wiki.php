<?php

error_reporting(E_ALL);

require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/library/Confluence/Book.php';
require_once dirname(__FILE__) . '/library/Confluence/Chapter.php';

$lng = isset($argv[1]) ? $argv[1] : 'en';
$incubator  = '';
$stylesheet = dirname(__FILE__) . '/xsl/wiki.xsl';

switch($lng) {
    case 'en':
        $space = 'ZFDOCDEV';
        break;
    case 'incubator':
        $space = 'ZFDOC';
        $lng   = 'en';
        $incubator = 'incubator/';
        break;
    default:
        $space = 'DOCDEV' . strtoupper($lng);
        break;
}

$path = dirname(dirname(dirname(dirname(__FILE__)))) . '/documentation/manual/' . $lng . '/manual.xml.in';

echo 'Running wikification';
echo "\n\t date: "      . date('Y-m-d H:i:s');
echo "\n\t styleshet: " . $stylesheet;
echo "\n\t language: "  . $lng;
echo "\n\t space: "     . $space;
echo "\n\t path: "      . $path;

$book  = new Confluence_Book($path);
$book  = $book->get();

$soap  = new SoapClient('http://framework.zend.com/wiki/rpc/soap-axis/confluenceservice-v1?wsdl');
$token = $soap->login($confluenceUser, $confluencePass);
$home  = $soap->getPage($token, $space, 'Home');

$jira  = new SoapClient('http://framework.zend.com/issues/rpc/soap/jirasoapservice-v2?wsdl');
$jiraToken   = $jira->login($jiraUser, $jiraPass);
$_components = $jira->getComponents($jiraToken, 'ZF');

$components = array();
foreach($_components as $key => $component) {
    $components[$component->name] = $component->id;
}

$new     = array();
$prev    = array();
$compare = array();

echo "\n\nChecking for appendix page ... ";

try {
    $appendix = $soap->getPage($token, $space, 'Appendix');
    echo 'exists';
} catch (Exception $e) {
    echo 'does not exist - creating';

    $page = new stdClass;
    $page->id          = false;
    $page->version     = 1;
    $page->permissions = true;
    $page->current     = true;
    $page->homePage    = false;
    $page->space       = $space;
    $page->title 	   = 'Appendix';
    $page->content     = '{children}';
    $page->parentId    = false;

    $soap->storePage($token, $page);
    $appendix = $soap->getPage($token, $space, 'Appendix');

    array_push($new, 'Appendix');
}

$current = $soap->getPages($token, $space);

echo "\nQuerying current wiki pages: ";
foreach ($current as $key => $wikiPage) {
    $key = md5($wikiPage->parentId . trim(substr($wikiPage->title, strpos($wikiPage->title, '. ') + 2)));
    $compare[$key] = $wikiPage;

    echo "\n\n\t title: " . $wikiPage->title;
    echo "\n\t hash: " . $key;

    array_push($prev, $wikiPage->title);
}

// process chapters
$i = 0;
foreach ($book->chapter as $chapter) {
    $i++;

    $mainChapter = new Confluence_Chapter($chapter, $i . '. ');
    $subChapter  = new Confluence_Chapter($chapter->sect1[0], $i . '. ');
    $compareKey  = md5($home->id . $mainChapter->getTitle(false));

    echo "\n\n" . $mainChapter->getTitle();
    echo "\n\t compare key: " . $compareKey;
    echo "\n\t page status: ";

    array_push($new, $mainChapter->getTitle());

    $wiki = $subChapter->getWiki($stylesheet);
    if (isset($components[$mainChapter->getTitle(false)])) {
        $wiki .= "\n\nh1. Jira issues\n";
        $wiki .= "{jiraissues:url=http://framework.zend.com/issues/secure/IssueNavigator.jspa?view=rss&&pid=10000&resolution=-1&component=" . $components[$mainChapter->getTitle(false)] . "&sorter/field=issuekey&sorter/order=DESC&tempMax=25&reset=true&decorator=none}\n\n{scrollbar}";
    }

    if (array_key_exists($compareKey, $compare)) {
        echo 'existing page - updating';

        $page = $soap->getPage($token, $space, $compare[$compareKey]->title);

        echo "\n\t queried page: " . $page->title;
        echo "\n\t page version: " . $page->version;
        echo "\n\t page parentId: " . $page->parentId;

        $page->permissions = true;
        $page->current     = true;
        $page->homePage    = false;
        $page->title       = $mainChapter->getTitle();
        $page->content     = $wiki;
        $page->parentId    = $home->id;
    } else {
        echo 'new page - creating';

        $page = new stdClass;
        $page->id          = false;
        $page->version     = 1;
        $page->permissions = true;
        $page->current     = true;
        $page->homePage    = false;
        $page->space       = $space;
	    $page->title 	   = $mainChapter->getTitle();
        $page->content     = $wiki;
        $page->parentId    = $home->id;
    }

    echo "\n\t storing status: ";
    try {
        $soap->storePage($token, $page);
        echo "success";
    } catch (Exception $e) {
        echo "failed - " . $e->getMessage();
    }

    try {
        $mainPage = $soap->getPage($token, $space, $mainChapter->getTitle());
    } catch (Exception $e) {
        continue;
    }

    $b = 0;
    $count = count($chapter->sect1)-1;

    for ($x = 1; $x <= $count; $x++) {
        $b += 0.1;
        $subChapter = new Confluence_Chapter($chapter->sect1[$x], $i+$b . '. ');
        $compareKey = md5($mainPage->id . $subChapter->getTitle(false));

        echo "\n\n" . $subChapter->getTitle();
        array_push($new, $subChapter->getTitle());

        if (array_key_exists($compareKey, $compare)) {
            echo ' existing page - updating';

            $page = $soap->getPage($token, $space, $compare[$compareKey]->title);

            echo "\n\t queried page: " . $page->title;
            echo "\n\t page version: " . $page->version;
            echo "\n\t page parentId: " . $page->parentId;

            $page->permissions = true;
            $page->current     = true;
            $page->homePage    = false;
            $page->title       = $subChapter->getTitle();
            $page->content     = $subChapter->getWiki($stylesheet);
            $page->parentId    = $mainPage->id;
        } else {
            echo 'new page - creating';

            $page = new stdClass;
            $page->id          = false;
            $page->version     = 1;
            $page->permissions = true;
            $page->current     = true;
            $page->homePage    = false;
            $page->space       = $space;
    	    $page->title 	   = $subChapter->getTitle();
            $page->content     = $subChapter->getWiki($stylesheet);
            $page->parentId    = $mainPage->id;
        }

        echo "\n\t storing status: ";
        try {
            $soap->storePage($token, $page);
            echo "success";
        } catch (Exception $e) {
            echo "failed - " . $e->getMessage();
        }
    }
}

// process appendix
$i = 0;
foreach ($book->appendix as $chapter) {
    $i++;

    $mainChapter = new Confluence_Chapter($chapter, $i . '. ');
    $subChapter  = new Confluence_Chapter($chapter->sect1[0], $i . '. ');
    $compareKey  = md5($appendix->id . $mainChapter->getTitle(false));

    echo "\n\n" . $mainChapter->getTitle();
    echo "\n\t compare key: " . $compareKey;
    echo "\n\t page status: ";

    array_push($new, $mainChapter->getTitle());

    if (array_key_exists($compareKey, $compare)) {
        echo ' existing page - updating';

        $page = $soap->getPage($token, $space, $compare[$compareKey]->title);

        echo "\n\t queried page: " . $page->title;
        echo "\n\t page version: " . $page->version;
        echo "\n\t page parentId: " . $page->parentId;

        $page->permissions = true;
        $page->current     = true;
        $page->homePage    = false;
        $page->title       = $mainChapter->getTitle();
        $page->content     = $subChapter->getWiki($stylesheet);
        $page->parentId    = $appendix->id;
    } else {
        echo 'new page - creating';

        $page = new stdClass;
        $page->id          = false;
        $page->version     = 1;
        $page->permissions = true;
        $page->current     = true;
        $page->homePage    = false;
        $page->space       = $space;
	    $page->title 	   = $mainChapter->getTitle();
        $page->content     = $subChapter->getWiki($stylesheet);
        $page->parentId    = $appendix->id;
    }

    echo "\n\t storing status: ";
    try {
        $soap->storePage($token, $page);
        echo "success";
    } catch (Exception $e) {
        echo "failed - " . $e->getMessage();
    }

    try {
        $mainPage = $soap->getPage($token, $space, $mainChapter->getTitle());
    } catch (Exception $e) {
        continue;
    }

    $b = 0;
    $count = count($chapter->sect1)-1;

    for ($x = 1; $x <= $count; $x++) {
        $b += 0.1;
        $subChapter = new Confluence_Chapter($chapter->sect1[$x], $i+$b . '. ');
        $compareKey = md5($mainPage->id . $subChapter->getTitle(false));

        echo "\n\n" . $subChapter->getTitle();
        array_push($new, $subChapter->getTitle());

        if (array_key_exists($compareKey, $compare)) {
            echo 'existing page - updating';

            $page = $soap->getPage($token, $space, $compare[$compareKey]->title);

            echo "\n\t queried page: " . $page->title;
            echo "\n\t page version: " . $page->version;
            echo "\n\t page parentId: " . $page->parentId;

            $page->permissions = true;
            $page->current     = true;
            $page->homePage    = false;
            $page->title       = $subChapter->getTitle();
            $page->content     = $subChapter->getWiki($stylesheet);
            $page->parentId    = $mainPage->id;
        } else {
            echo 'new page - creating';

            $page = new stdClass;
            $page->id          = false;
            $page->version     = 1;
            $page->permissions = true;
            $page->current     = true;
            $page->homePage    = false;
            $page->space       = $space;
    	    $page->title 	   = $subChapter->getTitle();
            $page->content     = $subChapter->getWiki($stylesheet);
            $page->parentId    = $mainPage->id;
        }

        echo "\n\t storing status: ";
        try {
            $soap->storePage($token, $page);
            echo "success";
        } catch (Exception $e) {
            echo "failed - " . $e->getMessage();
        }
    }
}

// remove deleted pages
if (count($book->chapter)) {
    $diff = array_diff($prev, $new);
    foreach ($diff as $key => $pagename) {
        if ($pagename != 'Home' && $pagename != 'manual-template' && $pagename != 'Appendix' && $pagename != 'manual-note') {
            echo 'Removing ' . $pagename . "\n";
            try {
                $page = $soap->getPage($token, $space, $pagename);
                $soap->removePage($token, $page->id);
            } catch (Exception $e) {
                continue;
            }

        }
    }
}

