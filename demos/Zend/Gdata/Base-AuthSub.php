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
 * @package    Zend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Demonstrating Zend_Gdata for reading Google Base entries
 * and using AuthSub authentication.
 */

$D = DIRECTORY_SEPARATOR;
set_include_path(
    dirname(__FILE__) . "{$D}..{$D}..{$D}..{$D}library"
    . PATH_SEPARATOR . get_include_path());

require_once 'Zend.php';
require_once 'Zend/Gdata/Base.php';
require_once 'Zend/Gdata/AuthSub.php';

/**
 * Google Base location
 */
$uri = Zend_Gdata_Base::BASE_FEED_URI;

/**
 * Developer Key - you need to enter your developer key to make this demo work
 * See http://code.google.com/apis/base/signup.html to get a developer key.
 */
/*
$key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
if (preg_match('/^X*$/', $key)) {
    echo "<h1>Configuration incomplete</h1>\n";
    echo "<p>You need to edit the script and enter your <b>Google developer key</b> to make this demo work.</p>\n";
    echo "<p>See <a href='http://code.google.com/apis/base/signup.html'>http://code.google.com/apis/base/signup.html</a> to get a developer key.</p>\n";
    exit();
}
 */
session_start();

if (!isset($_SESSION['base_token'])) {
    if (isset($_GET['token'])) {
        /**
         * Convert the single-use token to a session token.
         */
        $session_token =  Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
        $_SESSION['base_token'] = $session_token;
    } else {
        /**
         * Display a link to generate a single-use token.
         */
        $googleUri = Zend_Gdata_AuthSub::getAuthSubTokenUri(
            'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
            $uri, 0, 1);
        echo "Click <a href='$googleUri'>here</a> to authorize this application.";
        exit();
    }
}

/**
 * Create an authenticated HTTP Client to talk to Google.
 */
$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['base_token']);

/**
 * Submit query to Google Base
 */
$q='';
if (isset($_GET['q']) && $_GET['q']) {
    $q = $_GET['q'];	
    if (get_magic_quotes_gpc()) {
        $q = stripslashes($q);
    }
    $gdata = new Zend_Gdata_Base($client);
    /* @todo delete:
     * Google Base requires developer key
    $gdata->setDeveloperKey($key);
    $feed = $gdata->getFeed($uri . '?bq=' . urlencode($q) . '&max-results=' . urlencode($_GET['maxresults']));
     */
    $gdata->setQuery($q);
    $feed = $gdata->getFeed();
}

/**
 * Filter php_self to avoid a security vulnerability.
 */
$php_self = htmlentities(substr($_SERVER['PHP_SELF'], 0, strcspn($_SERVER['PHP_SELF'], "\n\r")), ENT_QUOTES);

/**
 * Logout and revoke AuthSub token when we are done with it.
 */
if (isset($_GET['logout'])) {
    Zend_Gdata_AuthSub::AuthSubRevokeToken($_SESSION['base_token']);
    unset($_SESSION['base_token']);
    header('Location: ' . $php_self);
    exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Google Base Demo</title>
<style>
body{ font-family: Arial; }
input,select{font-size: 32px;}
</style>
</head>
<body>
<p><a href="<?= $php_self ?>?logout">Log Out</a></p>
<form action="<?= $php_self ?>" method="GET">
<h1>Search Google Base<br> 
<input type="text" name="q" value="<?php echo htmlentities($q); ?>">
<select name="maxresults">
    <option value="5">5</option>
    <option value="10">10</option>
</select>
<input type="submit" value="Search">
</h1>
</form>
<?
if (isset($feed)) {
?>
    <ol>
<?php
    foreach ($feed as $feed_entry) {
        $link_list = $feed_entry->link();
        $href = $link_list[0]->getAttribute('href');
?>
        <li>
        <a href="<?= $href ?>"><?= $feed_entry->title() ?></a><br>
        <?= $feed_entry->author() ?>
        </li>
<?php
    }
?>
    </ol>
<?php
}
?>
</body>
</html>

