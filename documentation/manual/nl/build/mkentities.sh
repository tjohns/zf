#!/bin/sh

# this is needed to get the sorting to work correctly on broken RH systems
LANG=C
export LANG

echo "<!-- auto-generated -->"
for dir in `find $* -type d | grep -v \.svn | grep -v CVS` ; do
  echo "<!-- dir: $dir -->"
  all=""
  for item in `find $dir -maxdepth 1 -type f | grep -v \.svn | sort` ; do
    case $item in
      .svn*)
      ;;
      *.xml)
        ent=`echo $item | sed -e s/\.xml// -e s#^\./docbook/## -e s#/#.#g`
        echo "<!-- $item -->"
        echo "<!ENTITY $ent SYSTEM \"$item\">"
        all="$all&$ent;"
      ;;
    esac
  done
  
  ent=`echo $dir | sed -e s/\.xml// -e s#^\./docbook## -e s#^/## -e s#/#.#g`
  if test -n "$ent" ; then
    echo "<!-- $dir -->"
    echo "<!ENTITY $ent.all \"$all\">"
  fi
done

# vim:se ts=2 sw=2 et:
