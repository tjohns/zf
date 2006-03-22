<?php
/**
 * Connect to the O'Reilley "Meerkay" server and list all of the
 * categories available.
 */

require_once 'Zend/XmlRpc/Client.php';

$meerkat = new Zend_XmlRpc_Client('http://www.oreillynet.com/meerkat/xml-rpc/server.php', 'meerkat');

foreach ($meerkat->getCategories() as $category) {
    echo $category['title'] . " <br />\n";
}

