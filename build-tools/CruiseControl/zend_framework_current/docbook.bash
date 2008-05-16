#!/bin/bash

LANGUAGES="de en fr ja ru zh"

export DOCBOOK_DTD=/usr/share/docbook/xml/4.5/docbookx.dtd
export DOCBOOK_XSL=/usr/share/docbook-xsl/htmlhelp/htmlhelp.xsl

for lang in $LANGUAGES ; do
    src=source/documentation/manual/$lang
    dest=build/documentation/manual/$lang
    ( cd $src && autoconf && sh ./configure && make -e 2>&1 | tee err.txt )
    rsync --archive --delete $src/html/ $dest
done
