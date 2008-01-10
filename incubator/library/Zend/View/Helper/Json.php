<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Json */
require_once 'Zend/Json.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * Helper for simplifying JSON responses
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_Json
{
    /**
     * Suppress exit functionality (used in tests)
     * @var bool
     */
    public $suppressExit = false;

    /**
     * Encode data as JSON an set response header
     *
     * If $exitNow is true, 
     * 
     * @param  mixed $data 
     * @param  bool $exitNow 
     * @return string|void
     */
    public function json($data, $exitNow = true)
    {
        $data = Zend_Json::encode($data);
        $response = Zend_Controller_Front::getInstance()->getResponse();
        $response->setHeader('Content-Type', 'application/json');
        if ($exitNow) {
            $response->setBody($data);
            if (!$this->suppressExit) {
                $response->sendResponse();
                exit;
            }
            return;
        }

        return $data;
    }
}
