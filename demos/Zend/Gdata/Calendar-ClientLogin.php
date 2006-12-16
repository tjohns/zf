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
 * Demonstrating Zend_Gdata for reading and writing Google Calendar 
 * using ClientLogin authentication. 
 */

$D = DIRECTORY_SEPARATOR;
set_include_path(
    dirname(__FILE__) . "{$D}..{$D}..{$D}..{$D}library"
    . PATH_SEPARATOR . get_include_path());

require_once 'Zend.php';
require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Gdata/ClientLogin.php';

/**
 * Enter your Google account credential here
 */
$email = 'example@example-domain.com';
$passwd = 'xxxxxxxx';
if ($passwd == 'xxxxxxxx') {
    echo "<h1>Configuration incomplete</h1>\n";
    echo "<p>You need to edit the script for this demo and enter your Google account credentials to make this demo work.</p>\n";
    exit();
}

$sharedCalendarOwner = 'ogr93sav88fmf2ssnv851osqm4@group.calendar.google.com';
$myCalendar = 'http://www.google.com/calendar/feeds/default/private/full';

/**
 * Create an authenticated HTTP Client to talk to Google.
 */
$client = Zend_Gdata_ClientLogin::getHttpClient($email, $passwd, 'cl');

/**
 * Filter php_self to avoid a security vulnerability.
 */
$php_self = htmlentities(substr($_SERVER['PHP_SELF'], 0, strcspn($_SERVER['PHP_SELF'], "\n\r")), ENT_QUOTES);

/**
 * Delete an item.
 */
if (isset($_GET['deleteUri'])) {
    $gdataCal = new Zend_Gdata_Calendar($client);
    $gdataCal->delete($_GET['deleteUri']);
    header('Location: ' . $php_self);
    exit();
}

/**
 * Copy an item from the shared calendar to my calendar.
 */
if (isset($_POST['save'])) {
    $gdataCal = new Zend_Gdata_Calendar($client);
    if (get_magic_quotes_gpc()) {
        $_POST['save'] = stripslashes($_POST['save']);
    }
    $gdataCal->post(html_entity_decode($_POST['save']));
    header('Location: ' . $php_self);
    exit();
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Google Calendar Demo</title>
<style>
body{ font-family: Arial; }
</style>
</head>
<body>
<table border="1">
<tr>
<td valign="top">
<?php
/**
 * Get the public calendar feed.
 */
$gdataCal = new Zend_Gdata_Calendar($client);
$gdataCal->setUser($sharedCalendarOwner);
$feed = $gdataCal->getCalendarFeed();
$feed_title = $feed->title();
?>
    <h3><?= $feed_title ?></h3>
<?php
foreach ($feed as $item) {
?>
    <p><?= $item->title() ?>
    
<?php
    $action = $php_self;
    $value = htmlentities($item->saveXML());
    /**
     * Rename the title.
     */
    $item->title = $feed_title . ": " . $item->title();
?>

    <form method="POST" action="<?= $action ?>">
    <input type="hidden" name="save" value="<?= $value ?>">
    <input type="submit" value="Copy">
    </form>
    </p>
<?php
}
?>

</td>
<td valign="top">
<?php
    /**
     * Get my private calendar feed.
     */
    $gdataCal = new Zend_Gdata_Calendar($client);
    $gdataCal->setVisibility(Zend_Gdata_Data::VIS_PRIVATE);
    $feed = $gdataCal->getCalendarFeed();
?>
    <h3><?= $feed->title() ?></h3>
<?php
    foreach ($feed as $item) {
        $href = $php_self;
        $deleteUri = urlencode($item->id());
?>
    <p><?= $item->title() ?><a href="<?= $href ?>?deleteUri=<?= $deleteUri ?>"> [Delete]</a></p>
<?php
    }	
?>
</td>
</tr>
</table>
</body>
</html>
