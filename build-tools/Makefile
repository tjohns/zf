#
# Zend Framework
#
# LICENSE
#
# This source file is subject to the new BSD license that is bundled
# with this package in the file LICENSE.txt.
# It is also available through the world-wide-web at this URL:
# http://framework.zend.com/license/new-bsd
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@zend.com so we can send you a copy immediately.
#
# @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
# @license    http://framework.zend.com/license/new-bsd     New BSD License
# @version    $Id$

#
# Makefile - build Zend Framework product and documentation
#
# Main targets:
#  dist - produce zip and tarball.
#  zip - produce zip from staged tree.
#  tar - produce tarball from staged tree.
#  stage - produce staged product tree.
#  export - get files from subversion.
#  docbook - build and stage manual from docbook sources.
#  phpdoc - build and stage API doc using phpDocumentor.
#  clean - remove staged files.
#  clean-docbook - remove temp files under docbook sources.
#  clean-export - remove exported product tree.
#

# -- parameters you are likely to want to change --

# PHPDOC is the command to run to run phpDocumentor.  This can simply
# be 'phpdoc' if phpDocumentor is in your PATH.  On Cygwin, you can
# run 'php' with the phpdoc script as the argument, in Windows-style
# path format; e.g., "C:/php/phpdoc".
PHPDOC=phpdoc

# ZF_SVN_REVISION is the Subversion revision number used for exporting
# the release files. ZF_SVN_DIR is the path to the directory in SVN
# from which the release should be created, relative to the repository
# location (e.g., trunk, branch/release-1.0).
ZF_SVN_REVISION=8311
ZF_SVN_DIR=branch/release-1.0

# ZF_VERSION is the release version string to build.  This is added
# to the VERSION.txt file, and is also used for the name of the
# staging directory and the names of the zip and tarball files.
ZF_VERSION=1.0.4

# For ZF_LANG, use '*' if you want to build all manual translations.
# If you want to build one language, use one of the names of language
# directories (e.g., 'en', 'de').  You also can use any file-globbing
# pattern.
ZF_LANG=xx

# Locations of the DocBook DTD and XSL stylesheets
DTD_HOST=framework.zend.com
DTD_URL=http://$(DTD_HOST)/docbook/xml/4.5/docbookx.dtd
XSL_HOST=framework.zend.com
XSL_URL=http://$(XSL_HOST)/docbook-xsl/html/chunk.xsl

# -- end of parameters you are likely to want to change --

SVN=svn
SVN_USER=
SVN_PASS=

ZF_SVN_URL=http://framework.zend.com/svn/framework/$(ZF_SVN_DIR)
ZF_PRODUCT_NAME=Zend Framework
ZF_PRODUCTNAME=ZendFramework
ZF_PRODUCT=$(ZF_PRODUCTNAME)-$(ZF_VERSION)
ZF_EXPORT_DIR=./$(ZF_PRODUCTNAME)-export-$(ZF_SVN_REVISION)
STAGE_HOME=stage
ZF_STAGE_DIR=$(STAGE_HOME)/$(ZF_PRODUCT)

ZIP=zip
ZF_ZIPBALL=$(ZF_PRODUCT).zip
TAR=tar
ZF_TARBALL=$(ZF_PRODUCT).tar.gz

API_DOC_TITLE_CORE=Zend Framework API Documentation
API_DOC_TITLE_INCUBATOR=Zend Framework Incubator API Documentation
API_DOC_OUTPUT_STYLE=HTML:frames:DOM/earthli

.PHONY: clean clean-docbook clean-export

dist: export stage zip tar

export:
	@echo "== EXPORTING SVN FILES =="
	@if [ -f "$(ZF_EXPORT_DIR)/VERSION.txt" ] ; then \
	    echo "Found files in '$(ZF_EXPORT_DIR)'" ; \
	else \
	    $(SVN) export -r $(ZF_SVN_REVISION) --force --config-dir ~/.subversion $(ZF_SVN_URL) $(ZF_EXPORT_DIR) ; \
	fi

stage: stage-files stage-docs stage-version

stage-files:
	@echo "== STAGING FILES =="
	-@mkdir -p $(ZF_STAGE_DIR)
	@(pwd=`pwd` ; cd $(ZF_EXPORT_DIR) && rsync --archive --delete --exclude-from="../exclude-files" ./ ../$(ZF_STAGE_DIR))

stage-docs: docbook phpdoc

docbook: docbook-core docbook-incubator

docbook-core:
	-@mkdir -p $(ZF_STAGE_DIR)/documentation/manual/core
	@for language in $(ZF_EXPORT_DIR)/documentation/manual/$(ZF_LANG)/ ; do \
	    [ -d $$language ] || continue ; \
	    lang=`basename $$language` ; \
	    echo "== BUILDING AND STAGING END-USER DOCUMENTATION [CORE] (language: $$lang) ==" ; \
	    ( cd $$language && autoconf && sh ./configure && $(MAKE) -e 2>&1 | tee err.txt ) ; \
	    rsync --archive --delete --exclude=.svn $$language/html/ $(ZF_STAGE_DIR)/documentation/manual/core/$$lang ; \
	done

docbook-incubator:
	-@mkdir -p $(ZF_STAGE_DIR)/documentation/manual/incubator
	@for language in $(ZF_EXPORT_DIR)/incubator/documentation/manual/$(ZF_LANG)/ ; do \
	    [ -d $$language ] || continue ; \
	    lang=`basename $$language` ; \
	    echo "== BUILDING AND STAGING END-USER DOCUMENTATION [INCUBATOR] (language: $$lang) ==" ; \
	    ( cd $$language ; autoconf ; sh ./configure ; $(MAKE) -e 2>&1 | tee err.txt ) ; \
	    rsync --archive --delete --exclude=.svn $$language/html/ $(ZF_STAGE_DIR)/documentation/manual/incubator/$$lang ; \
	done

phpdoc: phpdoc-core phpdoc-incubator

phpdoc-core:
	@echo "== BUILDING AND STAGING API DOCUMENTATION (CORE) =="
	-@mkdir -p $(ZF_STAGE_DIR)/documentation/api/core
	if [ ! -f $(ZF_STAGE_DIR)/documentation/api/core/index.html -o `find $(ZF_STAGE_DIR)/library -newer $(ZF_STAGE_DIR)/documentation/api/core/index.html 2>/dev/null | wc -l` -gt 0 ] ; then \
	    $(PHPDOC) --quiet off \
	      --target $(ZF_STAGE_DIR)/documentation/api/core \
	      --directory $(ZF_STAGE_DIR)/library \
	      --title "$(API_DOC_TITLE_CORE)" \
	      --output $(API_DOC_OUTPUT_STYLE) ; \
	else \
	    echo "...core API documentation is up to date." ; \
	fi

phpdoc-incubator:
	@echo "== BUILDING AND STAGING API DOCUMENTATION (INCUBATOR) =="
	-@mkdir -p $(ZF_STAGE_DIR)/documentation/api/incubator
	if [ ! -f $(ZF_STAGE_DIR)/documentation/api/incubator/index.html -o `find $(ZF_STAGE_DIR)/incubator/library -newer $(ZF_STAGE_DIR)/documentation/api/incubator/index.html 2>/dev/null | wc -l` -gt 0 ] ; then \
	    $(PHPDOC) --quiet off \
	      --target $(ZF_STAGE_DIR)/documentation/api/incubator \
	      --directory $(ZF_STAGE_DIR)/incubator/library \
	      --title "$(API_DOC_TITLE_INCUBATOR)" \
	      --output $(API_DOC_OUTPUT_STYLE) ; \
	else \
	    echo "...incubator API documentation is up to date." ; \
	fi

stage-version:
	@echo "== OVERWRITING VERSION.txt =="
	@echo "$(ZF_PRODUCT_NAME) Release $(ZF_VERSION) (revision $(ZF_SVN_REVISION))" > $(ZF_STAGE_DIR)/VERSION.txt

zip:
	@echo "== CREATING ZIP ARCHIVES =="
	@( cd $(STAGE_HOME) && $(ZIP) -rq $(ZF_ZIPBALL) $(ZF_PRODUCT) -x $(ZF_PRODUCT)/documentation/\* )
	@echo "Code archive done..."
	@( cd $(STAGE_HOME) && $(ZIP) -rq $(ZF_PRODUCT)-apidoc.zip \
	    $(ZF_PRODUCT)/documentation/api )
	@echo "Documentation archive for API doc done..."
	@( cd $(STAGE_HOME) && $(ZIP) -rq $(ZF_PRODUCT)-manual-en.zip \
	    $(ZF_PRODUCT)/documentation/manual/core/en )
	@echo "Documentation archive for manual language 'en' done..."
	@for language in `ls -1d $(STAGE_HOME)/$(ZF_PRODUCT)/documentation/manual/core/* | grep -v '/en$$'` ; do \
	    lang=`basename $$language` ; \
	    ( cd $(STAGE_HOME) && $(ZIP) -rq $(ZF_PRODUCT)-manual-$$lang.zip \
		$(ZF_PRODUCT)/documentation/manual/core/$$lang ) ; \
	    echo "Documentation archive for manual language '$$lang' done..." ; \
	done
	@echo "Done."

tar:
	@echo "== CREATING TAR.GZ ARCHIVES =="
	@( cd $(STAGE_HOME) && $(TAR) -czf $(ZF_TARBALL) --exclude=$(ZF_PRODUCT)/documentation $(ZF_PRODUCT) )
	@echo "Code archive done..."
	@( cd $(STAGE_HOME) && $(TAR) -czf $(ZF_PRODUCT)-apidoc.tar.gz \
	    $(ZF_PRODUCT)/documentation/api )
	@echo "Documentation archive for API doc done..."
	@( cd $(STAGE_HOME) && $(TAR) -czf $(ZF_PRODUCT)-manual-en.tar.gz \
	    $(ZF_PRODUCT)/documentation/manual/core/en )
	@echo "Documentation archive for manual language 'en' done..."
	@for language in `ls -1d $(STAGE_HOME)/$(ZF_PRODUCT)/documentation/manual/core/* | grep -v '/en$$'` ; do \
	    lang=`basename $$language` ; \
	    ( cd $(STAGE_HOME) && $(TAR) -czf $(ZF_PRODUCT)-manual-$$lang.tar.gz \
		$(ZF_PRODUCT)/documentation/manual/core/$$lang ) ; \
	    echo "Documentation archive for manual language '$$lang' done..." ; \
	done
	@echo "Done."

clean:
	-rm -rf $(ZF_STAGE_DIR)
	-rm -f $(STAGE_HOME)/$(ZF_PRODUCT)*.zip
	-rm -f $(STAGE_HOME)/$(ZF_PRODUCT)*.tar.gz

clean-docbook:
	for language in $(ZF_EXPORT_DIR)/documentation/manual/$(ZF_LANG) $(ZF_EXPORT_DIR)/incubator/documentation/manual/$(ZF_LANG) ; do \
	    rm -rf $$language/autom4te.cache $$language/build/docbook-xsl $$language/html/*.html $$language/html/HTML.manifest $$language/config.* $$language/configure $$language/entities.ent $$language/Makefile ; \
	done

clean-export:
	-rm -rf $(ZF_EXPORT_DIR)
