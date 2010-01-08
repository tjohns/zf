Welcome to Zend Framework 1.7.9! This is a security release in the 
Zend Framework 1.7 series. 

RELEASE INFORMATION
---------------

Zend Framework 1.7.9 ([INSERT REV NUM HERE]).
Released on 2010-01-11.

SECURITY ADVISORIES
-------------------
This release contains a number of security fixes that may affect you.
Please read the following advisories to determine whether you are
affected; if so, please upgrade immediately.

We would like to thank Pádraic Brady for doing a preliminary security
audit of the framework and uncovering the issues reported below; he has
worked closely with the Zend Framework team during the weeks prior to
the release to report the issues as well as to assist in patching the
framework to resolve them.

Vulnerabilities reported and fixed with this version include:

Zend_Dojo_View_Helper_Editor was incorrectly decorating a TEXTAREA
instead of a DIV. The Dojo team has reported that this has security
implications as the rich text editor they use is unable to escape
content for a TEXTAREA. The primary rationale in ZF for using a TEXTAREA
was to allow for graceful degradation in browser environments that do
not support JavaScript. The component has been reworked such that we now
decorate an HTML DIV, and then provide a separate TEXTAREA within a
NOSCRIPT tag. If you use Zend_Dojo_View_Helper_Editor, it is strongly
recommended that you upgrade to this release or the latest available
Zend Framework release immediately.

Zend_Filter_StripTags contained an optional setting to allow
whitelisting HTML comments in filtered text. Microsoft Internet Explorer
and several other browsers allow developers to create conditional
functionality via HTML comments, including execution of script events
and rendering of additional commented markup. By allowing whitelisting
of HTML comments, a malicious user could potentially include XSS
exploits within HTML comments that would then be rendered in the final
output. The Zend Framework team has determined that since this
vulnerability is so trivial to exploit, the functionality to allow
whitelisting comments will now be disabled in this and all future
releases.  Additionally, the regular expression for stripping comments
has been bolstered to properly remove comments containing HTML tags,
nested comments, and comments ending with whitespace between the "--"
and ending delimiter (">"). If you use this filter and were enabling the
"allowComments" functionality, be advised that it is now silently
ignored. We also recommend such users to upgrade to this release or the
latest available Zend Framework release immediately.

Zend_Service_ReCaptcha_MailHide had a potential XSS vulnerability. Due
to the fact that the email address was never validated, and because its
use of htmlentities() did not include the encoding argument, it was
potentially possible for a malicious user aware of the issue to inject a
specially crafted multibyte string as an attack via the CAPTCHA's email
argument. If you use this service, we recommend upgrading to this
release or the latest available Zend Framework release immediately.

Zend_Json_Encoder was not taking into account the solidus character
("/") during encoding, leading to incompatibilities with the JSON
specification, and opening the potential for XSS or HTML injection
attacks when returning HTML within a JSON string. This particular
vulnerability only affects those users who are either (a) using
Zend_Json_Encoder directly, (b) requesting native encoding instead of
usage of ext/json (e.g., by enabling the static
$useBuiltinEncoderDecoder property of Zend_Json), or (c) on systems
where ext/json is unavailable (e.g. RHEL, CentOS). If you are affected,
we strongly recommend upgrading to this release or the latest available
Zend Framework release immediately.

SPECIAL NOTICE
--------------

Since it implements a binary protocol, the Zend_Amf component must be
aware of the processor architecture on which it is executed. Currently
Zend_Amf has been tested on machines with 32- and 64-bit Intel-based
processors. Specifically, it has not been tested on Power (i5),
PowerPC, and Sparc processors. Zend_Amf may work with these
architectures, but is not currently supported. We intend to add support
for additional architectures in upcoming mini releases.

NEW FEATURES
------------

* Zend_Amf with support for AMF0 and AMF3 protocols
* Dojo Toolkit 1.2.1
* Support for dijit editor available in the Dojo Toolkit
* ZendX_JQuery in extras library (see extras folder in the full package)
* Metadata API in Zend_Cache
* Google book search API in Zend_Gdata
* Preliminary support for GData Protocol v2 in Zend_Gdata
* Support for skip data processing in Zend_Search_Lucene
* Support for Open Office XML documents in Zend_Search_Lucene indexer
* Performance enhancements in Zend_Loader, Zend_Controller, and server
  components
* Zend_Mail_Storage_Writable_Maildir enhancements for mail delivery
* Zend_Tool in incubator (see incubator folder in the full package)
* Zend_Text_Table for formatting table using characters
* Zend_ProgressBar
* Zend_Config_Writer
* ZendX_Console_Unix_Process in the extras library
* Zend_Db_Table_Select support for Zend_Paginator
* Global parameters for routes
* Using Chain-Routes for Hostname-Routes via Zend_Config
* I18N improvements
    - Application wide locale for all classes
    - Data retrieving methods are now static
    - Additional cache handling methods in all I18N classes
    - Zend_Translate API simplified
* File transfer enhancements
    - Support for file elements in subforms
    - Support for multifile elements
    - Support for MAX_FILES_SIZE in form
    - Support for breaking validation chain
    - Support for translation of failure ,messages
    - New IsCompressed, IsImage, ExcludeMimeType, ExcludeExtension validators
    - Support for FileInfo extension in MimeType validator
* Zend_Db_Table_Select adapater for Zend_Paginator
* Support for custom adapters in Zend_Paginator
* More flexible handling of complex types in Zend_Soap

A detailed list of all features and bug fixes in this release may be found at:

http://framework.zend.com/issues/secure/IssueNavigator.jspa?requestId=10990

SYSTEM REQUIREMENTS
-------------------

Zend recommends the most current release of PHP for critical security and
performance enhancements, and currently supports PHP 5.2.4 or later.
Please see our reference guide for more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual. Questions
that are not addressed in the manual should be directed to the appropriate
mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or contrary to
its documented behavior, please create an issue in the Zend Framework issue
tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases- including further
maintenance releases of Zend Framework 1.7- you can subscribe to the fw-announce
mailing list by sending a blank message to fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license. You can
find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor (Zend Technologies), and you- the Zend
Framework user. Please visit us sometime soon at http://framework.zend.com!

