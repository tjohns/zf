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
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_App_FeedEntryParent
 */
require_once 'Zend/Gdata/App/FeedEntryParent.php';

/**
 * Concrete class for working with Atom entries.
 *
 * @category   Zend
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App_Entry extends Zend_Gdata_App_FeedEntryParent
{

    /**
     * Root XML element for Atom entries.
     *
     * @var string
     */
    protected $_rootElement = 'entry';

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Uploads changes in this entry to the server using the referenced 
     * Zend_Http_Client to do a HTTP PUT to the edit link stored in this 
     * entry's link collection.  Body for the PUT is generated using the
     * saveXML method (which calls getDOM).
     */
    public function save()
    {
        if ($this->id) {
            // If id is set, look for link rel="edit" in the
            // entry object and PUT.
            $editLink = $this->getLink('edit');
            $editUri = $editLink->href;
            if (!$editUri) {
                throw new Zend_Gdata_App_Exception('Cannot edit entry; no link rel="edit" is present.');
            }
            $client = $this->getHttpClient();
            if (is_null($client)) {
                $client = Zend_Gdata_App::getStaticHttpClient();
            }
            $client->resetParameters();
            $client->setUri($editUri);
            if (Zend_Gdata_App::getHttpMethodOverride()) {
                $client->setHeaders(array('X-HTTP-Method-Override: PUT',
                    'Content-Type: application/atom+xml'));
                $client->setRawData($this->saveXML());
                $response = $client->request('POST');
            } else {
                $client->setHeaders('Content-Type', 'application/atom+xml');
                $client->setRawData($this->saveXML());
                $response = $client->request('PUT');
            }
            if ($response->getStatus() !== 200) {
                throw new Zend_Gdata_App_Exception('Expected response code 200, got ' . $response->getStatus());
            }

            // Update internal properties using $client->responseBody;
            return new $this->_entryClassName(null, $response->getBody());
        } else {
            throw new Zend_Gdata_App_Exception('Cannot edit entry; no id is present');
        }
    }

    /**
     * Deletes this entry to the server using the referenced 
     * Zend_Http_Client to do a HTTP DELETE to the edit link stored in this 
     * entry's link collection.  
     */
    public function delete()
    {
        if ($this->id) {
            // If id is set, look for link rel="edit" in the
            // entry object and DELETE.
            $editLink = $this->getLink('edit');
            $editUri = $editLink->href;
            if (!$editUri) {
                throw new Zend_Gdata_App_Exception('Cannot delete entry; no link rel="edit" is present.');
            }
            $client = $this->getHttpClient();
            if (is_null($client)) {
                $client = Zend_Gdata_App::getStaticHttpClient();
            }
            $client->resetParameters();
            $client->setUri($editUri);
            $client->setHeaders('Content-Type', null);
            $client->setRawData('');
            if (Zend_Gdata_App::getHttpMethodOverride()) {
                $client->setHeaders('X-HTTP-Method-Override', 'DELETE');
                $response = $client->request('DELETE');
            } else {
                $response = $client->request('DELETE');
            }
            if ($response->getStatus() !== 200) {
                throw new Zend_Gdata_App_Exception('Expected response code 200, got ' . $response->getStatus());
            }
            return true;
        } else {
            throw new Zend_Gdata_App_Exception('Cannot edit entry; no id is present');
        }
    }

}
