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
 * @category   ZendL
 * @package    ZendL_Toolbar
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @see Zend_Wildfire_Plugin_FrameworkToolbar
 */
require_once 'Zend/Wildfire/Plugin/FrameworkToolbar.php';


/**
 * Toolbar plugin
 *
 * @category   ZendL
 * @package    ZendL_Toolbar
 * @uses       Zend_Controller_Plugin_Abstract
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendL_Toolbar_Plugin extends Zend_Controller_Plugin_Abstract
{
    /**
     * Inject the debug information at the end of the request
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
//        $response = Zend_Controller_Front::getInstance()->getResponse();
//        $response->setHeader('X-ZF-Enabled', 'true');

//        require_once 'Zend/Json.php';
        
        $data = array('foo'       => 'bar',
                      'lastQuery' => 'SELECT * FROM `table` WHERE 1',
                      'longStuff' => str_repeat('.', 5000),
                      'log'       => "Foo\nbar\r\nBaz");

/*        
        $json   = explode("\n", chunk_split(Zend_Json::encode($data), 4096, "\n"));
        $length = count($json);
        
        foreach ($json as $num => $line) {
            $response->setHeader('X-ZF-Data-' . $num, $line);
        }
*/        
        
        // Send data via wildfire
        Zend_Wildfire_Plugin_FrameworkToolbar::getInstance()->send($data);
    }
}

