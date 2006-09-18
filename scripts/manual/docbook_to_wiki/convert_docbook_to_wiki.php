<?php

require_once 'libs/Conversion.php';

$path = '../../../documentation/manual/en/module_specs/';

$conversion = new Conversion();
echo $conversion->convert($path . 'Zend_Service_Amazon.xml', 'xsl/wiki.xsl');

?>