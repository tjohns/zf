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
# Make targets:
#  dist - build all release packages in one step.
#    export - get files from subversion.
#    stage - produce staged product tree in one step.
#      stage-files - copy files from export tree.
#      stage-docs - build and stage the DocBook and PHP API documentation in one step.
#        docbook - build and stage manual from DocBook sources.
#        phpdoc - build and stage API doc using phpDocumentor.
#      stage-version - add SVN revision number and release date to README.txt
#    zip - produce zip packages from staged tree.
#    tar - produce tar.gz packages from staged tree.
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
# location (e.g., tag/release-1.0.3)
ZF_SVN_REVISION=9012
ZF_SVN_DIR=tag/release-1.5.1

# ZF_VERSION is the release version string to build.  This is added
# to the VERSION.txt file, and is also used for the name of the
# staging directory and the names of the zip and tarball files.
ZF_VERSION=1.5.1

# For ZF_LANG, use '*' if you want to build all manual translations.
# If you want to build one language, use one of the names of language
# directories (e.g., 'en', 'de').  You also can use any file-globbing
# pattern.
ZF_LANG=de en fr ja ru zh

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

API_DOC_DEST=$(ZF_STAGE_DIR)/documentation/api/core
API_DOC_TITLE_CORE=Zend Framework API Documentation
API_DOC_TITLE_INCUBATOR=Zend Framework Incubator API Documentation
API_DOC_OUTPUT_STYLE=HTML:frames:DOM/earthli

.PHONY: clean clean-docbook clean-export

dist: export stage zip tar

export:
	@echo "Exporting files from SVN..."
	@if [ -d "$(ZF_EXPORT_DIR)" ] ; then \
	    echo "Export directory already exists ($(ZF_EXPORT_DIR))" ; \
	else \
	    $(SVN) export -r $(ZF_SVN_REVISION) --force --config-dir ~/.subversion $(ZF_SVN_URL) $(ZF_EXPORT_DIR) ; \
	fi
	@echo "...Done exporting files from SVN."

stage: stage-files stage-docs stage-version

stage-files:
	@echo "Staging files..."
	@if [ -d "$(ZF_STAGE_DIR)" ] ; then \
	    echo "Staging directory already exists ($(ZF_STAGE_DIR))" ; \
	else \
	    mkdir -p $(ZF_STAGE_DIR) ; \
	    (cd $(ZF_EXPORT_DIR) && rsync --archive --delete --exclude-from="../exclude-files" ./ ../$(ZF_STAGE_DIR)) ; \
	fi
	@echo "...Done staging files."

stage-docs: docbook phpdoc

docbook: docbook-core

docbook-core:
	@echo "Building and staging DocBook documentation..."
	-@mkdir -p $(ZF_STAGE_DIR)/documentation/manual/core
	@for lang in $(ZF_LANG) ; do \
	    src=$(ZF_EXPORT_DIR)/documentation/manual/$$lang ; \
	    dest=$(ZF_STAGE_DIR)/documentation/manual/core/$$lang ; \
	    echo "Building and staging DocBook documentation for language: $$lang..." ; \
	    ( cd $$src && autoconf && sh ./configure && $(MAKE) -e 2>&1 | tee err.txt ) ; \
	    rsync --archive --delete $$src/html/ $$dest ; \
	    echo "...Done building and staging DocBook documentation for language: $$lang." ; \
	done
	@echo "...Done building and staging DocBook documentation."

phpdoc: phpdoc-core

phpdoc-core:
	@echo "Building and staging PHP API documentation..."
	-@mkdir -p $(API_DOC_DEST)
	if [ ! -f $(API_DOC_DEST)/index.html -o `find $(ZF_STAGE_DIR)/library -newer $(API_DOC_DEST)/index.html 2>/dev/null | wc -l` -gt 0 ] ; then \
	    $(PHPDOC) --quiet off \
	      --target $(API_DOC_DEST) \
	      --directory $(ZF_STAGE_DIR)/library \
	      --title "$(API_DOC_TITLE_CORE)" \
	      --output $(API_DOC_OUTPUT_STYLE) ; \
	else \
	    echo "PHP API documentation is already staged and up to date." ; \
	fi
	@echo "...Done building and staging PHP API documentation."

stage-version:
	@echo "Adding SVN revision and release date to release notes..."
	@sed -e 's/\[INSERT REV NUMBER HERE\]/$(ZF_SVN_REVISION)/' \
	    < $(ZF_STAGE_DIR)/README.txt \
	    > $(ZF_STAGE_DIR)/README.txt.new
	@ZF_RELEASE_DATE=`date +%Y-%m-%d` ; sed -e "s/\[INSERT DATE HERE\]/$$ZF_RELEASE_DATE/" \
	    < $(ZF_STAGE_DIR)/README.txt.new \
	    > $(ZF_STAGE_DIR)/README.txt
	@rm $(ZF_STAGE_DIR)/README.txt.new
	@echo "...Done adding SVN revision and release date to release notes."

zip:
	@echo "Creating ZIP archives..."
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
	@echo "...Done creating ZIP archives."

tar:
	@echo "Creating TAR.GZ archives..."
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
	@echo "...Done creating TAR.GZ archives."

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
