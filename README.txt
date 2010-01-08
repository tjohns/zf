Welcome to the Zend Framework 1.8 Release! 

RELEASE INFORMATION
---------------
Zend Framework 1.8.5 Release ([INSERT REV NUM HERE]).
Released on January 11, 2010.

For detailed changes, please see:

    http://framework.zend.com/changelog/1.8.5

As part of this release, we have also begun publishing roadmaps for upcoming
minor versions of Zend Framework. You may view these here:
    
    http://framework.zend.com/roadmap

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

Zend_File_Transfer had a potential MIME type injection vulnerability for
file uploads. In certain situations where either PHP's ext/finfo
extension is not installed and the mime_content_type() function was not
available on a system, Zend_File_Transfer would use the user provided
value for the type embedded inside the $_FILES superglobal.
Additionally, in cases where the functionality was available, but where
a type could not be determined by one of them, Zend_File_Transfer would
also fallback on the user provided type.  Using user provided
information for a file's MIME type in uploads is considered an insecure
practice, as it provides attack vectors by malicious users. This
vulnerability has been fixed in this release branch by returning
"application/octet" in situations where the MIME type cannot be detected
securely by PHP. If you use this component, or other components that
rely on it (e.g., Zend_Form_Element_File), we strongly recommend
upgrading to this version or the most current version of Zend Framework
available.

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

NEW FEATURES in 1.8
---------------------

* Zend_Tool, contributed by Ralph Schindler
* Zend_Application, contributed by Ben Scholzen and Matthew Weier O'Phinney
* Zend_Loader_Autoloader and Zend_Loader_Autoloader_Resource,
    contributed by Matthew Weier O'Phinney
* Zend_Navigation, contributed by Robin Skoglund
* Zend_CodeGenerator, by Ralph Schindler
* Zend_Reflection, Ralph Schindler and Matthew Weier O'Phinney
* Zend Server backend for Zend_Cache, contributed by Alexander Veremyev
* Zend_Service_Amazon_Ec2, contributed by Jon Whitcraft
* Zend_Service_Amazon_S3, Justin Plock and Stas Malyshev
* Zend_Filter_Encrypt, contributed by Thomas Weidner
* Zend_Filter_Decrypt, contributed by Thomas Weidner
* Zend_Filter_LocalizedToNormalized and _NormalizedToLocalized,
    contributed by Thomas Weidner
* Support for file upload progress support in Zend_File_Transfer,
    contributed by Thomas Weidner
* Translation-aware routes, contributed by Ben Scholzen
* Zend_Json expression support, contributed by Benjamin Eberlei and
    Oscar Reales
* Zend_Http_Client_Adapter_Curl, contributed by Benjamin Eberlei
* SOAP input and output header support, contributed by Alexander Veremyev
* Support for keyword field search using query strings,
    contributed by Alexander Veremyev
* Support for searching across multiple indexes in Zend_Search_Lucene,
    contributed by Alexander Veremyev
* Significant improvements for Zend_Search_Lucene search result match
    highlighting capabilities, contributed by Alexander Veremyev
* Support for page scaling, shifting and skewing in Zend_Pdf,
    contributed by Alexander Veremyev
* Zend_Tag_Cloud, contributed by Ben Scholzen
* Locale support in Zend_Validate_Int and Zend_Validate_Float,
    contributed by Thomas Weidner
* Phonecode support in Zend_Locale, contributed by Thomas Weidner
* Zend_Validate_Db_RecordExists and _NoRecordExists, contributed by
    Ryan Mauger
* Zend_Validate_Iban, contributed by Thomas Weidner
* Zend_Validate_File_WordCount, contributed by Thomas Weidner

A detailed list of all features and bug fixes in this release may be found at:

http://framework.zend.com/issues/secure/IssueNavigator.jspa?requestId=11050

SYSTEM REQUIREMENTS
-------------------

Zend Framework requires PHP 5.2.4 or later. Please see our reference
guide for more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
