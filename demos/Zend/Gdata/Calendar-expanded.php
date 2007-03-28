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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2006-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the Google Calendar data API.  Utilizes the 
 * Zend Framework Gdata components to communicate with the Google API.
 * 
 * Requires the Zend Framework Gdata components and PHP >= 5.1.4
 *
 * You can run this sample both from the command line (CLI) and also
 * from a web browser.  When running through a web browser, only
 * AuthSub and outputting a list of calendars is demonstrated.  When
 * running via CLI, all functionality except AuthSub is available and dependent
 * upon the command line options passed.  Run this script without any
 * command line options to see usage, eg:
 *     /usr/local/bin/php -f Calendar-expanded.php
 *
 * More information on the Command Line Interface is available at:
 *     http://www.php.net/features.commandline
 *
 * NOTE: You must ensure that the Zend Framework is in your PHP include
 * path.  You can do this via php.ini settings, or by modifying the 
 * argument to set_include_path in the code below.
 *
 * NOTE: As this is sample code, not all of the functions do full error
 * handling.  Please see getAtomEntry for an example of how errors could
 * be handled and the online code samples for additional information.
 */

// includes the Zend Framework library and loads the necessary classes
$D = DIRECTORY_SEPARATOR;
set_include_path(
    dirname(__FILE__) . "{$D}..{$D}..{$D}..{$D}library"
    . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Feed_EntryAtom');
Zend_Loader::loadClass('Zend_Http_Client');

/**
 * Returns the full URL of the current page, based upon env variables
 * 
 * Env variables used:
 * $_SERVER['HTTPS'] = (on|off|)
 * $_SERVER['HTTP_HOST'] = value of the Host: header
 * $_SERVER['HTTP_PORT'] = port number (only used if not http/80,https/443
 * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
 *
 * @return string Current URL
 */
function getCurrentUrl() 
{
  global $_SERVER;

  /**
   * Filter php_self to avoid a security vulnerability.
   */
  $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

  if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
    $protocol = 'https://';
  } else {
    $protocol = 'http://';
  }
  $host = $_SERVER['HTTP_HOST'];
  if ($_SERVER['HTTP_PORT'] != '' &&
     (($protocol == 'http://' && $_SERVER['HTTP_PORT'] != '80') ||
     ($protocol == 'https://' && $_SERVER['HTTP_PORT'] != '443'))) {
    $port = ':' . $_SERVER['HTTP_PORT'];
  } else {
    $port = '';
  }
  return $protocol . $host . $port . $php_request_uri;
}

/**
 * Returns the AuthSub URL which the user must visit to authenticate requests 
 * from this application.
 *
 * Uses getCurrentUrl() to get the next URL which the user will be redirected
 * to after successfully authenticating with the Google service.
 *
 * @return string AuthSub URL
 */
function getAuthSubUrl() 
{
  $next = getCurrentUrl();
  $scope = 'http://www.google.com/calendar/feeds/';
  $secure = false;
  $session = true;
  return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, 
      $session);
}

/**
 * Outputs a request to the user to login to their Google account, including
 * a link to the AuthSub URL.
 * 
 * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
 */
function requestUserLogin($linkText) 
{
  $authSubUrl = getAuthSubUrl();
  echo "<a href=\"{$authSubUrl}\">{$linkText}</a>"; 
}

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using AuthSub authentication.
 *
 * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
 * it is obtained.  The single use token supplied in the URL when redirected 
 * after the user succesfully authenticated to Google is retrieved from the 
 * $_GET['token'] variable.
 *
 * @return Zend_Http_Client
 */
function getAuthSubHttpClient() 
{
  global $_SESSION, $_GET;
  if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
    $_SESSION['sessionToken'] = 
        Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
  } 
  $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
  return $client;
}

/**
 * Processes loading of this sample code through a web browser.  Uses AuthSub
 * authentication and outputs a list of a user's calendars if succesfully 
 * authenticated.
 *
 * @return void
 */
function processPageLoad() 
{
  global $_SESSION, $_GET;
  if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
    requestUserLogin('Please login to your Google Account.');
  } else {
    $client = getAuthSubHttpClient();
    outputCalendarList($client);
  }
}

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using the ClientLogin credentials supplied.
 *
 * @param string $user The username, in e-mail address format, to authenticate
 * @param string $pass The password for the user specified
 * @return Zend_Http_Client
 */
function getClientLoginHttpClient($user, $pass) 
{
  $service = 'cl'; // the service name for calendar

  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing an event
 * in the user's calendar.  The calendar is retrieved using the magic cookie
 * which allows read-only access to private calendar data using a special token
 * available from within the Calendar UI.
 *
 * @param string $user The username or address of the calendar to be retrieved.
 * @param string $magicCookie The magic cookie token
 * @return void
 */
function outputCalendarMagicCookie($user, $magicCookie) 
{
  $client = new Zend_Http_Client();
  $gdataCal = new Zend_Gdata_Calendar($client);
  $gdataCal->setUser($user);
  $gdataCal->setVisibility('private-' . $magicCookie);
  $gdataCal->setProjection('full');
  $eventFeed = $gdataCal->getCalendarFeed();
  echo "<ul>\n";
  foreach ($eventFeed as $event) {
    echo "\t<li>" . $event->title() . "</li>\n";
  }
  echo "</ul>\n";
}

/** 
 * Outputs an HTML unordered list (ul), with each list item representing a
 * calendar in the authenticated user's calendar list.  
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @return void
 */
function outputCalendarList($client) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $calFeed = $gdataCal->getCalendarListFeed();
  echo "<h1>" . $calFeed->title() . "</h1>\n";
  echo "<ul>\n";
  foreach ($calFeed as $calendar) {
    echo "\t<li>" . $calendar->title() . "</li>\n";
  }
  echo "</ul>\n";
} 

/**
 * Outputs an HTML unordered list (ul), with each list item representing an
 * event on the authenticated user's calendar.  Includes the start time and
 * event ID in the output.  Events are ordered by starttime and include only
 * events occurring in the future.
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @return void
 */
function outputCalendar($client) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $gdataCal->setUser('default');
  $gdataCal->setVisibility('private');
  $gdataCal->setProjection('full');
  $gdataCal->setOrderby('starttime');
  // a setter method for the futureevents query parameter is not available 
  // in Zend_Gdata_Calendar, but it can be set using the "magic" setter methods
  // which are implicitly invoked by setting the 'futureevents' identifier 
  // as if it was a member of the $gdataCal object
  $gdataCal->futureevents = 'true';
  $eventFeed = $gdataCal->getCalendarFeed();
  echo "<ul>\n";
  foreach ($eventFeed as $event) {
    echo "\t<li>" . $event->title() . ' - ' . $event->{'gd:when'}['startTime'] . 
         ' (' . $event->id() . ")</li>\n";
  }
  echo "</ul>\n";
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing an
 * event on the authenticated user's calendar which occurs during the 
 * specified date range.
 * 
 * To query for all events occurring on 2006-12-24, you would query for
 * a startDate of '2006-12-24' and an endDate of '2006-12-25' as the upper
 * bound for date queries is exclusive.  See the 'query parameters reference':
 * http://code.google.com/apis/gdata/calendar.html#Parameters
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @param string $startDate The start date in YYYY-MM-DD format
 * @param string $endDate The end date in YYYY-MM-DD format
 * @return void
 */
function outputCalendarByDateRange($client, $startDate, $endDate) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $gdataCal->setUser('default');
  $gdataCal->setVisibility('private');
  $gdataCal->setProjection('full');
  $gdataCal->setOrderby('starttime');
  $gdataCal->setStartMin($startDate);
  $gdataCal->setStartMax($endDate);
  $eventFeed = $gdataCal->getCalendarFeed();
  echo "<ul>\n";
  foreach ($eventFeed as $event) {
    echo "\t<li>" . $event->title() . ' - ' . $event->{'gd:when'}['startTime'] . 
         ' (' . $event->id() . ")</li>\n";
  }
  echo "</ul>\n";
}

/**
 * Creates an event on the authenticated user's default calendar with the
 * specified event details.
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @param string $title The event title
 * @param string $desc The detailed description of the event
 * @param string $startDate The start date of the event in YYYY-MM-DD format
 * @param string $startTime The start time of the event in HH:MM 24hr format
 * @param string $endTime The end time of the event in HH:MM 24hr format
 * @param string $tzOffset The offset from GMT/UTC in [+-]DD format (eg -08)
 * @return void
 */
function createEvent ($client, $title = 'Tennis with Beth', 
    $desc='Meet for a quick lesson', $where = 'On the courts', 
    $startDate = '2008-01-20', $startTime = '10:00', 
    $endDate = '2008-01-20', $endTime = '11:00', $tzOffset = '-08')
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  Zend_Feed::registerNamespace('gd', 'http://schemas.google.com/g/2005');
  $newEntry = new Zend_Feed_EntryAtom();
  $newEntry->title = trim($title);
  $newEntry->{'gd:where'}['valueString'] = $where;

  $newEntry->content = $desc;
  $newEntry->content['type'] = 'text';

  $when = $newEntry->{'gd:when'};
  $when['startTime'] = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
  $when['endTime'] = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";

  $gdataCal->post($newEntry->saveXML());
}

/**
 * Returns an atom entry object representing the event with the specified ID.
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @param string $eventId The event ID string
 * @return Zend_Feed_EntryAtom if the event is found, null if it's not
 */
function getAtomEntry ($client, $eventId) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $gdataCal->setUser('default');
  $gdataCal->setVisibility('private');
  $gdataCal->setProjection('full');
  $gdataCal->setEvent($eventId);

  try {
    $eventFeed = $gdataCal->getCalendarFeed();
    if ($eventFeed->valid()) {
      return $eventFeed->current();
    } else  {
      return null;
    }
  } catch (Zend_Feed_Exception $fe) {
    return null;
  }
}

/**
 * Updates the title of the event with the specified ID to be
 * the title specified.  Also outputs the new and old title
 * with HTML br elements separating the lines
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @param string $eventId The event ID string
 * @param string $newTitle The new title to set on this event 
 * @return void
 */
function updateAtomEntry ($client, $eventId, $newTitle) 
{
  if ($eventOld = getAtomEntry($client, $eventId)) {
    echo "Old title: " . $eventOld->title() . "<br />\n";
    $eventOld->title = $newTitle;
    $eventOld->save();

    $eventNew = getAtomEntry($client, $eventId);
    echo "New title: " . $eventNew->title() . "<br />\n";
  }
}

/**
 * Deletes the event specified by retrieving the atom entry object
 * and calling Zend_Feed_EntryAtom::delete() method.  This is for
 * example purposes only, as it is inefficient to retrieve the entire
 * atom entry only for the purposes of deleting it.
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @param string $eventId The event ID string
 * @return void
 */
function deleteAtomEntryById ($client, $eventId) 
{
  $event = getAtomEntry($client, $eventId);
  $event->delete();
}

/**
 * Deletes the event specified by calling the Zend_Gdata::delete()
 * method.  The URL is typically in the format of:
 * http://www.google.com/calendar/feeds/default/private/full/<eventId>
 *
 * @param Zend_Http_Client $client The authenticated client object
 * @param string $url The url for the event to be deleted 
 * @return void
 */
function deleteAtomEntryByUrl ($client, $url) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $gdataCal->delete($url);
}

/**
 * Main logic for running this sample code via the command line or,
 * for AuthSub functionality only, via a web browser.  The output of
 * many of the functions is in HTML format for demonstration purposes,
 * so you may wish to pipe the output to Tidy when running from the 
 * command-line for clearer results.
 *
 * Run without any arguments to get usage information
 */
if ($argc >= 2) {
  switch ($argv[1]) {
    case 'outputCalendar':
      if ($argc == 4) { 
        $client = getClientLoginHttpClient($argv[2], $argv[3]);
        outputCalendar($client);
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} " .
             "<username> <password>\n";
      }
      break;
    case 'outputCalendarMagicCookie':
      if ($argc == 4) { 
        outputCalendarMagicCookie($argv[2], $argv[3]); 
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} " .
             "<username> <magicCookie>\n";
      }
      break;
    case 'outputCalendarByDateRange':
      if ($argc == 6) {
        $client = getClientLoginHttpClient($argv[2], $argv[3]);
        outputCalendarByDateRange($client, $argv[4], $argv[5]);
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} " . 
             "<username> <password> <startDate> <endDate>\n";
      }
      break;
    case 'outputCalendarList':
      if ($argc == 4) { 
        $client = getClientLoginHttpClient($argv[2], $argv[3]);
        outputCalendarList($client);
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} " .
             "<username> <password>\n";
      }
      break;
    case 'updateAtomEntry':
      if ($argc == 6) { 
        $client = getClientLoginHttpClient($argv[2], $argv[3]);
        updateAtomEntry($client, $argv[4], $argv[5]); 
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} <username> <password> " . 
             "<eventId> <newTitle>\n";
      }
      break;
    case 'deleteAtomEntryById':
      if ($argc == 5) { 
        $client = getClientLoginHttpClient($argv[2], $argv[3]);
        deleteAtomEntryById($client, $argv[4]); 
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} <username> <password> " . 
             "<eventId>\n";
      }
      break;
    case 'deleteAtomEntryByUrl':
      if ($argc == 5) { 
        $client = getClientLoginHttpClient($argv[2], $argv[3]);
        deleteAtomEntryByUrl($client, $argv[4]); 
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} <username> <password> " . 
             "<eventUrl>\n";
      }
      break;
    case 'createEvent':
      if ($argc == 12) { 
        $client = getClientLoginHttpClient($argv[2], $argv[3]);
        createEvent($client, $argv[4], $argv[5], $argv[6], $argv[7], $argv[8],
            $argv[9], $argv[10], $argv[11]);
      } else {
        echo "Usage: php {$argv[0]} {$argv[1]} <username> <password> " . 
             "<title> <description> <where> " .
             "<startDate> <startTime> <endDate> <endTime> <tzOffset>\n";
        echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <password> " . 
             "'Tennis with Beth' 'Meet for a quick lesson' 'On the courts' " .
             "'2008-01-01' '10:00' '2008-01-01' '11:00' '-08'\n";
      }
      break;
  } 
} else if (!isset($_SERVER["HTTP_HOST"]))  {
  // running from command line, but action left unspecified
  echo "Usage: php {$argv[0]} <action> [<username>] [<password>] " .
      "[<arg1> <arg2> ...]\n\n";
  echo "Possible action values include:\n" .
       "outputCalendar\n" . 
       "outputCalendarMagicCookie\n" . 
       "outputCalendarByDateRange\n" .
       "outputCalendarList\n" .
       "updateAtomEntry\n" .
       "deleteAtomEntryById\n" .
       "deleteAtomEntryByUrl\n" .
       "createEvent\n";
} else {
  // running through web server - demonstrate AuthSub
  processPageLoad();
}
