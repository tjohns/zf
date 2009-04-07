Welcome to the Zend Framework 1.8 Preview Release! This release showcases features
that will be introduced in the final 1.8 release. The intent of this release is to
give our users access to the latest features in Zend Framework and to improve the
overall quality of the production release through the feedback we recieve. In
particular, *your* feedback will help make the Zend Framework 1.8 production release
a success, so please post your comments and questions to the appropriate mailing list
and bring bugs to our attention by creating an issue in our issue tracker:

http://framework.zend.com/issues

Please note that this release contains a pre-production version of Zend_Tool. You can
use the command line client by running the appropriate script in the bin directory. 

RELEASE INFORMATION
---------------
Zend Framework 1.8 Preview Release (revision [INSERT REV NUM HERE]). Released on
April 7, 2009.

NEW FEATURES
------------

* Zend_Tool, contributed by Ralph Schindler
* Zend_Application, contributed by Ben Scholzen and Matthew Weier O'Phinney
* Zend_Navigation, contributed by Robin Skoglund
* Zend_CodeGenerator, by Ralph Schindler
* Zend_Reflection, Ralph Schindler and Matthew Weier O'Phinney
* Zend Server backend for Zend_Cache, contributed by Alexander Veremyev
* Zend_Service_Amazon_Ec2, contributed by Jon Whitcraft
* Zend_Service_Amazon_S3, Justin Plock and Stas Malyshev
* Zend_Filter_Encrypt, contributed by Thomas Weidner
* Zend_Filter_Decrypt, contributed by Thomas Weidner
* Support for file upload progress support in Zend_File_Transfer,
    contributed by Thomas Weidner
* Translation-aware routes, contributed by Ben Scholzen
* Zend_Json expression support, contributed by Benjamin Eberlei
* Zend_Http_Client_Adapter_Curl, contributed by Benjamin Eberlei
* SOAP input and output header support, contributed by Alexander Veremyev
* Support for keyword field search using query strings,
    contributed by Alexander Veremyev
* Support for searching across multiple indexes in Zend_Search_Lucene,
    contributed by Alexander Veremyev
* Support for page scaling, shifting and skewing in Zend_Pdf,
    contributed by Alexander Veremyev
* Locale support in Zend_Validate_Int and Zend_Validate_Float,
    contributed by Thomas Weidner
* Phonecode support in Zend_Locale, contributed by Thomas Weidner
* Zend_Validate_Iban, contributed by Thomas Weidner
* Zend_Validate_File_WordCount, contributed by Thomas Weidner

A detailed list of all features and bug fixes in this release may be found at:

http://framework.zend.com/issues/secure/IssueNavigator.jspa?requestId=11002

INTENDED USE
------------

The code in this release provides a 'sneak peek' at features to be released in the
upcoming Zend Framework 1.8 release and is not intended for production use. Please be
aware that the API's introduced in this release may not be final, and that the Zend
Framework team does not guarantee backwards compatibility to this release in future
releases of Zend Framework. We strongly discourage the use of this preview
release in production environments or for projects that could not easily be
refactored to work with future releases of Zend Framework. We recommend
evaluating these features for use in future projects, however, and would appreciate
any feedback.

SYSTEM REQUIREMENTS
-------------------

Zend Framework requires PHP 5.2.4 or later. Please see our reference guide for more
detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see /INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual. Questions that
are not addressed in the manual should be directed to the appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or contrary to its
documented behavior, please create an issue in the Zend Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases- including the production release of
Zend Framework 1.8- you can subscribe to the fw-announce mailing list by sending a
blank message to fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license. You can find
a copy of this license in /LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you- the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.