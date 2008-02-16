#!/bin/bash

#############################################################################
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
# Zend
# Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
# http://framework.zend.com/license/new-bsd     New BSD License
#############################################################################
PHP_BIN='php'

# Use current dir as script dir
SCRIPT_DIR=`pwd`

# Script name
ZF_SCRIPT=$SCRIPT_DIR/zf.php

# Insert the name of this script as the first argument
$PHP_BIN -d safe_mode=Off -f $ZF_SCRIPT %0 %*