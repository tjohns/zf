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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * Zend_Gdata_Exception
 */
require_once 'Zend/Gdata/Exception.php';

/**
 * Zend_Gdata_App
 */
require_once 'Zend/Gdata/App.php';

/**
 * Zend_Gdata_HttpException
 */
require_once 'Zend/Gdata/HttpException.php';

/**
 * Zend_Gdata_InvalidArgumentException
 */
require_once 'Zend/Gdata/InvalidArgumentException.php';

/**
 * Provides functionality to interact with Google data APIs
 * Subclasses exist to implement service-specific features
 * 
 * As the Google data API protocol is based upon the Atom Publishing Protocol 
 * (APP), GData functionality extends the appropriate Zend_Gdata_App classes
 *
 * @link http://code.google.com/apis/gdata/overview.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata extends Zend_Gdata_App
{

    const AUTH_SERVICE_NAME = 'xapi';

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    protected $_registeredPackages = array(
            'Zend_Gdata_Kind',
            'Zend_Gdata_Extension',
            'Zend_Gdata',
            'Zend_Gdata_App_Extension',
            'Zend_Gdata_App');

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     */
    public function __construct($client = null)
    {
        parent::__construct($client);
    }

    /**
     * Adds a Zend Framework package to the $_registeredPackages array.
     * This array is searched when using the magic __call method below
     * to instantiante new objects.
     *
     * @param string $name The name of the package (eg Zend_Gdata_App)
     */
    public function registerPackage($name) 
    {
        array_unshift($this->_registeredPackages, $name);
    }

    /**
     * Provides a magic factory method to instantiate new objects with
     * shorter syntax than would otherwise be required by the Zend Framework
     * naming conventions.  For instance, to construct a new 
     * Zend_Gdata_Calendar_Extension_Color, a developer simply needs to do
     * $gCal->newColor().  For this magic constructor, packages are searched
     * in the same order as which they appear in the $_registeredPackages
     * array
     *
     * @param string $method The method name being called
     * @param array $args The arguments passed to the call
     * @throws Zend_Gdata_App_Exception
     */
    public function __call($method, $args) 
    {
        if (substr($method, 0, 3) == 'new') {
            $class = substr($method, 3);
            if ($class === FALSE) {
                throw new Zend_Gdata_App_Exception(
                        'Class name not provided');
            }
            $foundClassName = null;
            // TODO Performance can probably be improved here
            foreach ($this->_registeredPackages as $name) {
                 try {
                     Zend_Loader::loadClass("${name}_${class}");
                     $foundClassName = "${name}_${class}";
                     break;
                 } catch (Zend_Exception $e) {
                     // package wasn't here- continue searching
                 }
            }
            if ($foundClassName != null) {
                $first = TRUE;
                $argString = '';
                for ($i = 0; $i < count($args); $i++) {
                    if (! $first) {
                        $argString .= ', ';
                    }
                    $argString .= "\$args[${i}]";
                    $first = FALSE;
                }
                eval("\$returnVal = new ${foundClassName}(${argString});");
                return $returnVal; 
                //$reflectionObj = new ReflectionClass($foundClassName);
                //return $reflectionObj->newInstanceArgs($args);
            } else {
                throw new Zend_Gdata_App_Exception(
                        "Unable to find '${class}' in registered packages");
            }
        } else {
           throw new Zend_Gdata_App_Exception("No such method ${method}");
        }
    }

    /**
     * Retreive feed object
     *
     * @param (string|Zend_Gdata_Query) $location
     * @return Zend_Gdata_Feed
     */     
    public function getFeed($location, $className='Zend_Gdata_Feed')
    {   
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            throw new Zend_Gdata_InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend_Gdata_Query');
        }
        return parent::getFeed($uri, $className);
    }

    /**
     * Retreive entry object
     *
     * @param (string) $location
     * @return Zend_Gdata_Feed
     */     
    public function getEntry($location, $className='Zend_Gdata_Entry')
    {   
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            throw new Zend_Gdata_InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend_Gdata_Query');
        }
        return parent::getEntry($uri, $className);
    }

}
