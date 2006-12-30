To run the unit tests for Zend_Session*:

$ cd zftrunk/incubator/tests/Zend/Session
$ php AllTests.php

Simulation of multiple, sequential requests required the use of exec() using the
CLI version of PHP.  Additionally, issues discussed on the headers_sent() manual
page also pose issues when trying to combine multiple test suites and avoid
problems associated with output buffering, and headers "already sent".

If you would like to help implement a solution, please start here:
http://framework.zend.com/issues/browse/ZF-700
